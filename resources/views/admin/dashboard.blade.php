@extends('admin.mainlayout')
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{URL('public/admin/dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
  <!-- Custom CSS File -->
  <link rel="stylesheet" href="{{URL('public/admin/docs/assets/css/custom.css')}}">
</head>
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Dashboard</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
          <a href="{{URL('admin/user/view')}}">
          <div class="small-box bg-green">
            <div class="inner">
              <p>Total Users</p>
              <h3>{{$user_total_count}}</h3>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
              <!-- <i class="ion ion-bag"></i> -->
            </div>
          </div>
        </a>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
        <a href="{{URL('admin/partner/view')}}">  
          <div class="small-box bg-purple">
            <div class="inner">
              <p>Total Partner</p>
              <h3>{{$patner_total_count}}</h3>
            </div>
            <div class="icon">
              <i class="fas fa-file-alt"></i>
            </div>
          </div>
        </a>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-md-6 col-sm-12">
          <!-- small box -->
        <a href="{{URL('admin/user/view')}}">  
          <div class="small-box bg-blue">
            <div class="inner">
              <p>Premium Users</p>
              <h3>{{$premium_user_total_count}}</h3>
            </div>
            <div class="icon">
              <i class="fas fa-clipboard-list"></i>
            </div>
          </div>
        </a>
        </div>
        <!-- ./col -->
        <?php /*<div class="col-lg-3 col-md-6 col-sm-12">
          <!-- small box -->
        <a href="{{URL('admin/report/earning')}}">   
          <div class="small-box bg-orange">
            <div class="inner">
              <p>Conversion Rates</p>
              <h3></h3>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
          </div>
        </a>
        </div>
        <!-- ./col -->
      </div> */?>
      <!-- /.row -->
      <!-- Main row -->
     
      <!-- /.row (main row) -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  <section class="content">
    <div class="container-fluid">
          <div class="row">          
            <div class="col-md-6 col-xl-6">     
              <div class="box-main-title box-dashboard-text">Users</div>         
              <div class="box-main graph-space">
                <canvas id="customer-chart" height="250"></canvas>             
              </div>
            </div>
            <div class="col-md-6 col-xl-6">  
              <div class="box-main-title box-dashboard-text">Partners</div>           
              <div class="box-main graph-space">
                <form>                
                  <canvas id="supplier-chart" height="250"></canvas>
                </form>
              </div>
            </div>
          </div>
         <!--  <div  class="row">
            <div class="col-md-12 col-xl-12">   
              <div class="box-main-title box-dashboard-text">Conversion Rates</div>            
              <div class="box-main graph-space">
                <canvas id="visitors-chart" height="250"></canvas>             
              </div>
            </div>
          </div> -->
             
      </div>
    </div><!-- /.container-fluid -->
  </section>
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

  var $salesChart = $('#customer-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : ['Week 1','Week 2','Week 3','Week 4'],
      datasets: [
        {
          backgroundColor: '#EF8D6E',
          borderColor    : '#EF8D6E',
          data           : <?php echo json_encode($customerArr1) ;?>,
          label          : 'Customers',
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
          lineWidth    : '0.05px',
          gridLines: {
            display      : true,
            // lineWidth    : '0.05px',
            // color        : 'rgba(0, 0, 0, .2)',
            // zeroLineColor: 'transparent'
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
            display: true,
          },
          ticks    : ticksStyle
        }]
      }
    }
  })


  
})
</script>
<script type="text/javascript"> 
 $(function () {
  'use strict'
  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#supplier-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : ['Week 1','Week 2','Week 3','Week 4'],
      datasets: [
        {
          backgroundColor: '#7FB1F5',
          borderColor    : '#7FB1F5',
          data           : <?php echo json_encode($supplierArr1) ;?>,
          label          : 'Suppliers',
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
            lineWidth    : '0.05px',
          gridLines: {
            display      : true,
            // lineWidth    : '0.05px',
            // color        : 'rgba(0, 0, 0, .2)',
            // zeroLineColor: 'transparent'
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
            display: true,
          },
          ticks    : ticksStyle
        }]
      }
    }
  })


  
})
</script>


<script type="text/javascript"> 

 $(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

  var $visitorsChart = $('#visitors-chart')
  var visitorsChart  = new Chart($visitorsChart, {
    data   : {
      labels  :  <?php //echo json_encode($charArr) ;?>,
      datasets: [{
        type                : 'line',
        data                : <?php //echo json_encode($charArr1) ;?>,
        backgroundColor     : 'transparent',
        borderColor         : '#007bff',
        pointBorderColor    : '#007bff',
        pointBackgroundColor: '#007bff',
        fill                : false
        // pointHoverBackgroundColor: '#007bff',
        // pointHoverBorderColor    : '#007bff'
      }]
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
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            /*lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'*/
          },
          ticks    : $.extend({
            beginAtZero : true,
           /* suggestedMax: 200*/
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: true
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})

   
</script>
@stop