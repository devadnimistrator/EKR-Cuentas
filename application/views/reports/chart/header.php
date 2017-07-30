<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-title">
  <div class="title_left">
    <h3>
      <a href="<?php echo base_url("reports"); ?>"><?php ___("Reports"); ?></a>
      <small><i class="fa fa-angle-double-right"></i> <?php ___("Chart"); ?> </small>
    </h3>
  </div>

  <div class="title_right">
    <div id="cart-rangedate" class="daterangepicker-input pull-right" style="width: 200px; margin-left: 5px;">
      <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
      <span><?php echo $start_date; ?> - <?php echo $end_date; ?></span> <b class="caret"></b>
    </div>
    <select id="chart-account-type" class="form-control pull-right" style="width: 100px; display: inline-block; height: 32px;">
      <option value="all">- <?php ___("All"); ?> -</option>
      <?php foreach (my_get_payment_methods() as $id => $name): ?>
        <option value="<?php echo $id; ?>" <?php if ($account == $id) echo "selected"; ?>><?php echo $name; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<div class="clearfix"></div>

<script>
  var chartStartDate = '<?php echo my_add_date(-29); ?>';
  var chartEndDate = '<?php echo date('Y-m-d'); ?>';
  var cashStatsChart, cashDateChart;
  var incomeStatsChart, incomeDateChart;

  $(function () {
    $('#cart-rangedate').daterangepicker(custom_daterangepicker_option1, function (start, end, label) {
      $("#cart-rangedate span").text(start.format('MMM DD, YY') + " - " + end.format('MMM DD, YY'));

      chartStartDate = start.format('YYYY-MM-DD');
      chartEndDate = end.format('YYYY-MM-DD');

      draw_cash_chart();
      draw_income_chart();
    });

    $("#chart-account-type").change(function () {
      draw_cash_chart();
      draw_income_chart();
    });

    draw_cash_chart();
    draw_income_chart();

    $(window).on('resize', function () {
      if (cashStatsChart != null && cashStatsChart != undefined) {
        cashStatsChart.resize();
      }

      if (cashDateChart != null && cashDateChart != undefined) {
        cashDateChart.resize();
      }

      if (incomeStatsChart != null && incomeStatsChart != undefined) {
        incomeStatsChart.resize();
      }

      if (incomeDateChart != null && incomeDateChart != undefined) {
        incomeDateChart.resize();
      }
    });
  })
</script>