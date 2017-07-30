<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("education/calendar"); ?>"><?php ___("Education"); ?></a>
      <small>
        <i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("education/classes"); ?>"><?php ___("Classes"); ?></a> 
        <i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?>
      </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <?php if ($this->education_class_m->is_exists()): ?>
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("education/classes/add"); ?>'"><?php ___("Add New"); ?></button>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class=" col-sm-12 col-xs-12">
    <?php
    $this->education_class_m->show_errors();
    $this->education_class_m->show_msgs();

    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editClass",
    "autocomplete" => false,
    "col_width" => 2
);

$this->education_class_m->form_create($formConfig);
$this->education_class_m->bs_form->form_start(TRUE);
?>

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Chooise Product"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $this->education_class_m->form_add_element("category_id", array('type' => 'select', 'options' => $this->categories));
        $this->education_class_m->form_add_element("product_id", array('type' => 'select', 'options' => array()));
        
        $this->education_class_m->bs_form->form_elements(TRUE);
        ?>

        <div class="form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Start"); ?> <span class="required">*</span></label>
          <div class="col-md-10 col-sm-10 col-xs-12 control-input item">
            <?php
              $start_datetime = "";
              if ($this->education_class_m->start_datetime) {
                $start_datetime = my_formart_date($this->education_class_m->start_datetime, DATETIME1_FULL_FORMAT);
              }
            ?>
            <input type="text" value="<?php echo $start_datetime; ?>" name="start_datetime" required="required" class="form-control datetime-picker" autocomplete="off">
            <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
          </div>
        </div>

        <div class="clearfix"></div>
      </div>

      <div class="x_title">
        <h2><?php ___("Payment Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="item form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Fee"); ?></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input">
            <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
            <input type="number" name="registration_fee" value="<?php echo $this->education_class_m->registration_fee; ?>" class="form-control has-feedback-left" autocomplete="off">
          </div>
        </div>

        <div id="cost-steps">
          <div class="cost-step cost-step-1">
            <div class="form-group" id="form-group-before_amount">
              <label class="control-label col-md-2 col-sm-2 col-xs-12">
                <?php ___("Cost"); ?> 
                <span class="required">*</span>
              </label>
              <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
                <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
                <input type="number" name="cost[]" value="" required="required" class="form-control has-feedback-left" autocomplete="off">
              </div>
              <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Due Date"); ?> <span class="required">*</span></label>
              <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
                <input type="text" value="" name="due_date[]" class="form-control date-picker" required="required" autocomplete="off">
                <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="item form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12">
            <a href="#" onclick="addCostStep()" class="plus-cost-icon"><i class="fa fa-plus-circle"></i> <?php ___("Add Cost"); ?></a>
          </label>
        </div>

        <div class="ln_solid"></div>

        <?php
        $this->education_class_m->bs_form->form_buttons(TRUE);
        ?>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>

<?php
$this->education_class_m->bs_form->form_end(TRUE);
?>

<script>
  $(function () {
    $("#form-editPayment button[type=reset]").click(function () {
      location.href = '<?php echo current_url(); ?>';
    })

    $("#em-category_id").change(function () {
      changeCategory($(this).val());
    });

    changeCategory($("#em-category_id").val());
  })

  function changeCategory(category_id) {
    $.getJSON("<?php echo site_url("app/ajax_get_procuts/" . PAYMENT_REASON_TYPE_EDUCATION) ?>/" + category_id, function (products) {
      var options = '';
      for (i = 0; i < products.length; i++) {
        options += '<option value="' + products[i].id + '">' + products[i].name + '</option>';
      }
      $("#em-product_id").html(options);
    });
  }

  function addCostStep() {
    var step = $(".cost-step").length;
    step++;

    var div_new_step = '<div class="cost-step cost-step-' + step + '">'
            + '<div class="form-group" id="form-group-before_amount">'
            + '<label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Cost"); ?> '
            + '<a href="#" onclick="deleteCostStep(' + step + ')" class="delete-cost-step red" title="<?php ___("Remove"); ?>"><i class="fa fa-minus-circle"></i></a>'
            + '</label>'
            + '<div class="col-md-4 col-sm-4 col-xs-12 control-input item">'
            + '<span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>'
            + '<input type="number" name="cost[]" value="" class="form-control has-feedback-left" autocomplete="off">'
            + '</div>'
            + '<label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Due Date"); ?> <span class="required">:</span></label>'
            + '<div class="col-md-4 col-sm-4 col-xs-12 control-input item">'
            + '<input type="text" value="" name="due_date[]" class="form-control date-picker" autocomplete="off">'
            + '<span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>'
            + '</div>'
            + '</div>'
            + '</div>';

    $("#cost-steps").append(div_new_step);

    $('input.date-picker').datepicker({
      language: my_js_options.language,
      format: my_js_options.date_full_format,
      todayBtn: "linked",
      autoclose: true
    });
  }
  
  function deleteCostStep(step) {
    $("#cost-steps .cost-step-" + step).remove();
  }
</script>
