<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Payments"); ?>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo $this->before_called_url; ?>"><?php ___("Expenses"); ?></a> </small>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("expenses/companies");?>"><?php ___("Companies"); ?></a> </small>
      <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <?php if ($expenses_company_m->is_exists()): ?>
      <button type="button" class="btn btn-primary btn-round btn-sm" onclick="location.href = '<?php echo base_url("expenses/companies/add"); ?>'"><?php ___("Add New"); ?></button>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <?php
    $expenses_company_m->show_errors();
    $expenses_company_m->show_msgs();
    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "addCompany",
    "autocomplete" => false
);

$expenses_company_m->form_create($formConfig);
$expenses_company_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Company Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $expenses_company_m->form_add_element("name");
        $expenses_company_m->form_add_element("taxnumber");
        $expenses_company_m->form_add_element("telphone");
        $expenses_company_m->form_add_element("email");

        $expenses_company_m->bs_form->form_elements(TRUE);
        ?>
      </div>
      <div class="x_title">
        <h2><?php ___("Address Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">  
        <?php
        $state_options = $expenses_company_m->state();
        $state_options['type'] = 'select';
        $state_options['options'] = $this->geo_states;

        $expenses_company_m->form_add_element("street");
        $expenses_company_m->form_add_element("street_number");
        $expenses_company_m->form_add_element("suburb");
        $expenses_company_m->form_add_element("state", $state_options);
        $expenses_company_m->form_add_element("city");
        $expenses_company_m->form_add_element("zipcode");

        $expenses_company_m->bs_form->form_elements(TRUE);
        ?>

        <div class="item form-group" id="form-group-city">
          <label class="control-label col-md-4 col-sm-5" for="em-city"><?php ___("Country"); ?></label>
          <div class="col-md-8 col-sm-7 control-input" style="padding-top: 8px">
            <?php ___($geo_state_m->country_name); ?>
          </div>
        </div>
        
        <div class="ln_solid"></div>

        <?php
        $expenses_company_m->bs_form->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$expenses_company_m->bs_form->form_end(TRUE);
