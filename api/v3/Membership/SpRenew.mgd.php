<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:Membership.SpRenew',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call Membership.SpRenew API',
      'description' => 'Call Membership.SpRenew API',
      'run_frequency' => 'Hourly',
      'api_entity' => 'Membership',
      'api_action' => 'SpRenew',
      'parameters' => 'offset_days=15',
      'is_active' => 0,
    ),
  ),
);