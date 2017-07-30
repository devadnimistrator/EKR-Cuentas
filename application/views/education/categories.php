<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php my_load_css("plugins/bootstrap-treeview/bootstrap-treeview.min.css"); ?>

<style>
  #btnDeleteCategory {
    display: none;
  }
</style>

<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Education"); ?>
      <small>
        <i class="fa fa-angle-double-right"></i> <a href="<?php echo base_url("education/products"); ?>"><?php ___("Products"); ?></a>
        <i class="fa fa-angle-double-right"></i> <?php echo $this->page_title ?>
      </small>
    </h3>
  </div>
  <?php if ($this->before_called_url): ?>
    <div class="title_right">
      <div class="pull-right">
        <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo $this->before_called_url; ?>'"><?php ___("Back"); ?></button>
      </div>
    </div>
  <?php endif; ?>
</div>
<div class="clearfix"></div>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="system-message">
    <?php
    $category_m->show_errors();
    $category_m->show_msgs();
    ?>
  </div>
</div>

<div class="row">
  <div class="col-md-4 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2 id="sub-title">
          <?php ___("Add New Category") ?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $formConfig = array(
            "name" => "edtiCategory",
            "autocomplete" => false,
            "ajaxcall" => "saveCategory"
        );

        $category_m->form_create($formConfig);
        $category_m->form_add_element('category_id', array("type" => 'hidden'));
        $category_m->form_add_element('parent_id');
        $category_m->form_add_element('name');
        $category_m->bs_form->add_button("submit", __("Submit"), array("class" => "btn btn-primary"));
        $category_m->bs_form->add_button("button", __("Delete"), array("onclick" => "deleteCategory()", "class" => "btn btn-danger", "id" => "btnDeleteCategory"));
        $category_m->form_generate();
        ?>
      </div>
    </div>
  </div>

  <div class="col-md-8 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Categories"); ?>
        </h2>
        
        <ul class="nav navbar-right panel_toolbox">
          <li>
            <button class="btn btn-primary btn-xs" onclick="location.href='<?php echo base_url("education/categories"); ?>'">
              <?php ___("Add New"); ?>
            </button>
          </li>
        </ul>
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
  function saveCategory() {
    var f = document.edtiCategory;

    $.post("<?php echo base_url("education/categories/ajax_save_category"); ?>", $("#form-edtiCategory").serialize(), function (message) {
      $("#system-message").html(message);

      initView();

      initCategoryInput();
    });
  }

  function deleteCategory() {
    if (!confirm("<?php ___("Are you sure delete?") ?>")) {
      return;
    }
    $.get("<?php echo base_url("education/categories/ajax_delete_category"); ?>/" + treeViewOptions.selectedNode.id, function (message) {
      $("#system-message").html(message);

      initView();

      initCategoryInput();
    });
  }

  function initCategoryInput() {
    $("#sub-title").text("<?php ___("Add New Category"); ?>");
    $("#em-category_id").val("");
    $("#em-parent_id").val(0);
    $("#em-name").val("");
    $("#btnDeleteCategory").hide();

    treeViewOptions.selectedNode = false;
  }

  var treeViewOptions = {
    highlightSelected: true,
    levels: 2,
    data: null,
    onNodeSelected: function (event, node) {
      $("#sub-title").text("<?php ___("Edit Category"); ?> #" + node.id);
      $("#em-category_id").val(node.id);
      $("#em-parent_id").val(node.parent_id);
      $("#em-name").val(node.text);
      $("#btnDeleteCategory").show();

      treeViewOptions.selectedNode = node;

      location.href = "#sub-title";
    },
    onNodeUnselected: function (event, node) {
      initCategoryInput();
    },
    showTags: true,
    selectedNode: null
  }

  $(function () {
    initView();
  });

  function initView() {
    $.getJSON("<?php echo base_url("education/categories/ajax_get_parent_categories"); ?>", function (categories) {
      var old_parent = $("#em-parent_id").val();
      $("#em-parent_id").empty();
      categories.forEach(function (category) {
        $("#em-parent_id").append('<option value="' + category.id + '">' + category.name + '</options>');
      });
      if (old_parent) {
        $("#em-parent_id").val(old_parent);
      }
    });

    $.getJSON("<?php echo base_url("education/categories/ajax_get_all_categories"); ?>", function (categories) {
      treeViewOptions.data = categories;

      $('#categories').empty();
      $('#categories').treeview(treeViewOptions);
    });
  }
</script>