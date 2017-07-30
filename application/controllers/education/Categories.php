<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// education/categories

class Categories extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Products");

    $this->load->model('education_category_m');
    $this->load->model("education_product_m");
  }

  public function index() {
    $params = array(
        "category_m" => $this->education_category_m
    );

    $this->load->view("education/categories", $params);
  }

  public function ajax_get_parent_categories() {
    $categories = array_merge(array(array("id" => 0, "name" => __("Is Parent"))), $this->education_category_m->get_categories(0, false));

    header('Content-Type: application/json');
    echo json_encode($categories);
  }

  public function ajax_get_all_categories() {
    header('Content-Type: application/json');
    echo json_encode($this->education_category_m->get_tree_node());
  }

  public function ajax_save_category() {
    if ($this->input->post('action') == 'process') {
      if ($this->education_category_m->form_validate($this->input->post()) == FALSE) {
        $this->education_category_m->show_errors();
      } else {
        $category_id = $this->input->post('category_id');
        $parent_id = $this->input->post('parent_id');

        $error = false;
        if ($category_id) {
          if ($category_id == $parent_id) {
            $error = true;
            
            my_show_msg(__("Parent can't use the self."), "danger");
          } else {
            
          }
        }

        if ($error === false) {
          $this->education_category_m->id = $category_id;
          $this->education_category_m->parent_id = $parent_id;
          $this->education_category_m->name = $this->input->post('name');
          $this->education_category_m->save();
          if ($this->input->post('category_id')) {
            my_show_msg(__("Successfully updated category."), "success");
          } else {
            my_show_msg(__("Successfully added new category."), "success");
          }
        }
      }
    }
  }

  public function ajax_delete_category() {
    $category_id = $this->uri->rsegment(3);
    $this->education_category_m->get_by_id($category_id);
    if ($this->education_category_m->is_exists()) {
      $parent_id = $this->education_category_m->parent_id;
      $this->education_category_m->delete();
      if ($parent_id == 0) {
        $this->db->where("parent_id", $category_id);
        $this->db->delete($this->education_category_m->table);
      }
      my_show_msg(__("Successfully deleted category."), "success");
    }
  }

}
