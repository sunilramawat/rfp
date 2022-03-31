@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            

          <h1 class="m-0 text-dark">Manage Reports </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->

    <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">New Customers</div>
        </div>

        {!!Form::open(['url'=>'admin/supplier/edit-save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!} 
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-12">
              <form>
                
                <canvas id="sales-chart" height="470"></canvas>
              </form>
            </div>
          </div>
        </div>
        {!!Form::close()!!}       	
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript"> 
 $(function () {
  'use strict'
  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#sales-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [
        {
          backgroundColor: '#F7CD37',
          borderColor    : '#F7CD37',
          data           : <?php echo json_encode($charArr1) ;?>,
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      series: {
            bars: {
                show: true,
              barWidth: 0.05,
              lineWidth: 1,
              order: 1,
              fillColor: {
                  colors: [{
                      opacity: 1
                  }, {
                      opacity: 1
                  }]
              }
            }
          },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '0.05px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero: true,

            // Include a dollar sign in the ticks
           /* callback: function (value, index, values) {
              if (value >= 1000) {
                value /= 1000
                value += 'k'
              }
              return  value
            }*/
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          lineWidth    : '0.05px',
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})

   
</script>
@stop