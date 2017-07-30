<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$query = "select `status`, sum(amount) as sum_amount from payment_histories where `type`=" . PAYMENT_TYPE_INCOME . " group by `status`";

$amounts = $this->db->query($query)->result();

$total_incom = 0;
$paid_amount = 0;
$pending_amount = 0;
foreach ($amounts as $amount) {
  if ($amount->status == PAYMENT_STATUS_PAID) {
    $paid_amount = $amount->sum_amount;
  } elseif ($amount->status == PAYMENT_STATUS_PENDING) {
    $pending_amount = $amount->sum_amount;
  }
}
$total_incom = $paid_amount + $pending_amount;

if (!isset($show_chart))
  $show_chart = true;
?>

<div class="tile-stats">
  <div class="icon"><i class="fa fa-indent"></i></div>
  <div class="count blue"><?php echo my_show_amount($total_incom); ?></div>

  <h3><?php ___("Income"); ?></h3>
  <div class="row">
    <div class="col-xs-6">
      <p>
        <a class="green" href="<?php echo base_url("payments/income/index/status/" . PAYMENT_STATUS_PAID); ?>" data-toggle="tooltip" title="<?php ___("Paid"); ?>">
          <?php ___("Paid"); ?>: <?php echo my_show_amount($paid_amount); ?>
        </a>
      </p>
    </div>
    <div class="col-xs-6">
      <p>
        <a class="red" href="<?php echo base_url("payments/income/index/status/" . PAYMENT_STATUS_PENDING); ?>" data-toggle="tooltip" title="<?php ___("Pending"); ?>">
          <?php ___("Pending"); ?>: <?php echo my_show_amount($pending_amount); ?>
        </a>
      </p>
    </div>
  </div>

  <?php if ($show_chart): ?>
    <div class="ln_solid"></div>

    <div id="total_income_pie_chart" style="height: 300px;"></div>

    <script>
      $(function () {
        var total_income_pie_chart = echarts.init(document.getElementById('total_income_pie_chart'), {
          color: ['#26B99A', '#E74C3C']
        });
        total_income_pie_chart.setOption({
          tooltip: {
            trigger: 'item',
            formatter: function (params) {
              var title = params.name + ": <small>" + params.percent + "%</small>" + '<br/>' + my_js_options.current_unit + $.number(params.value, 2);
              return title;
            }
          },
          legend: {
            show: false,
            x: 'center',
            y: 'bottom',
            data: ['<?php ___("Paid") ?>', '<?php ___("Pending") ?>']
          },
          toolbox: {
            show: true,
            feature: {
              magicType: {
                show: true,
                type: ['pie', 'funnel']
              },
              saveAsImage: {
                show: true,
                title: "<?php ___("Save Image"); ?>"
              }
            }
          },
          calculable: true,
          series: [{
              type: 'pie',
              radius: '55%',
              center: ['50%', '50%'],
              data: [{
                  value: <?php echo $paid_amount; ?>,
                  name: '<?php ___("Paid") ?>'
                }, {
                  value: <?php echo $pending_amount; ?>,
                  name: '<?php ___("Pending") ?>'
                }]
            }]
        });
      });

    </script>

  <?php endif; ?>

  <div class="clearfix"></div>
</div>