<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12" id="chart-income">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Income"); ?></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a href="<?php echo base_url("reports/grid/income/" . $account); ?>" title="<?php ___("View Grid"); ?>" data-toggle="tooltip"><i class="fa fa-list"></i></a></li>
          <li><a class="collapse-link" id="income-collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <div class="row">
          <div class="col-md-3 col-sm-5 col-xs-12">
            <div class="tile-stats">
              <div class="count green"><?php echo my_get_currency_unit(); ?><span id="income-stats-all"></span></div>
              
              <p style="color: #337ab7;"><?php ___("Paid"); ?>: <?php echo my_get_currency_unit(); ?><span id="income-stats-paid"></span></p>
              
              <p class="warning"><?php ___("Pending"); ?>: <?php echo my_get_currency_unit(); ?><span id="income-stats-pending"></span></p>
              
              <div class="ln_solid"></div>
              <div id="income-stats-chart" style="height: 200px;"></div>
              <div class="clearfix"></div>
            </div>
          </div>

          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="row">
              <div id="income-date-chart" style="width:100%; height:400px;"></div>
            </div>
          </div>
        </div>

        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
<br />

<script>
  function draw_income_chart() {
    if ($("#income-collapse-link > i.fa").hasClass('fa-chevron-down')) {
      $("#income-collapse-link").trigger("click");
    }

    $.post("<?php echo base_url("reports/chart/ajax_get_income"); ?>", {
      start_date: chartStartDate,
      end_date: chartEndDate,
      account: $("#chart-account-type").val()
    }, function (chart_data) {
      draw_income_stats_chart(chart_data);
      draw_income_date_chart(chart_data);
    }, 'json');
  }

  function draw_income_stats_chart(chart_data) {
    $("#income-stats-chart").empty();
    $("#income-stats-all").html($.number(chart_data.all_paid + chart_data.all_pending, 2));
    $("#income-stats-paid").html($.number(chart_data.all_paid, 2));
    $("#income-stats-pending").html($.number(chart_data.all_pending, 2));

    incomeStatsChart = echarts.init(document.getElementById('income-stats-chart'), {
      color: ['#337ab7', '#f0ad4e']
    });
    incomeStatsChart.setOption({
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
              value: chart_data.all_paid,
              name: '<?php ___("Paid") ?>'
            }, {
              value: chart_data.all_pending,
              name: '<?php ___("Pending") ?>'
            }]
        }]
    });
  }

  function draw_income_date_chart(chart_data) {
    $("#income-date-chart").empty();

    incomeDateChart = echarts.init(document.getElementById('income-date-chart'), {
      color: ['#337ab7', '#f0ad4e']
    });

    incomeDateChart.setOption({
      title: {
        show: false
      },
      tooltip: {
        trigger: 'axis'
      },
      legend: {
        x: 120,
        y: 20,
        data: ['<?php ___("Paid") ?>', '<?php ___("Pending") ?>']
      },
      toolbox: {
        show: true,
        feature: {
          magicType: {
            show: true,
            title: {
              line: '<?php ___("Line") ?>',
              bar: '<?php ___("Bar") ?>',
              stack: '<?php ___("Stack") ?>',
              tiled: '<?php ___("Tiled") ?>'
            },
            type: ['line', 'bar', 'stack', 'tiled']
          },
          saveAsImage: {
            show: true,
            title: "<?php ___("Save Image") ?>"
          }
        }
      },
      calculable: true,
      xAxis: [{
          type: 'category',
          boundaryGap: true,
          data: chart_data.dates
        }],
      yAxis: [{
          type: 'value'
        }],
      series: [{
          name: '<?php ___("Paid") ?>',
          type: 'line',
          smooth: true,
          itemStyle: {
            normal: {
              areaStyle: {
                type: 'default'
              }
            }
          },
          data: chart_data.paid
        }, {
          name: '<?php ___("Pending") ?>',
          type: 'line',
          smooth: true,
          itemStyle: {
            normal: {
              areaStyle: {
                type: 'default'
              }
            }
          },
          data: chart_data.pending
        }]
    });
  }

</script>