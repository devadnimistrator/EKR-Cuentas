<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Calendar");

    $this->load->model("education_class_m");
  }

  public function index() {
    $this->load->view("education/calendar");
  }

  public function ajax_get_events() {
    $start = $this->input->get("start");
    $end = $this->input->get("end");

    $query = "select * from education_classes where 1=1";
    $query .= " or (start_datetime between '" . $start . " 00:00' and '" . $end . " 23:59')";
    $query .= " or (end_datetime between '" . $start . " 00:00' and '" . $end . " 23:59')";
    $query .= " or (cancel_date between '" . $start . "' and '" . $end . "')";
    //$query .= " order by start_datetime";

    $results = $this->db->query($query)->result();

    $this->load->model("education_product_m");
    $events = array();
    foreach ($results as $class) {
      $this->education_product_m->get_by_id($class->product_id);
      if ($this->education_product_m->is_exists()) {
        $start_time = my_formart_date($class->start_datetime, 'G:m');
        $end_time = my_formart_date($class->end_datetime, 'G:m');
        if ($class->status == 'end') {
          $events[] = array(
              "id" => $class->id,
              "title" => $start_time . " " . __("Start") . ",\n" . $this->education_product_m->name,
              "start" => substr($class->start_datetime, 0, 10),
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
          $events[] = array(
              "id" => $class->id,
              "title" => $end_time . " " . __("End") . ",\n" . $this->education_product_m->name,
              "start" => substr($class->end_datetime, 0, 10),
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
        } elseif ($class->status == 'cancel') {
          $events[] = array(
              "id" => $class->id,
              "title" => $start_time . " " . __("Start") . ",\n" . $this->education_product_m->name,
              "start" => substr($class->start_datetime, 0, 10),
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
          $events[] = array(
              "id" => $class->id,
              "title" => __("Cancel") . ",\n" . $this->education_product_m->name,
              "start" => $class->cancel_date,
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
        } elseif ($class->status == 'plan') {
          $events[] = array(
              "id" => $class->id,
              "title" => $start_time . " " . __("Plan") . ",\n" . $this->education_product_m->name,
              "start" => substr($class->start_datetime, 0, 10),
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
        } elseif ($class->status == 'running') {
          $events[] = array(
              "id" => $class->id,
              "title" => $start_time . " " . __("Running") . ",\n" . $this->education_product_m->name,
              "start" => substr($class->start_datetime, 0, 10),
              "status" => $class->status,
              "url" => site_url("education/classes/edit/" . $class->id)
          );
        }
      }
    }

    $sql = "select * from education_class_steps where due_date between '" . $start . "' and '" . $end . "'";
    $result = $this->db->query($sql)->result();
    foreach ($result as $step) {
      $this->education_class_m->get_by_id($step->class_id);

      if ($this->education_class_m->status == 'cancel' || $this->education_class_m->status == 'end') {
        continue;
      }

      $this->education_product_m->get_by_id($this->education_class_m->product_id);

      $events[] = array(
          "id" => $this->education_class_m->id,
          "title" => __("Step") . " " . $step->step . ",\n" . $this->education_product_m->name,
          "start" => $step->due_date,
          "status" => $this->education_class_m->status, //"step",
          "url" => site_url("education/classes/edit/" . $this->education_class_m->id)
      );
    }

    header('Content-Type: application/json');
    echo json_encode($events);
  }

  public function ajax_get() {
    $id = $this->uri->segment(4);

    $this->load->model("education_product_m");

    $this->education_class_m->get_by_id($id);
    $this->education_product_m->get_by_id($this->education_class_m->product_id);

    $eventData = array(
        "category_id" => $this->education_product_m->category_id,
        "product_id" => $this->education_product_m->id,
        "start_date" => substr($this->education_class_m->start_datetime, 0, 10),
        "start_time_h" => substr($this->education_class_m->start_datetime, 11, 2),
        "start_time_m" => substr($this->education_class_m->start_datetime, 14, 2)
    );

    header('Content-Type: application/json');
    echo json_encode($eventData);
  }

  public function ajax_save() {
    $this->load->library("form_validation");
    $valid_config = array(
        array(
            'field' => 'product_id',
            'label' => __('Product'),
            'rules' => 'required'
        ),
        array(
            'field' => 'start_date',
            'label' => __('Start Date'),
            'rules' => 'required',
        )
    );

    $this->form_validation->set_rules($valid_config);

    $this->user_m->username = $this->input->post('username');
    $this->user_m->password = $this->input->post('password');

    if ($this->form_validation->run() == FALSE) {
      echo 0;
    } else {
      $schedule_id = $this->input->post("schedule_id");
      if ($schedule_id) {
        $this->education_class_m->get_by_id($schedule_id);
      }

      $this->education_class_m->product_id = $this->input->post("product_id");
      $this->education_class_m->start_datetime = $this->input->post("start_date") . " " . $this->input->post("start_time_h") . ":" . $this->input->post("start_time_m");
      if ($this->education_class_m->save()) {
        echo $this->education_class_m->id;
      } else {
        echo 0;
      }
    }

    exit;
  }

  public function ajax_delete() {
    $id = $this->uri->segment(4);

    $this->education_class_m->get_by_id($id);
    if ($this->education_class_m->is_exists()) {
      $this->education_class_m->delete();
      die('OK');
    }
    die('NO');
  }

}
