<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("students"); ?>"><?php ___("Student"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
    </h3>
  </div>
  <?php if ($student_m->is_exists()): ?>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("students/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
  <?php endif; ?>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <?php
    $student_m->show_errors();
    $student_m->show_msgs();
    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "addStudent",
    "autocomplete" => false
);

$student_m->form_create($formConfig);

$student_m->form_create($formConfig);
$student_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Account Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $student_m->form_add_element("first_name");
        $student_m->form_add_element("last_name");
        $student_m->form_add_element("email");
        $student_m->form_add_element("phone");
        $student_m->form_add_element("cellphone");
        $student_m->form_add_element("invoice_email");
        $student_m->bs_form->form_elements(TRUE);
        ?>
      </div>
      
      <div class="x_title">
        <h2><?php ___("Other Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $state_options = $student_m->state();
        $state_options['type'] = 'select';
        $state_options['options'] = $this->geo_states;
        
        $student_m->form_add_element("company_name");
        $student_m->form_add_element("tax_payer_number");
        $student_m->form_add_element("address");
        $student_m->form_add_element("state", $state_options);
        $student_m->form_add_element("city");
        $student_m->form_add_element("zipcode");
        $student_m->bs_form->form_elements(TRUE);
        ?>
        
        <div class="ln_solid"></div>
        
        <?php
        $student_m->bs_form->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$student_m->bs_form->form_end(TRUE);