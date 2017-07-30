<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Education_class_student_m extends My_Model {

  private function set_filter($class_id, $search) {
    $this->db->reset_query();
    $this->db->from($this->table);
    $this->db->join('students', 'education_class_students.student_id = students.`id`');
    if ($search) {
      $this->db->group_start();
      $this->db->like("students.first_name", $search);
      $this->db->or_like("students.last_name", $search);
      $this->db->group_end();
    }
    $this->db->where("education_class_students.class_id", $class_id);
  }

  public function find($class_id, $search, $start, $length) {
    $all_count = $this->count_all();
    if ($all_count == 0) {
      return array(
          "total" => $all_count,
          "count" => 0,
          "data" => array()
      );
    }
    $this->set_filter($class_id, $search);
    $count_by_filter = $this->db->count_all_results();
    if ($count_by_filter == 0) {
      return array(
          "total" => $all_count,
          "count" => $count_by_filter,
          "data" => array()
      );
    }

    $this->set_filter($class_id, $search);
    $this->db->select("education_class_students.*, students.first_name, students.last_name");
    $this->db->order_by("students.first_name");
    $this->db->order_by("students.last_name");
    if ($length) {
      $this->db->limit($length, $start);
    }
    return array(
        "total" => $all_count,
        "count" => $count_by_filter,
        "data" => $this->db->get()->result()
    );
  }

}
