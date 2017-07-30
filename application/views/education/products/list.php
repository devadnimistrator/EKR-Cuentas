<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Education"); ?>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Products"); ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("education/products/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$status_options = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Products"); ?>
          <small>
            <?php ___("Category"); ?>:
            <select id="filter_category" onchange="reloadTable(true)">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach ($this->categories as $id => $name): ?>
                <option value="<?php echo $id; ?>" <?php if ($category == "" . $id) echo "selected"; ?>><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>
          </small>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-products" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th class="nosort"><?php ___("Category") ?>:</th>
              <th><?php ___("Name") ?>:</th>
              <!--th><?php ___("Price(%s)", my_get_currency_unit()) ?>:</th-->
              <th class="nosort"><?php ___("Actions") ?>:</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Datatables -->
<?php $this->load->view('js/table.php'); ?>
<script>
  function changeFilter() {
    location.href = "<?php echo base_url("education/products./index"); ?>/" + $("#filter_category").val();
  }

  var $tableProducts;
  $(document).ready(function () {
    $tableProducts = $('#table-products').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "category"},
        {"data": "name"},
        //{"data": "price"},
        {"data": "actions"},
      ],
      order: [[2, "asc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("education/products./ajax_find/" . $category); ?>",
        type: "POST",
        data: function (d) {
          return $.extend({}, d, {
            category_id: $("#filter_category").val()
          });
        }
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data['product_id']);

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('education/products./edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableProducts.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_product(id) {
    if (confirm("<?php ___("Are you sure delete selected product?"); ?>")) {
      $.get("<?php echo base_url('education/products/ajax_delete') ?>/" + id, function () {
        reloadTable(false);
      })
    }
  }
</script>