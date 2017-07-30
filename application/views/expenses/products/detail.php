<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Expenses"); ?>
      <small>
        <i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("expenses/products"); ?>"><?php ___("Products"); ?></a>
        <i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?>
      </small>
    </h3>
  </div>
  <?php if ($product_m->is_exists()): ?>
    <div class="title_right">
      <div class="pull-right">
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("expenses/products/add"); ?>'"><?php ___("Add New"); ?></button>

      </div>
    </div>
  <?php endif; ?>
  <div class="clearfix"></div>
</div>

<div class="row">
  <div class=" col-sm-12 col-xs-12">
    <?php
    $product_m->show_errors();
    $product_m->show_msgs();

    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editProduct",
    "autocomplete" => false
);

$product_m->form_create($formConfig);
$product_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Product Information"); ?></h2>
        <!--ul class="nav navbar-right panel_toolbox">
          <li><a href="<?php echo base_url("expenses/categories"); ?>"><i class="fa fa-tags"></i> <?php ___("Categories"); ?></a>
        </ul-->
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $product_m->form_add_element("category_id", array('label' => __("Category") . ' <a href="' . base_url("expenses/categories") . '" title="'.__("Add Category").'"><i class="fa fa-plus-circle"></i></a>', 'type' => 'select', 'options' => $this->categories));
        $product_m->form_add_element("name");
        //$product_m->form_add_element("price", array('label' => __("Price") . "(" . my_get_currency_unit() . ")"));

        $product_m->bs_form->form_elements(TRUE);
        ?>

        <div class="ln_solid"></div>

        <?php
        $product_m->bs_form->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$product_m->bs_form->form_end(TRUE);
