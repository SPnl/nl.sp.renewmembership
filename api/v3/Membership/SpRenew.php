<?php

/**
 * Membership.SpRenew API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_membership_sprenew_spec(&$spec) {
  
}

/**
 * Membership.SpRenew API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_membership_sprenew($params) {
  $minEndDate = new DateTime();
  $maxEndDate = new DateTime();
  $limit = 1000;
  
  if (!empty($params['offset_days'])) {
    $maxEndDate->add(new DateInterval('P'.$params['offset_days'].'D'));
  } else {
    $maxEndDate->add(new DateInterval('P7D'));
  }
  
  if (!empty($params['limit'])) {
    $limit = $params['limit'];
  }
  
  $status_config = new CRM_Renewmembership_Status_ActiveMemberships();
  $type_config = new CRM_Renewmembership_MembershipType_MembershipTypes();
  $renewal = new CRM_Renewmembership_Renew($status_config, $type_config);
  $count = $renewal->renew($minEndDate, $maxEndDate, $limit);

  $returnValues = array();
  $returnValues[] = array(
    'count' => $count,
    'message' => 'Renewed '.$count.' memberships',
  );
  
  return civicrm_api3_create_success($returnValues, $params, 'Membership', 'SpRenew');
}

