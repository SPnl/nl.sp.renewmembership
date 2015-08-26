<?php

class CRM_Renewmembership_Form_RenewMembership extends CRM_Core_Form {

  protected $_membershipType;

  protected $_membershipStatus;

  function buildQuickForm() {
    foreach (CRM_Member_PseudoConstant::membershipType() as $id => $Name) {
      $this->_membershipType = $this->addElement('checkbox', "member_membership_type_id[$id]", NULL, $Name);
    }

    foreach (CRM_Member_PseudoConstant::membershipStatus(NULL, NULL, 'label') as $sId => $sName) {
      $this->_membershipStatus = $this->addElement('checkbox', "member_status_id[$sId]", NULL, $sName);
    }

    CRM_Core_Form_Date::buildDateRange($this, 'member_end_date', 1, '_low', '_high', ts('From'), FALSE);

    // add buttons
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Verleng lidmaatschappen'),
        'isDefault' => TRUE,
      ),
    ));
  }

  function postProcess() {
    $formValues = $this->exportValues();

    $fromRange = "member_end_date_low";
    $toRange = "member_end_date_high";
    $relative = 'member_end_date_relative';
    CRM_Contact_BAO_Query::fixDateValues($formValues[$relative], $formValues[$fromRange], $formValues[$toRange]);

    $selector = new CRM_Renewmembership_Selector();
    $selector->setData(array_keys($formValues['member_membership_type_id']), array_keys($formValues['member_status_id']), $formValues[$fromRange], $formValues[$toRange]);
    $original_where = $selector->getWhere();
    $selector->store();
    $where = $selector->getWhere();

    $count = CRM_Core_DAO::singleValueQuery("SELECT COUNT(*) FROM civicrm_membership ".$where);
    $this->assign('found', $count);

    if ($where == $original_where && isset($_POST['continue']) && !empty($_POST['continue'])) {
      $queue = CRM_Queue_Service::singleton()->create(array(
        'type' => 'Sql',
        'name' => 'nl.sp.renewmembership',
        'reset' => TRUE, //do not flush queue upon creation
      ));

      $limit = 10;
      for ($i = 0; $i <= $count; $i = $i + $limit) {
        $title = ts('Verleng lidmaatschappen %1 van %2', array(
          1 => $i,
          2 => $count,
        ));

        //create a task without parameters
        $task = new CRM_Queue_Task(
          array(
            'CRM_Renewmembership_Renew',
            'RenewFromQueue'
          ), //call back method
          array($limit), //parameters,
          $title
        );
        //now add this task to the queue
        $queue->createItem($task);
      }

      $runner = new CRM_Queue_Runner(array(
        'title' => ts('Verleng lidmaatschappen'), //title fo the queue
        'queue' => $queue, //the queue object
        'errorMode'=> CRM_Queue_Runner::ERROR_ABORT, //abort upon error and keep task in queue
        'onEnd' => array('CRM_Renewmembership_Form_RenewMembership', 'onEnd'), //method which is called as soon as the queue is finished
        'onEndUrl' => CRM_Utils_System::url('civicrm', 'reset=1'), //go to page after all tasks are finished
      ));

      $runner->runAllViaWeb(); // does not return
    }
  }

  static function onEnd(CRM_Queue_TaskContext $ctx) {
    //set a status message for the user
    CRM_Core_Session::setStatus('Lidmaatschappen verlengd', '', 'success');
  }


}