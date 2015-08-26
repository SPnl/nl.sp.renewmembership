<?php

class CRM_Renewmembership_Selector {

  protected $where;

  public function __construct() {
    $this->load();
  }

  public function load() {
    if (isset($_SESSION['CRM_Renewmembership_Selector'])) {
      $this->where = $_SESSION['CRM_Renewmembership_Selector'];
    }
  }

  public function store() {
    $_SESSION['CRM_Renewmembership_Selector'] = $this->where;
  }

  public function setData($membership_types, $status_ids, $end_date_from_range, $end_date_to_range) {
    $this->where = "WHERE 1 ";
    if (is_array($membership_types) && count($membership_types)) {
      $this->where .= " AND membership_type_id IN (".implode(",", $membership_types).")";
    }
    if (is_array($status_ids) && count($status_ids)) {
      $this->where .= " AND status_id IN (".implode(",", $status_ids).")";
    }
    if (!empty($end_date_from_range) && !empty($end_date_to_range)) {
      $from = new DateTime($end_date_from_range);
      $to = new DateTime($end_date_to_range);
      $this->where .= " AND DATE(end_date) >= DATE('".$from->format('Y-m-d')."') AND DATE(end_date) <= DATE('".$to->format('Y-m-d')."')";
    } elseif (!empty($end_date_from_range) && empty($end_date_to_range)) {
      $from = new DateTime($end_date_from_range);
      $this->where .= " AND DATE(end_date) >= DATE('".$from->format('Y-m-d')."')";
    } elseif (empty($end_date_from_range) && !empty($end_date_to_range)) {
      $to = new DateTime($end_date_to_range);
      $this->where .= " AND DATE(end_date) <= DATE('".$to->format('Y-m-d')."')";
    }
  }

  public function getWhere() {
    return $this->where;
  }

}