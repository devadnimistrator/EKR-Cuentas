<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Expenses Products");

    $this->load->model('tangible_category_m');
    $this->load->model("tangible_product_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Product");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->tangible_product_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Product") . ": #" . $this->tangible_product_m->id;
    }
  }

  public function index() {
    $this->my_save_current_url();

    $this->categories = $this->tangible_category_m->get_categories();

    $category = $this->uri->rsegment(3);
    if ($category === null) {
      $category = 'all';
    } else {
      
    }
    $this->load->view("tangible/products/list", array("category" => $category));
  }

  public function add() {
    $this->my_save_current_url();

    if ($this->input->post('action') == 'process') {
      if ($this->tangible_product_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->tangible_product_m->save()) {
          my_set_system_message(__("Successfully added new product."), "success");

          redirect("tangible/products/edit/" . $this->tangible_product_m->id);
        } else {
          $this->tangible_product_m->add_error("id", __("Failed add product."));
        }
      }
    }

    $this->categories = $this->tangible_category_m->get_categories();
    $this->load->view('tangible/products/detail', array(
        'product_m' => $this->tangible_product_m
    ));
  }

  public function edit() {
    $this->my_save_current_url();

    if ($this->input->post('action') == 'process') {
      if ($this->tangible_product_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->tangible_product_m->save()) {
          my_set_system_message(__("Successfully saved product informations."), "success");

          redirect("tangible/products/edit/" . $this->tangible_product_m->id);
        } else {
          $this->userinfo_m->add_error("id", __("Failed save product informations."));
        }
      }
    }

    $this->categories = $this->tangible_category_m->get_categories();
    $this->load->view('tangible/products/detail', array(
        'product_m' => $this->tangible_product_m
    ));
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->tangible_product_m->get_by_id($id);
    if ($this->tangible_product_m->is_exists()) {
      $this->tangible_product_m->delete();
    }
  }

  public function ajax_find() {
    $categories = $this->tangible_category_m->get_categories();

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $category = $this->input->post('category_id');

    $result = $this->tangible_product_m->find($category, $search['value'], $order, $start, $length);
    $products = array();
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $category = "";
        if (isset($categories[$row->category_id])) {
          $category = $categories[$row->category_id];
        }
        $product = array(
            "index" => ( ++$start),
            "product_id" => $row->id,
            "category" => $category,
            "price" => '<span class="datatable-number">' . $row->price . '</span>',
            "name" => $row->name,
            "actions" => my_make_table_edit_btn(base_url("tangible/products/edit/" . $row->id))
            . my_make_table_delete_btn('javascript:delete_product(' . $row->id . ')')
        );
        $products[] = $product;
      }
    }

    $returnData = array(
        'draw' => $draw,
        'recordsTotal' => $result['total'],
        'recordsFiltered' => $result['count'],
        'data' => $products
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

}
