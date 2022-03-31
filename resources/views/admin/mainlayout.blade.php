  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> @php 
      $testurl = str_replace('/admin/', '', Request::getPathInfo());
      $new_url = str_replace('/', '-', $testurl);
      if($new_url == 'report_order'){
        $new_url = 'Order-placed';
      }
      if($new_url == 'report-cancel'){
        $new_url = 'Order-cancelled';
      }

      if($new_url == 'report-refund'){
        $new_url = 'Refund-requests';
      }
      echo ucfirst($new_url);
      @endphp</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{URL('public/admin/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/summernote/summernote-bs4.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- Custom CSS File -->
  <link rel="stylesheet" href="{{URL('public/admin/docs/assets/css/custom.css')}}">
  
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item profile-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <span class="profile-img">
            <img src="{{URL('public/admin/dist/img/profile.png')}}" alt="">
          </span>
          <span class="profile-name">Richard</span>
          <i class="fas fa-angle-down"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- <span class="dropdown-item dropdown-header">15 Notifications</span> -->
          <div class="dropdown-divider"></div>
          <a href="{{URL('admin/logout')}}" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> Logout
            <!-- <span class="float-right text-muted text-sm">3 mins</span> -->
          </a>
          <div class="dropdown-divider"></div>
         <!--  <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div> -->
          <!-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> -->
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{URL('admin/dashboard')}}" class="brand-link">
      <img src="{{URL('public/admin/dist/img/logo_dashboard.png')}}" alt="">
    </a>
    <div class="main-navigation">MAIN NAVIGATION</div>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="{{URL('admin/dashboard')}}" class="{{ (request()->is('admin/dashboard')) ? 'nav-link active' : 'nav-link' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard

              </p>
            </a>
          </li>
          <li class="nav-item">
            @if((request()->is('admin/user*')))
              <a href="{{URL('admin/user/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/user/view')}}" class="nav-link ">
            @endif
            <i class="nav-icon fas fa-user-cog"></i>
              <p>
                Manage Users
              </p>
            </a>
          </li>
          <li class="nav-item">
            @if((request()->is('admin/partner*')) || (request()->is('admin/pendingrequest*')))
              <a href="{{URL('admin/partner/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/partner/view')}}" class="nav-link ">
            @endif
            
              <i class="nav-icon fas fa-store"></i>
              <p>
                Manage Posts
              </p>
            </a>
          </li>
          <!-- <li class="nav-item">
            @if((request()->is('admin/order*')))
              <a href="{{URL('admin/order/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/order/view')}}" class="nav-link ">
            @endif
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>
                Manage Orders
              </p>
            </a>
          </li> -->
          <!-- <li class="nav-item">
            @if((request()->is('admin/trade*')) || (request()->is('admin/subtrade*')))
              <a href="{{URL('admin/trade/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/trade/view')}}" class="nav-link ">
            @endif
            <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Manage Trades
              </p>
            </a>
          </li> -->
          <!-- <li class="nav-item">
            @if((request()->is('admin/payment*')))
              <a href="{{URL('admin/payment/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/payment/view')}}" class="nav-link ">
            @endif
              <i class="nav-icon fas fa-credit-card"></i>
              <p>
                Manage Payments
              </p>
            </a>
          </li>
          <li class="nav-item">
            @if((request()->is('admin/payout*')))
              <a href="{{URL('admin/payout/view')}}" class="nav-link active">
            @else
              <a href="{{URL('admin/payout/view')}}" class="nav-link ">
            @endif  
              <i class="nav-icon fas fa-money-check-alt"></i>
              <p>
                Manage Payout
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="{{ (request()->is('admin/report/*')) ? 'nav-link active' : 'nav-link' }}">
              <i class="nav-icon fas fa-chart-area"></i>
              <p>
                Manage Reports
                <i class="fas fa-angle-right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{URL('admin/report/order')}}" class="{{ (request()->is('admin/report/order')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Orders Placed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/cancel')}}" class="{{ (request()->is('admin/report/cancel')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Orders Cancelled</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/refund')}}" class="{{ (request()->is('admin/report/refund')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Refund Requests</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/customer')}}" class="{{ (request()->is('admin/report/customer')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Registered Customers</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/supplier')}}" class="{{ (request()->is('admin/report/supplier')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Registered Business/Supplier</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/earning')}}" class="{{ (request()->is('admin/report/earning')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Total Earnings</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{URL('admin/report/payout')}}" class="{{ (request()->is('admin/report/payout')) ? 'nav-link sub-active' : 'nav-link' }}">
                  <i class="fas fa-angle-right"></i>
                  <p>Total Payouts</p>
                </a>
              </li>
             
            </ul>
          </li> -->
          <li class="nav-item">
            <a href="{{URL('admin/cms/view')}}" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>
                CMS
              </p>
            </a>
          </li>
          <!--  <li class="nav-item">
            <a href="{{URL('admin/faq/view')}}" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>
                Question Answer 
              </p>
            </a>
          </li> -->
           <li class="nav-item">
            <a href="{{URL('admin/report/view')}}" class="nav-link">
              <i class="nav-icon fas fa-file"></i>
              <p>
                Report User
              </p>
            </a>
          </li> 
           <li class="nav-item">
            <a href="{{URL('admin/logout')}}" class="nav-link">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Logout
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- @include('admin.alert_message')
     -->            
    @yield('content')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">  
    &copy; 2021, All Rights Reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{URL('public/admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{URL('public/admin/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{URL('public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{URL('public/admin/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{URL('public/admin/plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{URL('public/admin/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL('public/admin/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{URL('public/admin/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{URL('public/admin/plugins/moment/moment.min.js')}}"></script>
<script src="{{URL('public/admin/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{URL('public/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{URL('public/admin/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{URL('public/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{URL('public/admin/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{URL('public/admin/dist/js/pages/dashboard.js')}}"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="{{ URL('public/admin/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{URL('public/admin/dist/js/demo.js')}}"></script>
<!-- <script src="{{ URL('public/admin/Flot/jquery.flot.min.js') }}"></script>
<script src="{{ URL('public/admin/Flot/jquery.flot.resize.min.js') }}"></script>
<script src="{{ URL('public/admin/Flot/jquery.flot.pie.min.js') }}"></script>
<script src="{{ URL('public/admin/Flot/jquery.flot.categories.min.js') }}"></script> -->
<script src="{{ URL('public/admin/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL('public/admin/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL('public/admin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<!-- Bootstrap 4 -->
<script src="{{ URL('public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>


<!-- Summernote -->
<script src="{{ URL('public/admin/plugins/summernote/summernote-bs4.min.js')}}"></script>
<script>
  $(function () {
    // Summernote
    $('.textarea').summernote({
      toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough', 'superscript', 'subscript']],
    ['fontsize', ['fontsize']],
    ['color', ['color']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['height', ['height']]
  ]
    });
  })

</script> 

</body>
</html>
<script>
$(function () {
  $("#example1").DataTable({
    "responsive": true,
    "autoWidth": false,
  });
  
  var table = $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": false,
    "bFilter":false,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    "dom": 'Bfrtip',
       "buttons": [
          'copyHtml5',
          'excelHtml5',
          'csvHtml5',
          'pdfHtml5'
          ],

    initComplete: function() {
         var $buttons = $('.dt-buttons').hide();
         $("#ExportReporttoExcel").on("click", function() {
              table.button( '.buttons-excel' ).trigger();
          });
         /*$('#exportLink').on('change', function() {
            var btnClass = $(this).find(":selected")[0].id 
               ? '.buttons-' + $(this).find(":selected")[0].id 
               : null;
            if (btnClass) $buttons.find(btnClass).click(); 
         })*/
       }         
  });
  
   

   $('#search').keyup(function(){
      table.search($(this).val()).draw() ;
    })
   $('#buttons').keyup(function(){
      table.buttons($(this).val()).draw() ;
    })
    
    $('#searchfilter').click( function() {
        table.draw();
    } );
});



</script>



