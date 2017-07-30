<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("payments"); ?>"><?php ___("Payments"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/income/"); ?>"><?php ___("Income"); ?></a> </small>
      <?php if ($this->payment_history_m->is_exists()): ?>
        <small><i class="fa fa-angle-double-right"></i> <?php ___("Edit Income"); ?> #<?php echo $this->payment_history_m->id; ?></small>
      <?php else: ?>
        <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("payments/income/add/first"); ?>"><?php ___("New regist"); ?></a></small>
        <small><i class="fa fa-angle-double-right"></i> <?php ___("Step2"); ?></small>
      <?php endif; ?>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <?php if ($this->payment_history_m->is_exists()): ?>
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("payments/income/add/first"); ?>'"><?php ___("Add New"); ?></button>
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

<div class="row">
  <div class="col-lg-6 col-md-9 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Chooise Product"); ?></h2>
        <div class="pull-right">
          <button class="btn btn-xs btn-primary"  data-toggle="modal" data-target="#ReceiptModal"><i class="fa fa-list-alt"></i> <?php ___("Receipt"); ?></button>
        </div>
        <div class="clearfix"></div>
      </div>

      <?php
      $formConfig = array(
          "name" => "editPayment",
          "autocomplete" => false,
          "col_width" => 2
      );

      $bsForm = new My_bs_form($formConfig);
      $bsForm->form_start(TRUE);
      $bsForm->add_element("action", BSFORM_HIDDEN, "process");
      ?>

      <div class="x_content">
        <?php
        $category_option = array(
            'label' => __("Category") . ' <a href="' . base_url("tangible/categories") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->categories
        );
        $bsForm->add_element("category_id", BSFORM_SELECT, $this->tangible_payment_m->category_id, $category_option);

        $product_option = array(
            'label' => __("Product") . ' <a href="' . base_url("tangible/products/add") . '" title="' . __("Add New") . '"><i class="fa fa-plus-circle"></i></a>',
            'required' => true,
            'options' => $this->products
        );
        $bsForm->add_element("product_id", BSFORM_SELECT, $this->tangible_payment_m->product_id, $product_option);

        $bsForm->form_elements(TRUE);
        ?>
        <div class="clearfix"></div>
      </div>

      <div class="x_title">
        <h2><?php ___("Payment Information"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="row">
            <div class="item form-group" id="form-group-discount">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-discount"><?php ___("Discount"); ?> <span class="required">:</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 control-input">
                <div class="input-group control-input">
                  <input type="number" id="em-discount" name="discount" value="<?php echo $this->payment_history_m->discount; ?>" class="form-control" autocomplete="off" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) echo "disabled"; ?> onchange="calcAmount()">
                  <div class="input-group-btn">
                    <select name="discount_type" id="em-discount_type" class="form-control" style="width: 100px; padding-left: 3px; padding-right: 0;" onchange="chooiseDicsountType(this.value)">
                      <option value="<?php echo DISCOUNT_TYPE_NO; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_NO) echo "selected"; ?>><?php ___("No"); ?></option>
                      <option value="<?php echo DISCOUNT_TYPE_FIXED; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_FIXED) echo "selected"; ?>><?php ___("(%s)Fixed", my_get_currency_unit(DEFAULT_CURRENCY_UNIT, "string")); ?></option>
                      <option value="<?php echo DISCOUNT_TYPE_PERCENT; ?>" <?php if ($this->payment_history_m->discount_type == DISCOUNT_TYPE_PERCENT) echo "selected"; ?>><?php ___("(%)Percent"); ?></option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="item form-group" id="form-group-before_amount">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-amount"><?php ___("Cost for pay"); ?> <span class="required">:</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 has-feedback control-input">
                <input type="hidden" id="em-before_amount" name="before_amount" value="<?php echo $this->payment_history_m->before_amount; ?>" />
                <input type="number" id="em-amount" name="amount" value="<?php echo $this->payment_history_m->amount; ?>" required="required" class="form-control has-feedback-left" autocomplete="off" readonly />
                <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="item form-group" id="form-group-payment">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-paid_amount"><?php ___("Paid Cost"); ?> <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 has-feedback control-input">
                <input type="number" id="em-paid_amount" name="paid_amount" value="<?php echo $this->payment_history_m->paid_amount; ?>" required="required" class="form-control has-feedback-left" autocomplete="off" onchange="calcAmount()" />
                <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="item form-group" id="form-group-payment">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-change"><?php ___("Change"); ?> <span class="required">:</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 has-feedback control-input">
                <input type="number" id="em-change" name="change" value="<?php echo $this->payment_history_m->paid_amount - $this->payment_history_m->amount; ?>" class="form-control has-feedback-left" readonly autocomplete="off" required="required" data-validate-minmax="0,"/>
                <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="row">
            <div class="item form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-payment_method_id"><?php ___("Method"); ?> <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 control-input">
                <select id="em-payment_method_id" name="payment_method_id" required="required" class="form-control col-xs-12">
                  <?php foreach ($this->payment_methods as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php if ($id == $this->payment_history_m->payment_method_id) echo "selected"; ?>><?php echo $name; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="item form-group">
              <label class = "control-label col-md-4 col-sm-4 col-xs-12" for = "em-pay_date"><?php ___("Registered"); ?> <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 control-input">
                <div class=" input-group">
                  <?php
                  $pay_date = date(DATE_FULL_FORMAT);
                  if ($this->payment_history_m->pay_date) {
                    $pay_date = my_formart_date($this->payment_history_m->pay_date, DATE_FULL_FORMAT);
                  }
                  ?>
                  <input type="text" id="em-pay_date" name="pay_date" value="<?php echo $pay_date; ?>" required="required" class="form-control date-picker" autocomplete="off" size="10">
                  <?php
                  $pay_h = substr($this->payment_history_m->pay_time, 0, 2);
                  $pay_m = substr($this->payment_history_m->pay_time, 3, 2);
                  ?>
                  <div class="input-group-btn" style="width: 100px; padding-left: 8px;">
                    <select name="pay_time_h" required="required" class="form-control" style="width: 42px; float: left; padding-left: 2px !important; padding-right: 0px !important;">
                      <?php
                      for ($h = 0; $h < 24; $h ++): $_h = str_pad($h, 2, "0", STR_PAD_LEFT);
                        ?>
                        <option value="<?php echo $_h; ?>" <?php if ($_h == $pay_h) echo "selected" ?>><?php echo $h; ?></option>
                      <?php endfor; ?>
                    </select>
                    <div style="float: left; padding: 5px 0; width: 8px; text-align: center; font-size: 14px;">:</div>
                    <select name="pay_time_m" required="required" class="form-control" style="width: 42px; float: left; padding-left: 2px !important; padding-right: 0px !important;">
                      <?php
                      for ($m = 0; $m < 60; $m ++): $_m = str_pad($m, 2, "0", STR_PAD_LEFT);
                        ?>
                        <option value="<?php echo $_m; ?>" <?php if ($_m == $pay_m) echo "selected" ?>><?php echo $_m; ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="item form-group">
              <label class="control-label col-md-4 col-sm-4 col-xs-12" for="em-payment_method_id"><?php ___("Status"); ?> <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12 control-input">
                <select id="em-payment_status" name="payment_status" required="required" class="form-control col-xs-12">
                  <option value="<?php echo PAYMENT_STATUS_PAID; ?>" <?php if (PAYMENT_STATUS_PAID == $this->payment_history_m->status) echo "selected"; ?>><?php ___("Paid"); ?></option>
                  <option value="<?php echo PAYMENT_STATUS_PENDING; ?>" <?php if (PAYMENT_STATUS_PENDING == $this->payment_history_m->status) echo "selected"; ?>><?php ___("Pending"); ?></option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class='clearfix'></div>

        <div class="ln_solid"></div>

        <?php
        $bsForm->form_buttons(TRUE);
        ?>
      </div>

      <?php
      $bsForm->form_end(TRUE);
      ?>
    </div>
    <div class="clearfix"></div>
  </div>
</div>



<script>
  $(function () {
    $("#form-editPayment button[type=reset]").click(function () {
      location.href = '<?php echo current_url(); ?>';
    })

    var currency_unit = '<?php echo my_get_currency_unit(DEFAULT_CURRENCY_UNIT, 'text'); ?>';
    $("#em-category_id").change(function () {
      $.getJSON("<?php echo site_url("app/ajax_get_procuts/" . PAYMENT_REASON_TYPE_TANGIBLE) ?>/" + $(this).val(), function (products) {
        var options = '';
        var first_product_id = 0;
        for (i = 0; i < products.length; i++) {
          if (first_product_id == 0) {
            first_product_id = products[i].id;
          }
          options += '<option value="' + products[i].id + '">' + currency_unit + products[i].price + ' - ' + products[i].name + '</option>';
        }
        $("#em-product_id").html(options);

        get_product_price(first_product_id);
      });
    })

    $("#em-product_id").change(function () {
      get_product_price($(this).val());
    });

<?php if (!$this->payment_history_m->is_exists()): ?>
      get_product_price($("#em-product_id").val());
<?php endif; ?>
  })

  function get_product_price(product_id) {
    if (product_id == 0) {
      $("#em-before_amount").val("");
      calcAmount();

    } else {
      $.get("<?php echo site_url("app/ajax_get_procut_price/" . PAYMENT_REASON_TYPE_TANGIBLE) ?>/" + $("#em-product_id").val(), function (price) {
        $("#em-before_amount").val(price);
        calcAmount();
      });
    }
  }
  function chooiseDicsountType(discount_type) {
    if (discount_type == '<?php echo DISCOUNT_TYPE_NO; ?>') {
      $("#em-discount").val('0.00');
      $("#em-discount").prop('disabled', true);
    } else {
      $("#em-discount").prop('disabled', false);
    }

    calcAmount();
  }

  function calcAmount() {
    var before_amount = $("#em-before_amount").val() * 1;
    if (before_amount <= 0) {
      $("#em-before_amount").val("");
      $("#em-amount").val("");
      $("#form-group-before_amount").addClass('bad');
      return;
    }

    var discount_type = $("#em-discount_type").val();
    $("#em-before_amount").val(before_amount);

    var discount = $("#em-discount").val() * 1;
    $("#em-discount").val(discount);
    if (discount < 0) {
      $("#em-paid_amount").val("");
      $("#form-group-before_amount").addClass('bad');
      return;
    }
    if (discount_type == '<?php echo DISCOUNT_TYPE_NO; ?>') {
      $("#em-amount").val(before_amount);
    } else if (discount_type == '<?php echo DISCOUNT_TYPE_FIXED; ?>') {
      if (discount >= before_amount) {
        $("#em-amount").val("");
        $("#form-group-before_amount").addClass('bad');
        return;
      }

      $("#em-amount").val(before_amount - discount);
    } else if (discount_type == '<?php echo DISCOUNT_TYPE_PERCENT; ?>') {
      if (discount > 90) {
        $("#em-paid_amount").val("");
        $("#form-group-before_amount").addClass('bad');
        return;
      }

      $("#em-amount").val(before_amount * (100.00 - discount) / 100.00);
    }

    var paid_amount = $("#em-paid_amount").val() * 1;
    var amount = $("#em-amount").val() * 1;
    $("#em-change").val(paid_amount - amount);
  }
</script>
