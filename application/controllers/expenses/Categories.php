<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Categories");

    $this->load->model('expenses_category_m');
    $this->load->model("expenses_product_m");
  }

  public function index() {
    $params = array(
        "category_m" => $this->expenses_category_m
    );

    $this->load->view("expenses/categories", $params);
  }

  public function ajax_get_all_categories() {
    $categories = $this->expenses_category_m->get_categories();
    $nodes = array();
    if (!empty($categories)) {
      foreach ($categories as $id => $name) {
        $nodes[] = array(
            "id" => $id,
            "text" => $name,
            "tags" => array(
                $this->expenses_product_m->count_by_category_id($id)
            )
        );
      }
    }

    header('Content-Type: application/json');
    echo json_encode($nodes);
  }

  public function ajax_save_category() {
    if ($this->input->post('action') == 'process') {
      if ($this->expenses_category_m->form_validate($this->input->post()) == FALSE) {
        $this->expenses_category_m->show_errors();
      } else {
        $this->expenses_category_m->id = $this->input->post('category_id');
        $this->expenses_category_m->name = $this->input->post('name');
        $this->expenses_category_m->save();

        if ($this->input->post('category_id')) {
          my_show_msg(__("Successfully updated category."), "success");
        } else {
          my_show_msg(__("Successfully added new category."), "success");
        }
      }
    }
  }

  public function ajax_delete_category() {
    $this->expenses_category_m->get_by_id($this->uri->rsegment(3));
    if ($this->expenses_category_m->is_exists()) {
      $this->expenses_category_m->delete();
      
      my_show_msg(__("Successfully deleted category."), "success");
    }
  }

}
