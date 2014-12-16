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
  
  public function __construct(CRM_Renewmembership_Status_Config $status_config, CRM_Renewmembership_MembershipType_Config $type_config) {
    $this->status_config = $status_config;
    $this->type_config = $type_config;
  }
  
  public function renew(DateTime $minEndDate, DateTime $maxEndDate, $limit) {
    $count = 0;
    $memberships = $this->findMemberships($minEndDate, $maxEndDate, $limit);
    while($memberships->fetch()) {
      $currentEndDate = new DateTime($memberships->end_date);
      $newEndDate = $this->type_config->getNewEndDate($currentEndDate, $memberships->membership_type_id);
      if ($newEndDate <= $currentEndDate) {
        continue; //end date is before current end date. So do not renew
      }
      
      $this->renewMembership($memberships->id, $newEndDate, $memberships->contribution_id);      
      $count ++;
    }
    return $count;
  }
  
  protected function renewMembership($membership_id, DateTime $newEndDate, $contributionId) {
    //update membership end date
    $params['id'] = $membership_id;
    $params['end_date'] = $newEndDate->format('Ymd');
    civicrm_api3('Membership', 'create', $params);
    
    $contribution = $this->getRenewalPayment($contributionId);
    if ($contribution) {
      $result = civicrm_api3('Contribution', 'create', $contribution);
      
      $membershipPayment['contribution_id'] = $result['id'];
      $membershipPayment['membership_id'] = $membership_id;
      civicrm_api3('MembershipPayment', 'create', $membershipPayment);
    }
  }
  
  protected function getRenewalPayment($contributionId) {
    if (!$contributionId) {
      return false;
    }
    
    try {
      $contribution = civicrm_api3('Contribution', 'getsingle', array('id' => $contributionId));
    } catch (Exception $ex) {
      return false;
    }
    
    $receiveDate = new DateTime();
    $contribution['receive_date'] = $receiveDate->format('YmdHis');
    unset($contribution['payment_instrument']);
    unset($contribution['contribution_id']);
    unset($contribution['invoice_id']);
    unset($contribution['id']);
    return $contribution;
  }
  
  protected function findMemberships(DateTime $minEndDate, DateTime $maxEndDate, $limit) {
    
    $sql = "SELECT `m`.*, `c`.`id` AS `contribution_id`, MAX(`c`.`receive_date`) 
            FROM `civicrm_membership` `m`
            LEFT JOIN `civicrm_membership_payment` `mp` ON `m`.`id` = `mp`.`membership_id`
            LEFT JOIN `civicrm_contribution` `c` ON `mp`.`contribution_id` = `c`.`id`
            WHERE `m`.`end_date` >= %1 AND `m`.`end_date` <= %2
            AND `m`.`status_id` IN (".implode(",", $this->status_config->getStatusIds()).")
            AND `m`.`membership_type_id` IN (".implode(",", $this->type_config->getMembershipTypeIds()).")  
            GROUP BY `m`.`id`
            LIMIT %3";
    $dao = CRM_Core_DAO::executeQuery($sql, array(
      1 => array($minEndDate->format('Y-m-d'), 'String'),
      2 => array($maxEndDate->format('Y-m-d'), 'String'),
      3 => array($limit, 'Positive')
    ));
    
    return $dao;
  }
  
}

