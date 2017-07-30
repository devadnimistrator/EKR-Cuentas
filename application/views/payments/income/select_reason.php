<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Payments"); ?>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/income"); ?>"><?php ___("Income"); ?></a> </small>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("New regist"); ?></small>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Step1"); ?></small>
    </h3>
  </div>
</div>
<div class="clearfix"></div>

<?php
my_show_msg($error_msgs, "danger");

$formConfig = array(
    "name" => "editPayment",
    "autocomplete" => false,
    "col_width" => 0,
    "buttons" => array(array(
            "type" => "submit",
            "value" => __("Next"),
            "options" => array("class" => "btn btn-primary")
        ))
);

$bsForm = new My_bs_form($formConfig);
$bsForm->form_start(TRUE);
$bsForm->add_element("action", BSFORM_HIDDEN, "process");
?>

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Chooise Reason"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $income_types = array(
            'label' => __("Type"),
            'required' => true,
            'options' => $this->income_types
        );
        $bsForm->add_element("reason_type", BSFORM_SELECT, $income_type, $income_types);
        $bsForm->form_elements(TRUE);
        ?>
        
        <div id="education_class_field">
          <label>
            <?php ___("Chooise Class"); ?>
            <a href="<?php echo base_url("education/classes/add"); ?>" title="<?php ___("Add New"); ?>"><i class="fa fa-plus-circle"></i></a>
          </label>
        <?php
        $class_option = array(
            'label' => __("Class") . ' <a href="' . base_url("education/classes/add") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->classes
        );
        $bsForm->add_element("class_id", BSFORM_SELECT, 0, $class_option);
        $bsForm->form_elements(TRUE);
        ?>
        </div>

        <?php
        $bsForm->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$bsForm->form_end(TRUE);
?>

<script>
$(function() {
  $("#em-reason_type").change(function() {
    if ($(this).val() == 1) {
      $("#education_class_field").show();
    } else {
      $("#education_class_field").hide();
    }
  })
})
</script>