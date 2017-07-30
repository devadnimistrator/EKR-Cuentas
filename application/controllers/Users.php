<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for profile
 *
 * - Change Profile
 */
class Users extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->check_admin();
    
    $this->page_title = __("Cashiers");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Cashier");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->user_m->get_by_id($this->uri->rsegment(3));
      $this->userinfo_m->get_by_user_id($this->user_m->id);
      $this->page_title = __("Edit Cashier") . ": #" . $this->user_m->id;
    }
  }

  public function index() {
    $status = $this->uri->rsegment(3);
    if ($status === null) {
      $status = 'all';
    } else {
      
    }
    $this->load->view("users/list", array("status" => $status));
  }

  public function add() {
    if ($this->input->post('action') == 'process') {
      if ($this->user_m->form_validate($this->input->post()) == FALSE || $this->userinfo_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->user_m->save()) {
          $this->userinfo_m->user_id = $this->user_m->id;
          $this->userinfo_m->save();

          my_set_system_message(__("Successfully added new cashier."), "success");

          redirect("users/edit/" . $this->user_m->id);
        } else {
          $this->userinfo_m->add_error("id", __("Failed add cashier."));
        }
      }
    }

    $this->load->view('users/add', array(
        'userinfo_m' => $this->userinfo_m,
        'user_m' => $this->user_m
    ));
  }

  public function edit() {
    if ($this->input->post('action') == 'process') {
      if ($this->user_m->form_validate($this->input->post()) == FALSE || $this->userinfo_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        $this->user_m->password = my_encrypt_password($this->user_m->password);
        if ($this->user_m->save() && $this->userinfo_m->save()) {
          my_set_system_message(__("Successfully saved cashier informations."), "success");

          redirect("users/edit/" . $this->user_m->id);
        } else {
          $this->userinfo_m->add_error("id", __("Failed save cashier informations."));
        }
      }
    }

    $this->load->view('users/edit', array(
        'userinfo_m' => $this->userinfo_m,
        'user_m' => $this->user_m
    ));
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->user_m->get_by_id($id);
    if ($this->user_m->is_exists()) {
      $this->userinfo_m->get_by_user_id($this->user_m->id);
      $this->user_m->delete();
      $this->userinfo_m->delete();
    }
  }

  public function ajax_find() {
    //$status = $this->uri->rsegment(3);


    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $status = $this->input->post('status');

    $result = $this->userinfo_m->find($status, $search['value'], $order, $start, $length);
    $users = array();
    if ($result['count'] > 0) {
      $user_status = $this->config->item("data_status");
      foreach ($result['data'] as $user) {
        $users[] = array(
            "index" => ( ++$start),
            "user_id" => $user->id,
            "username" => $user->username,
            "fullname" => $user->fullname,
            "email" => $user->email,
            "phone" => $user->phone,
            "status" => $user_status[$user->status],
            "last_access" => my_formart_date($user->last_access, DATETIME_FULL_FORMAT),
            "actions" => my_make_table_edit_btn(base_url('users/edit/' . $user->user_id))
            . my_make_table_delete_btn('javascript:delete_user(' . $user->user_id . ')')
        );
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $users
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}
