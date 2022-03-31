@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Supplier </h1>
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
          <div class="box-main-title">Pending Request List</div>
          <div class="box-main-top-right">
            <div class="box-serch-field">
              <input type="text" class="box-serch-input" name="" placeholder="Search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        
         <div  id="maintable" class="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
              <table class="table table-bordered admin-table dataTable"  id="example2" >
               
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Business Name</th> 
                    <th>Owner Name</th> 
                    <th>Email Address</th> 
                    <th>Phone Number</th> 
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Supplier_manage as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->business_name}}</td>
                    <td>{{$chip->first_name}}</td>
                    <td>{{$chip->email}}</td>
                    <td>{{'+'.$chip->phone_code.' '.$chip->phone_number}}</td>
                   
                   <!--  <td>{{$chip->block_status}} </td> -->
                    <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                      <a href="{{URL('admin/pendingrequest/detail')}}/{{$chip->id}}">
                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>  
                                 <!--  &nbsp;
                        <a href="{{URL('admin/supplier/delete')}}/{{$chip->id}}">
                                    <i class="fa fa-trash aria-hidden="true""></i>
                                  </a>        -->   
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
          <!-- <div class="box-main-bottom">
          <div class="box-main-showing">Showing {{$current_page}} to {{$row_count}} of {{$total_count}} entries</div>
          {{$Supplier_manage->links() }} -->
          <!-- <ul class="pagination">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
            </li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item active" aria-current="page">
              <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#">Next</a>
            </li>
          </ul> -->
       <!--  </div>   -->
        
        
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <img src="{{URL('public/admin/dist/img/cancel-close.svg')}}" alt="">
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">From:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="" class="form-control" placeholder="MM-DD-YYYY">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">To:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="" class="form-control" placeholder="MM-DD-YYYY">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Suppliers:</lable>
              </div>
              <div class="col-md-8 col-12">
                <select class="form-control" name="order_status" id="order_status">
                  <option value="" {{Request::get('order_status') == '' ? 'selected' : '' }} >All</option>
                   <option value="0" {{Request::get('order_status') != '' ? 'selected' : '' }}>Confirmed</option>
                  <option value="1" {{Request::get('order_status') == 1 ? 'selected' : '' }}>Cancelled</option>
                  <option value="2" {{Request::get('order_status') == 2 ? 'selected' : '' }}>Read to Dispatch</option>
                  <option value="3" {{Request::get('order_status') == 3 ? 'selected' : '' }}>Shipped</option>
                  <option value="4" {{Request::get('order_status') == 4 ? 'selected' : '' }}>Delivered</option>
                  <option value="5" {{Request::get('order_status') == 5 ? 'selected' : '' }}>Returned</option>
                  <option value="6" {{Request::get('order_status') == 6 ? 'selected' : '' }}>Payment Pending</option>
                  <option value="7" {{Request::get('order_status') == 7 ? 'selected' : '' }}>Payment Fail</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                
              </div>
              <div class="col-md-8 col-12">
                <button type="button" class="btn btn-primary">Apply</button>
              </div>
            </div>
          </div>
        </form>
        
      </div>
      
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">

function ChangeStatus(Id, Status)
{ 
  //alert(Status);
  $("#LoadingProgress").fadeIn('fast');
    $.ajax({
      url: "{{ URL('admin/supplier/ChangeStatus') }}/"+Id+"/"+Status,
      type: "GET",
      contentType: false,
      cache: false,
      processData:false,
    success: function( data, textStatus, jqXHR ) {
      window.location.reload();
      $("#LoadingProgress").fadeOut('fast');
    },
    error: function( jqXHR, textStatus, errorThrown ) {

    }
  });
}
$(document).ready(function () {
  $('#search').on('keyup',function(){
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/supplier/search') }}",
        type : 'get',
        data:{'search':$value},
        success:function(data){
          $('#maintable').html(data);
        }
    });
  })

  //your code here
});
  
</script>
@stop
