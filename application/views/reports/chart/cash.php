<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><?php ___("Cash"); ?></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a href="#" onclick="view_cash_grid();" title="<?php ___("View Grid"); ?>" data-toggle="tooltip"><i class="fa fa-list"></i></a></li>
          <li><a class="collapse-link" id="cash-collapse-link"><i class="fa fa-chevron-up"></i></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <div class="row">
          <div class="col-md-3 col-sm-5 col-xs-12">
            <div class="tile-stats">
              <div class="icon"><i class="fa" id="cash-stats-icon"></i></div>
              <div class="count blue"><?php echo my_get_currency_unit(); ?><span id="cash-stats-remaining"></span></div>

              <h3><?php ___("Before"); ?>: <?php echo my_get_currency_unit(); ?><span id="cash-stats-before-remaining">0</span></h3>
              <div class="row">
                <div class="col-xs-6">
                  <p>
                    <a class="green" href="<?php echo base_url("payments/income"); ?>" data-toggle="tooltip" title="<?php ___("Income"); ?>">
                      <i class="fa fa-sort-asc"></i> <?php echo my_get_currency_unit(); ?><span id="cash-stats-income">0</span>
                    </a>
                  </p>
                </div>
                <div class="col-xs-6">
                  <p>
                    <a class="red" href="<?php echo base_url("payments/expenses"); ?>" data-toggle="tooltip" title="<?php ___("Expenses"); ?>">
                      <i class="fa fa-sort-desc"></i> <?php echo my_get_currency_unit(); ?><span id="cash-stats-expenses">0</span>
                    </a>
                  </p>
                </div>
              </div>

              <div class="ln_solid"></div>
              <div id="cash-stats-chart" style="height: 200px;"></div>
              <div class="clearfix"></div>
            </div>
          </div>

          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="row">
              <div id="cash-date-chart" style="width:100%; height:400px;"></div>
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
  function view_cash_grid() {
    location.href = "<?php echo base_url("reports/grid/cash"); ?>/" + $("#chart-account-type").val() + "/" + chartStartDate + "/" + chartEndDate;
  }

  function draw_cash_chart() {
    if ($("#cash-collapse-link > i.fa").hasClass('fa-chevron-down')) {
      $("#cash-collapse-link").trigger("click");
    }

    $.post("<?php echo base_url("reports/chart/ajax_get_cash"); ?>", {
      start_date: chartStartDate,
      end_date: chartEndDate,
      account: $("#chart-account-type").val()
    }, function (chart_data) {
      draw_cash_stats_chart(chart_data);
      draw_cash_date_chart(chart_data);
    }, 'json');
  }

  function draw_cash_stats_chart(chart_data) {
    $("#cash-stats-chart").empty();
    var remaining = chart_data.before_remaining + chart_data.all_income - chart_data.all_expenses;
    $("#cash-stats-remaining").html($.number(remaining, 2));
    $("#cash-stats-before-remaining").html($.number(chart_data.before_remaining, 2));
    $("#cash-stats-income").html($.number(chart_data.all_income, 2));
    $("#cash-stats-expenses").html($.number(chart_data.all_expenses, 2));

    if (remaining > chart_data.before_remaining) {
      $("#cash-stats-icon").removeClass().addClass("fa fa-arrow-up");
    } else if (remaining > chart_data.before_remaining) {
      $("#cash-stats-icon").removeClass().addClass("fa fa-arrow-up");
    } else {
      $("#cash-stats-icon").removeClass().addClass("fa fa-arrow-h");
    }

    cashStatsChart = echarts.init(document.getElementById('cash-stats-chart'), {
      color: ['#26B99A', '#E74C3C']
    });
    cashStatsChart.setOption({
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
        data: ['<?php ___("Income") ?>', '<?php ___("Expenses") ?>']
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
              value: chart_data.all_income,
              name: '<?php ___("Income") ?>'
            }, {
              value: chart_data.all_expenses,
              name: '<?php ___("Expenses") ?>'
            }]
        }]
    });
  }

  function draw_cash_date_chart(chart_data) {
    $("#cash-date-chart").empty();

    cashDateChart = echarts.init(document.getElementById('cash-date-chart'), {
      color: ['#26B99A', '#E74C3C', '#3498DB']
    });

    cashDateChart.setOption({
      title: {
        show: false
      },
      tooltip: {
        trigger: 'axis'
      },
      legend: {
        x: 120,
        y: 20,
        data: ['<?php ___("Income") ?>', '<?php ___("Expenses") ?>', '<?php ___("Remaining") ?>']
      },
      toolbox: {
        show: true,
        feature: {
          magicType: {
            show: true,
            title: {
              line: '<?php ___("Line"); ?>',
              bar: '<?php ___("Bar"); ?>'
            },
            type: ['line', 'bar']
          },
          saveAsImage: {
            show: true,
            title: "<?php ___("Save Image"); ?>"
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
          name: '<?php ___("Income") ?>',
          type: 'line',
          smooth: true,
          itemStyle: {
            normal: {
              areaStyle: {
                type: 'default'
              }
            }
          },
          data: chart_data.income
        }, {
          name: '<?php ___("Expenses") ?>',
          type: 'line',
          smooth: true,
          itemStyle: {
            normal: {
              areaStyle: {
                type: 'default'
              }
            }
          },
          data: chart_data.expenses
        }, {
          name: '<?php ___("Remaining") ?>',
          type: 'line',
          smooth: true,
          itemStyle: {
            normal: {
              areaStyle: {
                type: 'default'
              }
            }
          },
          data: chart_data.remaining
        }]
    });
  }

</script>