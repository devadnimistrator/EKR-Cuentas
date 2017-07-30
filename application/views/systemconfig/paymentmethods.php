<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php my_load_css("plugins/bootstrap-treeview/bootstrap-treeview.min.css"); ?>

<style>
  #btnDeleteMethod {
    display: none;
  }
</style>

<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("systemconfig/paymentmethods"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
    $payment_method_m->show_errors();
    $payment_method_m->show_msgs();
    ?>
  </div>
</div>

<div class="row">
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2 id="sub-title">
          <?php ___("Add New Method") ?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
            "name" => "edtiMethod",
            "autocomplete" => false,
            "ajaxcall" => "saveMethod"
        );

        $payment_method_m->form_create($formConfig);
        $payment_method_m->form_add_element('method_id', array("type" => 'hidden'));
        $payment_method_m->form_add_element('name');
        $payment_method_m->form_add_element('type', array("type" => 'select', "options" => my_get_payment_methods()));
        $payment_method_m->bs_form->add_button("submit", __("Submit"), array("class" => "btn btn-primary"));
        $payment_method_m->bs_form->add_button("button", __("Delete"), array("onclick" => "deleteMethod()", "class" => "btn btn-danger", "id" => "btnDeleteMethod"));
        $payment_method_m->form_generate();
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-8 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Payment Methods"); ?>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div id="categories" class=""></div>
      </div>
    </div>
  </div>
</div>

<?php my_load_js("plugins/bootstrap-treeview/bootstrap-treeview.min.js"); ?>

<script>
  function saveMethod() {
    var f = document.edtiMethod;

    $.post("<?php echo base_url("systemconfig/ajax_save_method"); ?>", $("#form-edtiMethod").serialize(), function (message) {
      $("#system-message").html(message);

      initView();

      initMethodInput();
    });
  }

  function deleteMethod() {
    if (!confirm("<?php ___("Are you sure delete?") ?>")) {
      return;
    }
    $.get("<?php echo base_url("systemconfig/ajax_delete_method"); ?>/" + treeViewOptions.selectedNode.id, function (message) {
      $("#system-message").html(message);

      initView();

      initMethodInput();
    });
  }

  function initMethodInput() {
    $("#sub-title").text("<?php ___("Add New Method"); ?>");
    $("#em-method_id").val("");
    $("#em-name").val("");
    $("#btnDeleteMethod").hide();

    treeViewOptions.selectedNode = false;
  }

  var treeViewOptions = {
    highlightSelected: true,
    levels: 2,
    data: null,
    onNodeSelected: function (event, node) {
      $("#sub-title").text("<?php ___("Edit Method"); ?> #" + node.id);
      $("#em-method_id").val(node.id);
      $("#em-name").val(node.text);
      $("#em-type").val(node.type);
      $("#btnDeleteMethod").show();

      treeViewOptions.selectedNode = node;

      location.href = "#sub-title";
    },
    onNodeUnselected: function (event, node) {
      initMethodInput();
    },
    showTags: true,
    selectedNode: null
  }

  $(function () {
    initView();
  });

  function initView() {
    $.getJSON("<?php echo base_url("systemconfig/ajax_get_all_methods"); ?>", function (categories) {
      treeViewOptions.data = categories;

      $('#categories').empty();
      $('#categories').treeview(treeViewOptions);
    });
  }
</script>