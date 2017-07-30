<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("payments"); ?>"><?php ___("Payments"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/expenses"); ?>"><?php ___("Expenses"); ?></a> </small>
      <small><i class="fa fa-angle-double-right"></i> <?php echo $this->page_title; ?></small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <?php if ($this->payment_history_m->is_exists()): ?>
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/expenses/add"); ?>'"><?php ___("Add New"); ?></button>
      <?php endif; ?>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class=" col-sm-12 col-xs-12">
    <?php
    my_show_msg($error_msgs, "danger");

    my_show_system_message("success");
    ?>
  </div>
</div>
<?php
$formConfig = array(
    "name" => "editPayment",
    "autocomplete" => false,
    "col_width" => 3
);

$bsForm = new My_bs_form($formConfig);
$bsForm->form_start(TRUE);
$bsForm->add_element("action", BSFORM_HIDDEN, "process");
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
        $company_option = array(
            'label' => __("Company") . ' <a href="' . base_url("expenses/companies/add") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->companies
        );
        $bsForm->add_element("company_id", BSFORM_SELECT, $this->expenses_payment_m->company_id, $company_option);

        $category_option = array(
            'label' => __("Category") . ' <a href="' . base_url("expenses/categories") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->categories
        );
        $bsForm->add_element("category_id", BSFORM_SELECT, $this->expenses_payment_m->category_id, $category_option);

        $product_option = array(
            'label' => __("Product") . ' <a href="' . base_url("expenses/products/add") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->products
        );
        $bsForm->add_element("product_id", BSFORM_SELECT, $this->expenses_payment_m->product_id, $product_option);

        $bsForm->form_elements(TRUE);
        ?>
        <div class="clearfix"></div>
      </div>
      <div class="x_title">
        <h2><?php ___("Payment Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $bsForm->add_element("amount", BSFORM_NUMBER, $this->payment_history_m->amount, array("label" => __("Amount(%s)", my_get_currency_unit()), "required" => true));
        $bsForm->form_elements(TRUE);
        ?>

        <div class="item form-group">
          <label class="control-label col-md-3 col-sm-4 col-xs-12" for="em-pay_date"><?php ___("Registered"); ?> <span class="required">*</span></label>
          <div class="col-md-9 col-sm-8 col-xs-12 control-input">
            <?php
            $pay_date = date(DATE_FULL_FORMAT);
            if ($this->payment_history_m->pay_date) {
              $pay_date = my_formart_date($this->payment_history_m->pay_date, DATE_FULL_FORMAT);
            }
            ?>
            <input type="text" id="em-pay_date" name="pay_date" value="<?php echo $pay_date; ?>" required="required" class="form-control date-picker" autocomplete="off" size="10" style="width:100px; float:left; margin-right:20px;">
            <?php
            $pay_h = substr($this->payment_history_m->pay_time, 0, 2);
            $pay_m = substr($this->payment_history_m->pay_time, 3, 2);
            ?>
            <select name="pay_time_h" required="required" class="form-control" style="width: 50px; float: left; padding-left: 5px !important; padding-right: 5px !important;">
              <?php for ($h = 0; $h < 24; $h ++): $_h = str_pad($h, 2, "0", STR_PAD_LEFT); ?>
                <option value="<?php echo $_h; ?>" <?php if ($_h == $pay_h) echo "selected" ?>><?php echo $h; ?></option>
              <?php endfor; ?>
            </select>
            <div style="float: left; padding: 5px 2px;">:</div>
            <select name="pay_time_m" required="required" class="form-control" style="width: 50px; float: left; padding-left: 5px !important; padding-right: 5px !important;">
              <?php for ($m = 0; $m < 60; $m ++): $_m = str_pad($m, 2, "0", STR_PAD_LEFT); ?>
                <option value="<?php echo $_m; ?>" <?php if ($_m == $pay_m) echo "selected" ?>><?php echo $_m; ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <?php
        $payment_method_option = array(
            'label' => __("Method"),
            'required' => true,
            'options' => $this->payment_methods
        );

        $bsForm->add_element("payment_method_id", BSFORM_SELECT, $this->payment_history_m->payment_method_id, $payment_method_option);

        $bsForm->form_elements(TRUE);
        ?>

        <div class="ln_solid"></div>

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
  $(function () {
    $("#form-editPayment button[type=reset]").click(function () {
      location.href = '<?php echo current_url(); ?>';
    })

    var currency_unit = '<?php echo my_get_currency_unit(DEFAULT_CURRENCY_UNIT, 'text'); ?>';
    $("#em-category_id").change(function () {
      $.getJSON("<?php echo site_url("app/ajax_get_procuts/" . PAYMENT_REASON_TYPE_EXPENSES) ?>/" + $(this).val(), function (products) {
        var options = '';
        var first_product_id = 0;
        for (i = 0; i < products.length; i++) {
          if (first_product_id == 0) {
            first_product_id = products[i].id;
          }
          options += '<option value="' + products[i].id + '">' + products[i].name + '</option>';
        }
        $("#em-product_id").html(options);
      });
    })
  })
</script>
