<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Companies extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Companies");

    $this->load->model("expenses_company_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Company");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->expenses_company_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Company") . ": #" . $this->expenses_company_m->id;
    }
  }

  public function index() {
    $this->load->view("expenses/companies/list");
  }

  public function add() {
    if ($this->input->post('action') == 'process') {
      if ($this->expenses_company_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        $this->expenses_company_m->country = DEFAULT_COUNTRY;
        if ($this->expenses_company_m->save()) {
          my_set_system_message(__("Successfully added new company."), "success");

          redirect("expenses/companies/edit/" . $this->expenses_company_m->id);
        } else {
          $this->expenses_company_m->add_error("id", __("Failed add company."));
        }
      }
    }

    $this->load->model('geo_state_m');
    $this->geo_states = $this->geo_state_m->get_states_by_country(DEFAULT_COUNTRY);

    $this->load->view('expenses/companies/detail', array(
        'expenses_company_m' => $this->expenses_company_m,
        'geo_state_m' => $this->geo_state_m
    ));
  }

  public function edit() {
    if ($this->input->post('action') == 'process') {
      if ($this->expenses_company_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->expenses_company_m->save()) {
          my_set_system_message(__("Successfully saved company informations."), "success");

          redirect("expenses/companies/edit/" . $this->expenses_company_m->id);
        } else {
          $this->userinfo_m->add_error("id", __("Failed save company informations."));
        }
      }
    }

    $this->load->model('geo_state_m');
    $this->geo_states = $this->geo_state_m->get_states_by_country($this->expenses_company_m->country);

    $this->load->view('expenses/companies/detail', array(
        'expenses_company_m' => $this->expenses_company_m,
        'geo_state_m' => $this->geo_state_m
    ));
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->expenses_company_m->get_by_id($id);
    if ($this->expenses_company_m->is_exists()) {
      $this->expenses_company_m->delete();
    }
  }

  public function ajax_find() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    
    $result = $this->expenses_company_m->find($search['value'], $order, $start, $length);
    $companies = array();
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $company = array(
            "index" => ( ++$start),
            "company_id" => $row->id,
            "name" => $row->name,
            "taxnumber" => $row->taxnumber,
            "telphone" => $row->telphone,
            "email" => $row->email,
            "address" => my_address_display($row),
            "actions" => my_make_table_edit_btn(base_url("expenses/companies/edit/" . $row->id))
            . my_make_table_delete_btn('javascript:delete_company(' . $row->id . ')')
        );
        $companies[] = $company;
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $companies
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}
