<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3><?php echo $this->page_title ?></h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("students/add"); ?>'"><?php ___("Add New"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>


<?php
$student_status = $this->config->item("data_status");
?>

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2><?php ___("Student"); ?></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="table-students" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort"><i class="fa fa-list-ol"></i></th>
              <th><?php ___("First Name") ?>:</th>
              <th><?php ___("Last Name") ?>:</th>
              <th><?php ___("Email") ?>:</th>
              <th><?php ___("Phone Number") ?>:</th>
              <th><?php ___("Cell Phone") ?>:</th>
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
    $tableUsers = $('#table-students').DataTable({
      language: {
        "url": "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>"
      },
      columns: [
        {"data": "index"},
        {"data": "first_name"},
        {"data": "last_name"},
        {"data": "email"},
        {"data": "phone"},
        {"data": "cellphone"},
        {"data": "actions"},
      ],
      order: [[1, "asc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("students/ajax_find"); ?>",
        type: 'POST'
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true,
      createdRow: function (row, data, index) {
        $(row).attr('id', data['student_id']);

        $(row).dblclick(function () {
          location.href = '<?php echo base_url('students/edit'); ?>/' + $(this).attr('id');
        });
      },
    });
  });

  function reloadTable(resetPaging) {
    $tableUsers.ajax.reload(function () {
    }, resetPaging);
  }

  function delete_student(studentid) {
    if (confirm("<?php ___("Are you sure delete selected student?"); ?>")) {
      $.get("<?php echo base_url('students/ajax_delete') ?>/" + studentid, function () {
        reloadTable(false);
      })
    }
  }

  function show_student(studentid) {
    $.get("<?php echo base_url('students/ajax_get') ?>/" + studentid, function (student) {
      $("#StudentModal").css("margin-top", $(window).height() / 10);
      
      $("#student-no").text(studentid);

      $("#student-info").html("");
      $("#student-info").append("<tr><th><?php ___("Name"); ?>: </th><td>" + student.name + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Email"); ?>: </th><td>" + student.email + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Phone Number"); ?>: </th><td>" + student.phone + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Cell Phone"); ?>: </th><td>" + student.cellphone + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Company Name"); ?>: </th><td>" + student.company_name + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Tax Payer Number"); ?>: </th><td>" + student.tax_payer_number + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Address"); ?>: </th><td>" + student.address + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("State"); ?>: </th><td>" + student.state + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("City"); ?>: </th><td>" + student.city + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Zipcode"); ?>: </th><td>" + student.zipcode + "</td></tr>");
      $("#student-info").append("<tr><th><?php ___("Invoice Email"); ?>: </th><td>" + student.invoice_email + "</td></tr>");

      $("#ShowStudentButton").click();
    }, 'json');
  }
</script>

<!-- student info modal -->
<div id="ShowStudentButton" data-toggle="modal" data-target="#StudentModal"></div>
<div id="StudentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <?php ___("Student"); ?> #<span id="student-no"></span>
        </h4>
      </div>

      <div class="modal-body">
        <table id="student-info">
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default antoclose" data-dismiss="modal"><i class="fa fa-close"></i> <?php ___("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
<!-- /student info modal -->