<?php

class CRM_Renewmembership_MembershipType_MembershipTypes implements CRM_Renewmembership_MembershipType_Config {
  
  protected $membership_types = array();
  
  public function __construct() {
    $membership_types = civicrm_api3('MembershipType', 'get', array());
    foreach($membership_types['values'] as $mtype) {
      $this->membership_types[$mtype['id']] = $mtype;
    }
  }
  
  public function getMembershipTypeIds() {
    $return = array();
    foreach($this->membership_types as $mtype_id => $mtype) {
      $return[] = $mtype_id;
    }
    return $return;
  }
  
  public function getNewEndDate(\DateTime $currentEndDate, $membership_type_id) {
    $endDate = clone $currentEndDate;
    if (isset($this->membership_types[$membership_type_id])) {
      switch ($this->membership_types[$membership_type_id]['duration_unit']) {
        case 'year':
          $interval = new DateInterval('P'.$this->membership_types[$membership_type_id]['duration_interval'].'Y');
          $endDate->add($interval);
          break;
        case 'month':
          $interval = new DateInterval('P'.$this->membership_types[$membership_type_id]['duration_interval'].'M');
          $endDate->add($interval);
          break;
        case 'day':
          $interval = new DateInterval('P'.$this->membership_types[$membership_type_id]['duration_interval'].'D');
          $endDate->add($interval);
          break;
      }
    }
    return $endDate;
  }
  
}

