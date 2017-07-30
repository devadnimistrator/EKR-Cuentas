<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ajax
 *
 * @author psp
 */
class App extends My_Controller {

  public function ajax_get_categories() {
    $product_type = $this->uri->segment(3);

    $categories = array();

    if ($product_type == PAYMENT_REASON_TYPE_EDUCATION) {
      $this->load->model("education_category_m");
      $categories = $this->education_category_m->get_all_categories(false);
    } elseif ($product_type == PAYMENT_REASON_TYPE_TANGIBLE) {
      $this->load->model("tangible_category_m");
      $categories = $this->tangible_category_m->get_categories();
    } elseif ($product_type == PAYMENT_REASON_TYPE_EXPENSES) {
      $this->load->model("expenses_category_m");
      $categories = $this->expenses_category_m->get_categories();
    }

    $result = array();
    foreach ($categories as $key => $value) {
      $result[] = array(
          "id" => $key,
          "name" => $value
      );
    }

    header('Content-Type: application/json');
    echo json_encode($result);
  }

  public function ajax_get_procuts() {
    $type = $this->uri->segment(3);
    $category = $this->uri->segment(4);
    $products = array();

    if ($type == PAYMENT_REASON_TYPE_EDUCATION) {
      $this->load->model("education_product_m");
      $products = $this->education_product_m->get_products($category, false);
    } elseif ($type == PAYMENT_REASON_TYPE_TANGIBLE) {
      $this->load->model("tangible_product_m");
      $products = $this->tangible_product_m->get_products($category, false);
    } elseif ($type == PAYMENT_REASON_TYPE_EXPENSES) {
      $this->load->model("expenses_product_m");
      $products = $this->expenses_product_m->get_products($category, false);
    }

    header('Content-Type: application/json');
    echo json_encode($products);
  }

  public function ajax_get_procut_price() {
    $type = $this->uri->segment(3);
    $product_id = $this->uri->segment(4);

    $price = 0;
    if ($type == PAYMENT_REASON_TYPE_EDUCATION) {
      $this->load->model("education_product_m");
      $this->education_product_m->get_by_id($product_id);
      $price = $this->education_product_m->price;
    } elseif ($type == PAYMENT_REASON_TYPE_TANGIBLE) {
      $this->load->model("tangible_product_m");
      $this->tangible_product_m->get_by_id($product_id);
      $price = $this->tangible_product_m->price;
    } elseif ($type == PAYMENT_REASON_TYPE_EXPENSES) {
      $this->load->model("expenses_product_m");
      $this->expenses_product_m->get_by_id($product_id);
      $price = $this->expenses_product_m->price;
    }

    echo $price;
  }
  
  public function ajax_get_class() {
    $start_date = $this->input->post("start_date");
    $end_date = $this->input->post("end_date");
    
    $query = "select c.`id` as class_id, p.`name` as product_name, c.start_datetime";
    $query.= " from education_payments ep join education_classes c on ep.class_id=c.`id` join education_products p on c.product_id=p.`id`";
    $query.= " where ep.paid_date between '".$start_date."' and '".$end_date."'";
    $query.= " order by c.start_datetime";
    $result = $this->db->query($query)->result();
    
    $classes = array();
    foreach ($result as $row) {
      $classes[] = array(
          "id" => $row->class_id,
          "name" => substr($row->start_datetime, 0, 10) . ", " . $row->product_name,
      );
    }
    
    header('Content-Type: application/json');
    echo json_encode($classes);
  }

}
