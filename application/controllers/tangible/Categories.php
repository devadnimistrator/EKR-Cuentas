<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Products");
    
    $this->load->model('tangible_category_m');
    $this->load->model("tangible_product_m");
  }

  public function index() {
    $params = array(
        "category_m" => $this->tangible_category_m
    );

    $this->load->view("tangible/categories", $params);
  }

  public function ajax_get_all_categories() {
    $categories = $this->tangible_category_m->get_categories();
    $nodes = array();
    if (!empty($categories)) {
      foreach ($categories as $id => $name) {
        $nodes[] = array(
            "id" => $id,
            "text" => $name,
            "tags" => array(
                $this->tangible_product_m->count_by_category_id($id)
            )
        );
      }
    }

    header('Content-Type: application/json');
    echo json_encode($nodes);
  }

  public function ajax_save_category() {
    if ($this->input->post('action') == 'process') {
      if ($this->tangible_category_m->form_validate($this->input->post()) == FALSE) {
        $this->tangible_category_m->show_errors();
      } else {
        $this->tangible_category_m->id = $this->input->post('category_id');
        $this->tangible_category_m->name = $this->input->post('name');
        $this->tangible_category_m->save();

        if ($this->input->post('category_id')) {
          my_show_msg(__("Successfully updated category."), "success");
        } else {
          my_show_msg(__("Successfully added new category."), "success");
        }
      }
    }
  }

  public function ajax_delete_category() {
    $this->tangible_category_m->get_by_id($this->uri->rsegment(3));
    if ($this->tangible_category_m->is_exists()) {
      $this->tangible_category_m->delete();
      my_show_msg(__("Successfully deleted category."), "success");
    }
  }

}
