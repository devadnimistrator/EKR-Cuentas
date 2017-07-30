<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Education_category_m extends My_Model {

  public $fields = array(
      'parent_id' => array(
          'label' => 'Parent',
          'type' => 'select',
      ),
      'name' => array(
          'label' => 'Name',
          'rules' => array('required', 'min_length[3]')
      )
  );

  public function get_categories($parent_id = false, $index_array = true) {
    if ($parent_id !== false) {
      $this->db->order_by('name');
      $this->db->where('parent_id', $parent_id);
    } else {
      $this->db->order_by('parent_id');
      $this->db->order_by('name');
    }
    $result = $this->db->get($this->table)->result();

    if (empty($result)) {
      return array();
    } else {
      if ($index_array) {
        $categories = array();
        foreach ($result as $category) {
          $categories[$category->id] = ($category->parent_id ? '&nbsp;&nbsp;' : '') . $category->name;
        }
        return $categories;
      } else {
        return $result;
      }
    }
  }

  public function get_tree_node($selected_id = 0) {
    $parents = $this->get_categories(0, false);

    $categories = array();
    foreach ($parents as $parent) {
      $node = array(
          "id" => $parent->id,
          "text" => $parent->name,
          "parent_id" => 0,
      );

      $childs = $this->get_categories($parent->id, false);
      if (!empty($childs)) {
        $node['nodes'] = array();

        foreach ($childs as $category) {
          $node['nodes'][] = array(
              "id" => $category->id,
              "text" => $category->name,
              "parent_id" => $category->parent_id,
          );
        }
      }

      $categories[] = $node;
    }

    return $categories;
  }

  public function get_all_categories($show_parent = false) {
    $parents = $this->get_categories(0, false);

    $categories = array();
    foreach ($parents as $parent) {
      if ($show_parent) {
        $categories[$parent->id] = array(
            "parent_id" => 0,
            "parent_name" => "",
            "name" => $parent->name
        );
      } else {
        $categories[$parent->id] = $parent->name;
      }


      $childs = $this->get_categories($parent->id, false);
      if (!empty($childs)) {
        $node['nodes'] = array();

        foreach ($childs as $category) {
          if ($show_parent) {
            $categories[$category->id] = array(
                "parent_id" => $parent->id,
                "parent_name" => $parent->name,
                "name" => $category->name
            );
          } else {
            $categories[$category->id] = "&nbsp;&nbsp;&nbsp;&nbsp;" . $category->name;
          }
        }
      }
    }

    return $categories;
  }
  
  public function delete() {
    parent::delete();
    
    sync_education_payments();
  }

}
