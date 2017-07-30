<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
my_load_css("plugins/fullcalendar/fullcalendar.min.css");
//my_load_css("plugins/fullcalendar/dist/fullcalendar.print.css");

$CI->add_js("plugins/fullcalendar/fullcalendar.min.js");
if (DISPLAY_LANGUAGE != 'english') {
  $CI->add_js("plugins/fullcalendar/locale/" . DISPLAY_LANGUAGE . ".js");
}
?>
<style>

  #script-warning {
    display: none;
    background: #eee;
    border-bottom: 1px solid #ddd;
    padding: 0 10px;
    line-height: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 12px;
    color: red;
    margin-bottom: 20px;
  }

  #calendar-loading {
    display: none;
    position: absolute;
    top: 10px;
    right: 10px;
  }

  .fc-event {
    padding: 5px;
  }

  .fc-event.event-plan {
    background-color: #1ABB9C;
  }

  .fc-event.event-running {
    background-color: #349ADB;
  }

  .fc-event.event-end {
    background-color: #999;
  }

  .fc-event.event-cancel {
    background-color: #E74C3C;
  }

  .fc-event span {
    line-height: 1.5em;
  }
</style>


<div class="x_panel">
  <div class="x_title">
    <h2>
      <?php ___("Calendar"); ?>
      <small>
        <i class="fa fa-square" style="color:#1ABB9C"></i> <?php ___("Plan"); ?>&nbsp;
        <i class="fa fa-square" style="color:#349ADB"></i> <?php ___("Running"); ?>&nbsp;
        <i class="fa fa-square" style="color:#999"></i> <?php ___("End"); ?>&nbsp;
        <i class="fa fa-square" style="color:#E74C3C"></i> <?php ___("Cancel"); ?>
      </small>
    </h2>
    <ul class="nav navbar-right panel_toolbox">
      <li><button onclick="location.href = '<?php echo site_url("education/classes/add"); ?>'" class="btn btn-round btn-primary btn-sm"><i class="fa fa-plus-circle"></i> <?php ___("Add Class"); ?></button></li>
      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
    </ul>
    <div class="clearfix"></div>
  </div>
  <div class="x_content">
    <div id='script-warning'>
      <code><?php ___("Can't load calendar of education"); ?></code>
    </div>

    <div id='calendar-loading'>loading...</div>

    <div id='educationCalendar'></div>
  </div>
</div>
<!-- FullCalendar -->
<script>
  $(window).load(function () {
    var date = new Date(),
            d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear(),
            started,
            categoryClass;
    var calendar = $('#educationCalendar').fullCalendar({
      weekNumbers: true,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listMonth'
      },
      selectable: true,
      selectHelper: true,
      editable: false,
      events: {
        url: '<?php echo site_url("education/calendar/ajax_get_events"); ?>',
        error: function () {
          $('#script-warning').show();
        }
      },
      loading: function (bool) {
        $('#loading').toggle(bool);
      },
      eventRender: function (event, element) {
        if (element.find(".fc-content").length > 0) {
          if (event.status == 'plan') {
            element.addClass('event-plan');
          } else if (event.status == 'running') {
            element.addClass('event-running');
          } else if (event.status == 'end') {
            element.addClass('event-end');
          } else if (event.status == 'cancel') {
            element.addClass('event-cancel');
          } else {
            console.log(event.status);
          }
        } else {
          if (event.status == 'plan') {
            element.find(".fc-list-item-time").prepend("<i class='fa fa-circle green'></i> ");
          } else if (event.status == 'running') {
            element.find(".fc-list-item-time").prepend("<i class='fa fa-play blue'></i> ");
          } else if (event.status == 'end') {
            element.find(".fc-list-item-time").prepend("<i class='fa fa-stop dark'></i> ");
          } else if (event.status == 'cancel') {
            element.find(".fc-list-item-time").prepend("<i class='fa fa-ban red'></i> ");
          }
        }
      }
    });
  });
</script>
<!-- /FullCalendar -->