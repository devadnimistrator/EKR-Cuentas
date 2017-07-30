<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <?php ___("Payments"); ?>
      <small><i class="fa fa-angle-double-right"></i> <a href="<?php echo $this->before_called_url; ?>"><?php ___("Expenses"); ?></a> </small>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Companies"); ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("expenses/companies/add"); ?>'"><?php ___("Add New"); ?></button>
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
          <?php ___("Companies"); ?>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-companies" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Name") ?>:</th>
              <th><?php ___("RFC") ?>:</th>
              <th><?php ___("Telphone") ?>:</th>
              <th><?php ___("Email") ?>:</th>
              <th><?php ___("Address") ?>:</th>
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
  var $tableCompanies;
  $(document).ready(function () {
    $tableCompanies = $('#table-companies').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "name"},
        {"data": "taxnumber"},
        {"data": "telphone"},
        {"data": "email"},
        {"data": "address"},
        {"data": "actions"},
      ],
      order: [[1, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("expenses/companies/ajax_find"); ?>",
        type: "POST"
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data['company_id']);

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('expenses/companies/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function delete_company(id) {
    if (confirm("<?php ___("Are you sure delete selected company?"); ?>")) {
      $.get("<?php echo base_url('expenses/companies/ajax_delete') ?>/" + id, function () {
        $tableCompanies.ajax.reload(function () {
        }, false);
      })
    }
  }
</script>