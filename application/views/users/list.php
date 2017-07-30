<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("users/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$user_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Cashiers"); ?>
          <small>
            <?php ___("Status"); ?>:
            <select id="user_status" onchange="reloadTable(true)">
              <option value="all">- <?php ___("All"); ?> -</option>
              <?php foreach ($user_status as $value => $text): ?>
                <option value="<?php echo $value; ?>" <?php if ($status == "" . $value) echo "selected"; ?>><?php ___($text); ?></option>
              <?php endforeach; ?>
            </select>
          </small>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-users" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("Username") ?>:</th>
              <th><?php ___("Full Name") ?>:</th>
              <th><?php ___("Email") ?>:</th>
              <th><?php ___("Phone") ?>:</th>
              <th><?php ___("Status") ?>:</th>
              <th><?php ___("Last Access") ?>:</th>
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
  var $tableUsers;
  $(document).ready(function () {
    $tableUsers = $('#table-users').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "username"},
        {"data": "fullname"},
        {"data": "email"},
        {"data": "phone"},
        {"data": "status"},
        {"data": "last_access"},
        {"data": "actions"},
      ],
      order: [[6, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("users/ajax_find/" . $status); ?>",
        type: 'POST',
        data: function (d) {
          return $.extend({}, d, {
            status: $("#user_status").val()
          });
        }
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data['user_id']);

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('users/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_user(userid) {
    if (confirm("<?php ___("Are you sure delete selected user?"); ?>")) {
      $.get("<?php echo base_url('users/ajax_delete') ?>/" + userid, function () {
        reloadTable(false);
      })
    }
  }
</script>