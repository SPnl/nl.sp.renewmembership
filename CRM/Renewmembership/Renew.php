<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CRM_Renewmembership_Renew {
  
  /**
   *
   * @var CRM_Renewmembership_Status_Config
   */
  protected $status_config;
  
  /**
   *
   * @var CRM_Renewmembership_MembershipType_Config 
   */
  protected $type_config;

  public static function RenewFromQueue(CRM_Queue_TaskContext $ctx, $limit) {
    $type_config = new CRM_Renewmembership_MembershipType_MembershipTypes();
    $selector = new CRM_Renewmembership_Selector();
    $where = $selector->getWhere();
    $dao = CRM_Core_DAO::executeQuery("
      SELECT civicrm_membership.*
      FROM `civicrm_membership`
      ".$where."
      GROUP BY `civicrm_membership`.`id`
      LIMIT 0, ".$limit." FOR UPDATE");
    while($dao->fetch()) {
      $currentEndDate = new DateTime($dao->end_date);
      $newEndDate = $type_config->getNewEndDate($currentEndDate, $dao->membership_type_id);
      if ($newEndDate <= $currentEndDate) {
        continue; //end date is before current end date. So do not renew
      }
      self::renewMembership($dao->id, $newEndDate);
    }
    return true;
  }
  
  protected static function renewMembership($membership_id, DateTime $newEndDate) {
    $dao = CRM_Core_DAO::executeQuery("
      SELECT `c`.`id` AS `contribution_id`
FROM `civicrm_membership`
INNER JOIN `civicrm_membership_payment` `mp` ON `civicrm_membership`.`id` = `mp`.`membership_id`
INNER JOIN `civicrm_contribution` `c` ON `mp`.`contribution_id` = `c`.`id` AND c.receive_date <= civicrm_membership.end_date
WHERE civicrm_membership.id = %1
ORDER BY c.receive_date DESC
LIMIT 0, 1 FOR UPDATE", array(1=>array($membership_id, 'Integer')));

    //update membership end date
    $params['id'] = $membership_id;
    $params['end_date'] = $newEndDate->format('Ymd');
    $renewTransaction = new CRM_Core_Transaction();
    civicrm_api3('Membership', 'create', $params);

    if ($dao->fetch()) {
      $contribution = self::getRenewalPayment($dao->contribution_id);
      if ($contribution) {
        $result = civicrm_api3('Contribution', 'create', $contribution);

        $membershipPayment['contribution_id'] = $result['id'];
        $membershipPayment['membership_id'] = $membership_id;
        civicrm_api3('MembershipPayment', 'create', $membershipPayment);
      }
    }

    $renewTransaction->commit();
  }
  
  protected static function getRenewalPayment($contributionId) {
    if (!$contributionId) {
      return false;
    }
    
    try {
      $contribution = civicrm_api3('Contribution', 'getsingle', array('id' => $contributionId));
      $sql = "SELECT honor_contact_id, honor_type_id FROM civicrm_contribution WHERE id = %1";
      $dao = CRM_Core_DAO::executeQuery($sql, array( 1 => array($contribution['id'], 'Integer')));
      if ($dao->fetch() && $dao->honor_contact_id) {
        $contribution['honor_contact_id'] = $dao->honor_contact_id;
        $contribution['honor_type_id'] = $dao->honor_type_id;
      }
    } catch (Exception $ex) {
      return false;
    }
    
    $receiveDate = new DateTime();
    $contribution['receive_date'] = $receiveDate->format('YmdHis');
    $contribution['contribution_status_id'] = 2;//pending
    $instrument_id = self::getPaymenyInstrument($contribution);
    unset($contribution['payment_instrument']);
    unset($contribution['instrument_id']);
    if ($instrument_id) {
      $contribution['payment_instrument_id'] = $instrument_id;
    }
    unset($contribution['contribution_id']);
    unset($contribution['invoice_id']);
    unset($contribution['id']);
    return $contribution;
  }
  
  protected static function getPaymenyInstrument($contribution) {
    if (empty($contribution['instrument_id'])) {
      return false;
    }
    
    $instrument_id = CRM_Core_OptionGroup::getValue('payment_instrument', $contribution['instrument_id'], 'id', 'Integer');
    if (empty($instrument_id)) {
      return false;
    }
    return $instrument_id;
  }
  
}

