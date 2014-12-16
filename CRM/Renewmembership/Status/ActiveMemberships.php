<?php

class CRM_Renewmembership_Status_ActiveMemberships implements CRM_Renewmembership_Status_Config {
  
  protected $status;
  
  public function __construct() {
    $status = civicrm_api3('MembershipStatus', 'get', array('is_current_member' => 1));
    $this->status = $status['values'];
  }
  
  public function getStatusIds() {
    $return = array();
    foreach($this->status as $status) {
      $return[] = $status['id'];
    }
    return $return;
  }
  
}
