<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for student
 *
 * - Change Profile
 */
class Students extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Students");

    $this->load->model("student_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Student");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->student_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Student") . ": #" . $this->student_m->id;
    }
  }

  public function index() {
    $status = $this->uri->rsegment(3);
    if ($status === null) {
      $status = 'all';
    } else {
      
    }
    $this->load->view("students/list", array("status" => $status));
  }

  public function add() {
    if ($this->input->post('action') == 'process') {
      if ($this->student_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->student_m->save()) {
          $this->student_m->save();

          my_set_system_message(__("Successfully added new student."), "success");

          redirect("students/edit/" . $this->student_m->id);
        } else {
          $this->student_m->add_error("id", __("Failed add cashier."));
        }
      }
    }

    $this->load->model('geo_state_m');
    $this->geo_states = $this->geo_state_m->get_states_by_country(DEFAULT_COUNTRY);

    $this->geo_state_m->get_by_country_iso_code(DEFAULT_COUNTRY);
    
    $this->load->view('students/edit', array(
        'student_m' => $this->student_m,
        'geo_state_m' => $this->geo_state_m
    ));
  }

  public function edit() {
    if ($this->input->post('action') == 'process') {
      if ($this->student_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->student_m->save()) {
          my_set_system_message(__("Successfully saved student informations."), "success");

          redirect("students/edit/" . $this->student_m->id);
        } else {
          $this->student_m->add_error("id", __("Failed save cashier informations."));
        }
      }
    }

    $this->load->model('geo_state_m');
    $this->geo_states = $this->geo_state_m->get_states_by_country(DEFAULT_COUNTRY);
    
    $this->load->view('students/edit', array(
        'student_m' => $this->student_m,
        'geo_state_m' => $this->geo_state_m
    ));
  }

  public function ajax_get() {
    $id = $this->uri->rsegment(3);
    $this->student_m->get_by_id($id);

    $this->load->model('geo_state_m');
    
    $student_info = array(
        "name" => $this->student_m->first_name . " " . $this->student_m->last_name,
        "email" => $this->student_m->email,
        "phone" => $this->student_m->phone,
        "cellphone" => $this->student_m->cellphone,
        "company_name" => $this->student_m->company_name,
        "tax_payer_number" => $this->student_m->tax_payer_number,
        "address" => $this->student_m->address,
        "state" => $this->geo_state_m->get_state_name(DEFAULT_COUNTRY, $this->student_m->state),
        "city" => $this->student_m->city,
        "zipcode" => $this->student_m->zipcode,
        "invoice_email" => $this->student_m->invoice_email
    );
    
    header('Content-Type: application/json');
    echo json_encode($student_info);
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->student_m->get_by_id($id);
    if ($this->student_m->is_exists()) {
      $this->student_m->delete();
    }
  }

  public function ajax_find() {
    //$status = $this->uri->rsegment(3);


    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');

    $result = $this->student_m->find($search['value'], $order, $start, $length);
    $students = array();
    if ($result['count'] > 0) {
      $student_status = $this->config->item("data_status");
      foreach ($result['data'] as $student) {



        $actions = array();
        $actions[] = array(
            "label" => __("View"),
            "url" => 'javascript:view_student(' . $student->id . ')',
            "icon" => 'list-alt'
        );
        $actions[] = array(
            "label" => __("Edit"),
            "url" => base_url("students/edit/" . $student->id),
            "icon" => 'edit'
        );
        $actions[] = array(
            "label" => __("Delete"),
            "url" => 'javascript:delete_student(' . $student->id . ')',
            "icon" => 'trash-o'
        );

        $students[] = array(
            "index" => ( ++$start),
            "student_id" => $student->id,
            "first_name" => $student->first_name,
            "last_name" => $student->last_name,
            "email" => $student->email,
            "phone" => $student->phone,
            "cellphone" => $student->cellphone,
            "actions" => my_make_table_edit_btn(base_url('students/edit/' . $student->id))
            . my_make_table_delete_btn('javascript:delete_student(' . $student->id . ')')
            . my_make_table_btn("javascript:show_student(" . $student->id . ")", "View", "dark", "list-alt")
        );
      }
    }

    $returnData = array(
        "draw" => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $students
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}
