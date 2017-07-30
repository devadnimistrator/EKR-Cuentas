<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("education/calendar"); ?>"><?php ___("Education"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Classes"); ?> </small>
    </h3>
  </div>
  <div class="title_right">
    <div class="pull-right">
      <button type="button" class="btn btn-round btn-primary btn-sm" onclick="location.href = '<?php echo base_url("education/classes/add"); ?>'"><?php ___("Add New Class"); ?></button>
    </div>
  </div>
</div>
<div class="clearfix"></div>

<style>
  #btnExcelExport.btn-dark {
    cursor: default;
  }
</style>

<div class="row">
  <div class="col-lg-2 col-md-3 col-xs-12">
    <div class="x_panel">
      <div class="row x_title">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <h2>
            <?php ___("Filters"); ?>
          </h2>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </ul>
          <div class="clearfix"></div>
        </div>
      </div>

      <div class="x_content">
        <div class="row">
          <form method="post" name="frmFilter" id="frmFilter" class="form-filters">
            <label for="payment_method"><?php ___("Date"); ?>:</label>
            <div id="filter_daterange" class="daterangepicker-input">
              <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
              <span></span>
              <b class="caret"></b>
            </div>
            
            <label for="class_status"><?php ___("Status"); ?>:</label>
            <select name="class_status" id="class_status" onchange="reloadTable(true)" class="form-control">
              <option value="all">- <?php ___("All"); ?> -</option>
              <option value="plan"><?php ___("Plan"); ?></option>
              <option value="running"><?php ___("Running"); ?></option>
              <option value="end"><?php ___("End"); ?></option>
              <option value="cancel"><?php ___("Cancel"); ?></option>
            </select>

            <div id="category-filter">
              <label for="category_id"><?php ___("Category"); ?>:</label>
              <select name="category_id" id="category_id" onchange="reloadTable(true)" class="form-control"></select>
            </div>
          </form>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-10 col-md-9 col-xs-12">
    <div class="x_panel">
      <div class="x_title">						
        <h2>
          <?php ___("Classes"); ?>
        </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <table id="dataTable" class="table table-striped table-bordered" width="100%">
          <thead>
            <tr>
              <th class="nosort" width="50"><i class="fa fa-list-ol"></i></th>
              <th width="150"><?php ___("Start") ?>:</th>
              <th class="nosort"><?php ___("Description") ?>:</th>
              <th class="nosort" width="100"></th>
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
    location.href = "<?php echo base_url("expenses/payments/index"); ?>/" + $("#filter_company").val() + "/" + $("#filter_category").val() + "/" + $("#filter_payment").val() + "/" + $("#from-date").val() + "/" + $("#end-date").val();
  }

  var $dataTable;
  var currencyUnit = '<?php echo my_get_currency_unit(); ?>';
  var lastSearchParams = {
    start_date: '<?php echo date('Y-01-01'); ?>',
    end_date: '<?php echo date('Y-12-31'); ?>'
  };
  $(document).ready(function () {
    $('#filter_daterange span').text(moment().startOf('year').format('MMM DD, YY') + " - " + moment().endOf('year').format('MMM DD, YY'));
    
    
    $('#filter_daterange').daterangepicker(custom_daterangepicker_option2, function (start, end, label) {
      $("#filter_daterange span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      lastSearchParams.start_date = start.format('YYYY-MM-DD');
      lastSearchParams.end_date = end.format('YYYY-MM-DD');

      reloadTable(true);
    });

    loadCategories();

    $dataTable = $('#dataTable').DataTable({
      language: {
        url: "<?php echo base_url("assets/plugins/datatables/language/"); ?>" + my_js_options.language + ".json?v=<?php echo ASSETS_VERSION; ?>",
        searchPlaceholder: "<?php ___("Search by Reason"); ?>"
      },
      columns: [
        {data: "index"},
        {data: "start_datetime"},
        {data: "description"},
        {data: "actions"},
      ],
      order: [[1, "desc"]],
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?php echo base_url("education/classes/ajax_find"); ?>",
        type: "POST",
        data: function (d) {
          lastSearchParams = $.extend(lastSearchParams, d, {
            status: document.frmFilter.class_status.value,
            category_id: document.frmFilter.category_id.value
          });
//          console.log(lastSearchParams);
          return lastSearchParams;
        }
      },
      aoColumnDefs: [{
          'bSortable': false,
          'aTargets': ['nosort']
        }],
      responsive: true
    });
  });

  function reloadTable(resetPaging) {
    $("#btnExcelExport").removeClass("btn-primary").addClass("btn-dark");
    $dataTable.ajax.reload(function () {
    }, resetPaging);
  }

  function loadCategories() {
    $.getJSON("<?php echo base_url("app/ajax_get_categories/".PAYMENT_REASON_TYPE_EDUCATION); ?>", function (categories) {
      $("#category_id").html('<option value="all" selected><?php echo ___("All"); ?></option>');

      $.each(categories, function (index, category) {
        $("#category_id").append('<option value="' + category.id + '">' + category.name + '</option>');
      });
      $("#category-filter").show();
    });
  }
  
  function delete_class(id) {
    if (confirm("<?php ___("Are you sure delete selected class?"); ?>")) {
      $.get("<?php echo base_url('education/classes/ajax_delete_class') ?>/" + id, function () {
        reloadTable(false);
      })
    }
  }
</script>