<?php

interface CRM_Renewmembership_MembershipType_Config {
  
  /**
   * @return array with ID's of membership types
   */
  public function getMembershipTypeIds();
  
  /**
   * 
   * @return DateTime new end date for membership
   */
  public function getNewEndDate(DateTime $currentEndDate, $membership_type_id);
  
}

