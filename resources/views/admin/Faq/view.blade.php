@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-10">
          <h1 class="m-0 text-dark">Question Answer </h1>
        </div><!-- /.col -->
        <div class="col-sm-2">
          <a href="{{URL('admin/faq/add')}}">{{ Form::submit('Add New',array('class'=>'btn btn-primary')) }}</a>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->
  
    <!-- Main content --> 
  <section class="content">
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">Question Answer List</div>
          <div class="box-main-top-right">
            <div class="box-serch-field">
              <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </div>
           <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
           <!--  <a href="{{URL('admin/cms/add')}}">{{ Form::submit('Add New',array('class'=>'btn btn-primary')) }}</a> -->
            <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
          </div>
        </div>
        <div id="maintable" class="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
              <table class="table table-bordered admin-table dataTable"  id="example2" >
               
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Question</th> 
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Faq as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->question}}</td>
                    <td>
                     <!--  <a href="{{URL('admin/faq/edit')}}/{{$chip->id}}"><i class="fas fa-edit"></i></a>
                       &nbsp; -->
                      
                      <a  onClick="DeleteTrade({{$chip->id}})" style="cursor: pointer" >
                                  <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>                     
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
           
        </div>
        
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">

function ChangeStatus(Id, Status)
{ 
  $("#LoadingProgress").fadeIn('fast');
    $.ajax({
      url: "{{ URL('admin/trade/ChangeStatus') }}/"+Id+"/"+Status,
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



function DeleteTrade(Id, Status)
{ 
  $("#LoadingProgress").fadeIn('fast');
    if (confirm('Are you sure you want to delete this Question?')) {
      $.ajax({
        url: "{{ URL('admin/faq/delete') }}/"+Id,
        type: "GET",
        contentType: false,
        cache: false,
        processData:false,
      success: function( data, textStatus, jqXHR ) {
        if(data == 0){
          alert('Sorry, this Question cannot be deleted. It is associated with multiple products.');
        }else{
          window.location.reload();
         $("#LoadingProgress").fadeOut('fast');
        }
        //alert('das');
         //window.location.reload();
         //$("#LoadingProgress").fadeOut('fast');
      },
      error: function( jqXHR, textStatus, errorThrown ) {
        //alert('dassa');
      }
    });
  }
}
$(document).ready(function () {
  $('#search').on('keyup',function(){
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/cms/search') }}",
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
