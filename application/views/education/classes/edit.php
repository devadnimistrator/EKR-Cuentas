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

<div class="row">
  <div class="col-md-6 col-sm-12 col-xs-12">
    <?php
    $formConfig = array(
        "name" => "editClass",
        "autocomplete" => false,
        "col_width" => 2
    );

    $this->education_class_m->form_create($formConfig);
    $this->education_class_m->bs_form->form_start(TRUE);
    ?>
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Chooise Product"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $this->education_class_m->form_add_element("category_id", array('type' => 'select', 'options' => $this->categories));
        $this->education_class_m->form_add_element("product_id", array('type' => 'select', 'options' => array()));
        //$this->education_class_m->form_add_element("start_datetime");

        $this->education_class_m->bs_form->form_elements(TRUE);
        ?>
        <div class="clearfix"></div>
      </div>

      <div class="x_title">
        <h2><?php ___("Schedule"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Start"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
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

        <div class="form-group" id="form-group-before_amount">
          <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Status"); ?> <span class="required">*</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
            <?php
            $status = array(
                "plan" => __("Plan"),
                "running" => __("Running"),
                "end" => __("End"),
                "cancel" => __("Cancel"),
            );
            echo form_dropdown(array("name" => "status", "class" => "form-control col-xs-12", "onchange" => "changeStatus()"), $status, $this->education_class_m->status);
            ?>
          </div>

          <label class="control-label col-md-2 col-sm-2 col-xs-12 text-right ended_field" style="<?php if ($this->education_class_m->status != 'end') echo "display:none"; ?>"><?php ___("End"); ?> <span>:</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input item ended_field" style="<?php if ($this->education_class_m->status != 'end') echo "display:none"; ?>">
            <input type="text" value="<?php echo $this->education_class_m->end_datetime; ?>" name="end_datetime" class="form-control datetime-picker" autocomplete="off">
            <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
          </div>

          <label class="control-label col-md-2 col-sm-2 col-xs-12 text-right canceled_field" style="<?php if ($this->education_class_m->status != 'cancel') echo "display:none"; ?>"><?php ___("Cancel"); ?> <span>:</span></label>
          <div class="col-md-4 col-sm-4 col-xs-12 control-input canceled_field" style="<?php if ($this->education_class_m->status != 'cancel') echo "display:none"; ?>">
            <input type="text" value="<?php echo $this->education_class_m->cancel_date; ?>" name="cancel_date" class="form-control date-picker" autocomplete="off">
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
          <?php
          $class_steps = $this->education_class_step_m->get_by_class_id($this->education_class_m->id);
          $step = 0;
          ?>
          <?php foreach ($class_steps as $class_step): $step ++; ?>
            <div class="cost-step cost-step-<?php echo $step; ?>">
              <input type="hidden" name="step_id[]" value="<?php echo $class_step->id; ?>" />
              <div class="form-group" id="form-group-before_amount">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">
                  <?php ___("Cost"); ?> 
                  <?php if ($step == 1) : ?>
                    <span class="required">*</span>
                  <?php else: ?>
                    <a href="#" onclick="deleteCostStep(<?php echo $step; ?>, <?php echo $class_step->id; ?>)" class="delete-cost-step red" title="<?php ___("Remove"); ?>"><i class="fa fa-minus-circle"></i></a>
                  <?php endif; ?>
                </label>
                <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
                  <span class="fa fa-dollar form-control-feedback left" aria-hidden="true"></span>
                  <input type="number" name="cost[]" value="<?php echo $class_step->cost; ?>" required="required" class="form-control has-feedback-left" autocomplete="off">
                </div>
                <label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Due Date"); ?> <span class="required">*</span></label>
                <div class="col-md-4 col-sm-4 col-xs-12 control-input item">
                  <input type="text" name="due_date[]" value="<?php echo my_formart_date($class_step->due_date, DATE_FULL_FORMAT); ?>" class="form-control date-picker" required="required" autocomplete="off">
                  <span class="fa fa-calendar form-control-feedback right" aria-hidden="true"></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
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

    <?php
    $this->education_class_m->bs_form->form_end(TRUE);
    ?>
  </div>

  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Students"); ?></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li>
            <button data-toggle="modal" data-target="#AddStudentModal" class="btn btn-primary btn-xs">
              <i class="fa fa-plus-circle"></i> <?php ___("Add"); ?>
            </button>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="class-students" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th width="30"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Name") ?>:</th>
              <th><?php ___("Total Paid(%s)", my_get_currency_unit()) ?>:</th>
              <th><?php ___("Status") ?>:</th>
              <th><?php ___("Actions") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- student add modal -->
<div id="AddStudentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><?php ___("Chooise Student"); ?></h4>
      </div>
      <div class="modal-body">
        <table id="table-students" class="table table-hover" width="100%">
          <thead>
            <tr>
              <th><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Name") ?>:</th>
              <th><?php ___("Email") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- /student add modal -->

<!-- pay history modal -->
<div id="PayHistoryButton" data-toggle="modal" data-target="#PayHistoryModal"></div>
<div id="PayHistoryModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><?php ___("Payment Histories"); ?></h4>
      </div>
      <div class="modal-body">
        <table id="table-payment-histories" class="table table-hover" width="100%">
          <thead>
            <tr>
              <th><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Date") ?>:</th>
              <th><?php ___("Amount") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- /pay history modal -->

<script>
  var class_id = <?php echo $this->education_class_m->id; ?>;
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
            + '<input type="hidden" name="step_id[]" value="0" />'
            + '<div class="form-group" id="form-group-before_amount">'
            + '<label class="control-label col-md-2 col-sm-2 col-xs-12"><?php ___("Cost"); ?> '
            + '<a href="#" onclick="deleteCostStep(' + step + ', 0)" class="delete-cost-step red" title="<?php ___("Remove"); ?>"><i class="fa fa-minus-circle"></i></a>'
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

  function deleteCostStep(step, step_id) {
    if (!confirm("<?php ___("Are you sure delete selected step?"); ?>")) {
      return;
    }

    if (step_id > 0) {
      $.post("<?php echo site_url("education/classes/ajax_delete_step"); ?>", {
        "step_id": step_id
      }, function (result) {
        if (result == 'ok') {
          $("#cost-steps .cost-step-" + step).remove();
        }
      });
    } else {
      $("#cost-steps .cost-step-" + step).remove();
    }
  }

  function chooiseStudent(student_id) {
    $.post("<?php echo site_url("education/classes/ajax_add_student"); ?>", {
      "class_id": class_id,
      "student_id": student_id
    }, function (result) {
      if (result == 'ok') {
        reloadClassStudent(true);
      }
    });
  }

  function deleteStudent(student_id) {
    if (!confirm("<?php ___("Are you sure delete selected student from this class?"); ?>")) {
      return;
    }

    $.post("<?php echo site_url("education/classes/ajax_delete_student"); ?>", {
      "student_id": student_id
    }, function () {
      reloadClassStudent(true);
    });
  }

  function holdStudent(student_id) {
    $.post("<?php echo site_url("education/classes/ajax_hold_student"); ?>", {
      "student_id": student_id
    }, function () {
      reloadClassStudent(false);
    });
  }

  function unholdStudent(student_id) {
    $.post("<?php echo site_url("education/classes/ajax_unhold_student"); ?>", {
      "student_id": student_id
    }, function () {
      reloadClassStudent(false);
    });
  }

  function changeStatus() {
    if (document.editClass.status.value == 'cancel') {
      $(".canceled_field").show();
    } else {
      $(".canceled_field").hide();
    }

    if (document.editClass.status.value == 'end') {
      $(".ended_field").show();
    } else {
      $(".ended_field").hide();
    }
  }
</script>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  var $tableStudent, $classStudent, $paymentHistories;
  var selected_student_id = 0;
  $(function () {
    $tableStudent = $('#table-students').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "first_name"},
        {"data": "email"}
      ],
      ordering: false,
      processing: true,
      serverSide: true,
      responsive: true,
      ajax: {
        url: "<?php echo base_url("students/ajax_find"); ?>",
        type: 'POST'
      },
      createdRow: function (row, data, index) {
        $('td', row).eq(1).html('<a href="#" onclick="chooiseStudent(' + data.student_id + ')"><i class="fa fa-plus-circle"></i> ' + data.first_name + ' ' + data.last_name + '</a>');
      },
    });

    $classStudent = $('#class-students').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "name"},
        {"data": "amount"},
        {"data": "status"},
        {"data": "actions"},
      ],
      ordering: false,
      processing: true,
      serverSide: true,
      responsive: true,
      pageLength: 25,
      ajax: {
        url: "<?php echo base_url("education/classes/ajax_get_students/" . $this->education_class_m->id); ?>",
        type: 'POST'
      }
    });

    $paymentHistories = $('#table-payment-histories').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "date"},
        {"data": "amount"}
      ],
      paging: false,
      ordering: false,
      processing: true,
      serverSide: true,
      responsive: true,
      info: false,
      bFilter: false,
      ajax: {
        url: "<?php echo base_url("education/classes/ajax_get_payment_histories"); ?>",
        type: 'POST',
        data: function (d) {
          return {
            class_id: class_id,
            class_student_id: selected_student_id
          };
        }
      }
    });
  })

  function reloadClassStudent(refresh) {
    $classStudent.ajax.reload(function () {
    }, refresh);
  }

  function show_pay_histories(student_id) {
    selected_student_id = student_id;
    $("#PayHistoryButton").click();
    $paymentHistories.ajax.reload(function () {
    }, true);
  }
</script>