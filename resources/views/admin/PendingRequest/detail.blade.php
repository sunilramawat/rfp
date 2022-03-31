@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Suppliers </h1>
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
          <div class="box-main-title">Detail</div>
          <div class="box-main-top-right">
            <a href="{{URL('admin/pendingrequest/view')}}"><button type="button" class="btn btn-primary">Back</button></a>
          </div>
        </div>
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <div class="form-group">
                  <div class="row align-items-end">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Logo:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="manage-supplierdetail-profile"><img src="{{URL('public/admin/dist/img/profile.png')}}"></div>
                     <!--  <label class="manage-supplierdetail-browse">
                        <input type="file" name="">
                        <button class="btn btn-primary">Browse</button>
                      </label> -->
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Business Name:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{$Supplier->business_name}}</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Owner Name:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{$Supplier->first_name}}</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Email Address:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{$Supplier->email}}</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Phone Number:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{'+'.$Supplier->phone_code}} {{$Supplier->phone_number}}</div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Business Identification:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{$Supplier->business_identification_number}}</div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      <lable class="control-label">Location:</lable>
                    </div>
                    <div class="col-md-6 col-12">
                      <div class="request-detail-text">{{$Supplier->address}}</div>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6 col-12">
                      
                    </div>
                    <div class="col-md-6 col-12">
                      @if($Supplier->activated == 0)
                          <button type="button" class="btn btn-success" onClick="Approve({{$Supplier->id}},1)">Approve</button>
                          <button type="button" onClick="Reject({{$Supplier->id}},1)" class="btn btn-danger btn-gap-left">Reject</button>
                      @endif
                    
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">

function Approve(Id, Status)
{ 
  $("#LoadingProgress").fadeIn('fast');
    $.ajax({
      url: "{{ URL('admin/pendingrequest/Approve') }}/"+Id+"/"+Status,
      type: "GET",
      contentType: false,
      cache: false,
      processData:false,
    success: function( data, textStatus, jqXHR ) {
      window.location.reload();
      $("#LoadingProgress").fadeOut('fast');
    },
    error: function( jqXHR, textStatus, errorThrown ) {
      window.location.reload();
      $("#LoadingProgress").fadeOut('fast');
    }
  });
}

function Reject(Id, Status)
{ 
  $("#LoadingProgress").fadeIn('fast');
    if (confirm('Are you sure you want to reject this user.')) {
      $.ajax({
        url: "{{ URL('admin/pendingrequest/Reject') }}/"+Id+"/"+Status,
        type: "GET",
        contentType: false,
        cache: false,
        processData:false,
      success: function( data, textStatus, jqXHR ) {
        window.location=data.url1;
       // window.location.reload();
       },
      error: function( jqXHR, textStatus, errorThrown ) {

      }
    });
  }
}
$(document).ready(function () {
  $('#search').on('keyup',function(){
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/trade/search') }}",
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