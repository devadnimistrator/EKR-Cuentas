<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// education/products

class Products extends My_Controller {

  public function __construct() {
    parent::__construct();

    $this->page_title = __("Education Products");

    $this->load->model('education_category_m');
    $this->load->model("education_product_m");

    if ($this->uri->rsegment(2) == 'add') {
      $this->page_title = __("Add New Product");
    } elseif ($this->uri->rsegment(2) == 'edit') {
      $this->education_product_m->get_by_id($this->uri->rsegment(3));
      $this->page_title = __("Edit Product") . ": #" . $this->education_product_m->id;
    }
  }

  public function index() {
    $this->my_save_current_url();

    $this->categories = $this->education_category_m->get_all_categories();

    $category = $this->uri->rsegment(3);
    if ($category === null) {
      $category = 'all';
    } else {
      
    }
    $this->load->view("education/products/list", array("category" => $category));
  }

  public function add() {
    $this->my_save_current_url();

    if ($this->input->post('action') == 'process') {
      if ($this->education_product_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->education_product_m->save()) {
          my_set_system_message(__("Successfully added new product."), "success");

          redirect("education/products/edit/" . $this->education_product_m->id);
        } else {
          $this->education_product_m->add_error("id", __("Failed add product."));
        }
      }
    }

    $this->categories = $this->education_category_m->get_all_categories();
    $this->load->view('education/products/detail', array(
        'product_m' => $this->education_product_m
    ));
  }

  public function edit() {
    $this->my_save_current_url();

    if ($this->input->post('action') == 'process') {
      if ($this->education_product_m->form_validate($this->input->post()) == FALSE) {
        
      } else {
        if ($this->education_product_m->save()) {
          my_set_system_message(__("Successfully saved product informations."), "success");

          redirect("education/products/edit/" . $this->education_product_m->id);
        } else {
          $this->userinfo_m->add_error("id", __("Failed save product informations."));
        }
      }
    }

    $this->categories = $this->education_category_m->get_all_categories();
    $this->load->view('education/products/detail', array(
        'product_m' => $this->education_product_m
    ));
  }

  public function ajax_delete() {
    $id = $this->uri->rsegment(3);
    $this->education_product_m->get_by_id($id);
    if ($this->education_product_m->is_exists()) {
      $this->education_product_m->delete();
    }
  }

  public function ajax_find() {
    $categories = $this->education_category_m->get_all_categories(true);

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $category = $this->input->post('category_id');

    $result = $this->education_product_m->find($category, $search['value'], $order, $start, $length);
    $products = array();
    if ($result['count'] > 0) {
      foreach ($result['data'] as $row) {
        $category = "";
        if (isset($categories[$row->category_id])) {
          if ($categories[$row->category_id]['parent_id'] == 0) {
            $category = $categories[$row->category_id]['name'];
          } else {
            $category = $categories[$row->category_id]['parent_name'] . " / " . $categories[$row->category_id]['name'];
          }
        }
        $product = array(
            "index" => ( ++$start),
            "product_id" => $row->id,
            "category" => $category,
            "price" => '<span class="datatable-number">' . $row->price . '</span>',
            "name" => $row->name,
            "actions" => my_make_table_edit_btn(base_url("education/products/edit/" . $row->id))
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
