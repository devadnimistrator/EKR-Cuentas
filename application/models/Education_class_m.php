<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Education_class_m extends My_Model {

  public $fields = array(
      "category_id" => array(
          'label' => 'Category',
          'rules' => array('required'),
          'value' => 0,
      ),
      "product_id" => array(
          'label' => 'Product',
          'rules' => array('required')
      ),
      "start_datetime" => array(
          'label' => 'Start Time',
          'rules' => array('required')
      ),
      "end_datetime" => array(
          'label' => 'End Time'
      ),
      "registration_fee" => array(
          'type' => 'number',
          'label' => 'Fee'
      )
  );

  private function set_filter($start_date, $end_date, $status, $category_id, $search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    $this->db->join('education_products', 'education_classes.product_id = education_products.`id`');
    if ($search) {
      $this->db->like("education_products.name", $search);
    }
    if ($category_id != 'all' && $category_id != '') {
      $this->db->group_start();
      $this->db->where("education_products.category_id", $category_id);
      $this->db->or_where("education_products.category_id in (select `id` from education_categories where parent_id=" . $category_id . ")");
      $this->db->group_end();
    }
    if ($status != 'all') {
      $this->db->where("education_classes.`status`", $status);
    }
    if ($start_date) {
      $this->db->where("education_classes.start_datetime >= ", $start_date . " 00:00");
    }
    if ($start_date) {
      $this->db->where("education_classes.start_datetime <= ", $end_date . " 23:59");
    }
  }

  public function find($start_date, $end_date, $status, $category_id, $search, $orders, $start, $length) {
    $all_count = $this->count_all();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "count" => 0,
          "data" => array()
      );
    }
    $this->set_filter($start_date, $end_date, $status, $category_id, $search);
    $count_by_filter = $this->db->count_all_results();
    
    if ($count_by_filter == 0) {
      return array(
          "total" => $all_count,
          "count" => $count_by_filter,
          "data" => array()
      );
    }

    $this->set_filter($start_date, $end_date, $status, $category_id, $search);
    $this->db->select("education_classes.*, education_products.name as product_name");

    if ($orders) {
      foreach ($orders as $order) {
        switch ($order['column']) {
          case 1 :
            $order_field = "start_datetime";
            break;
        }
        $this->db->order_by($order_field, $order['dir']);
      }
    } else {
      $this->db->order_by("start_datetime", "desc");
    }

    $this->db->limit($length, $start);
    return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => $this->db->get()->result()
    );
  }
  
  public function get_classes($status = false) {
    $this->db->from($this->table);
    $this->db->join('education_products', 'education_classes.product_id = education_products.`id`');
    if (is_array($status)) {
      $this->db->where_in("education_classes.status", $status);
    } elseif ($status) {
      $this->db->where("education_classes.status", $status);
    }
    $this->db->order_by("education_classes.start_datetime");
    $this->db->select("education_classes.id, education_classes.start_datetime, education_products.`name` as product_name");
    $results = $this->db->get()->result();
    
    $classes = array();
    foreach ($results as $row) {
      $classes[$row->id] = my_formart_date($row->start_datetime, DATE_FULL_FORMAT) . ", " . $row->product_name;
    }
    return $classes;
  }
  
  public function delete() {
    parent::delete();
    
    sync_education_payments();
  }

}
