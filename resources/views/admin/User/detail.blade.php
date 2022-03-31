@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Users </h1>
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
          <div class="box-main-title">User Detail</div>
          <div class="box-main-top-right">
            
            <a href="{{URL('admin/user/view')}}"><button type="button" class="btn btn-primary">Back</button></a>

          </div>
        </div>
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <?php /*<div class="form-group">
                  <div class="row align-items-end">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Picture:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="manage-supplierdetail-profile">
                    
                      	<!-- <img src="http://localhost/material/public/admin/dist/img/profile.png">-->
                      		<img src="{{$Users['image_url']}}"> 

                   		
                      </div>
                     <!--  <label class="manage-supplierdetail-browse">
                        <input type="file" name="">
                        <button class="btn btn-primary">Browse</button>
                      </label> -->
                    </div>
                  </div>
                </div>*/ ?>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Name:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">{{$Users['first_name']}}</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Email Address:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">
                           {{$Users['email']}}   
                      </div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Phone:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">{{$Users['phone']}}
                      </div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Type:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text"> @if($Users->is_subscribe ==  0)         
                        Normal    
                      @else
                        Premium           
                      @endif</div>
                    </div>
                  </div>
                </div>  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Status:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">          
                       @if($Users->user_status == 2 || $Users->user_status == 0)
                       Unactive    
                      @else
                        Active           
                      @endif</div>
                    </div>
                  </div>
                </div>  
              </form>
            </div>
          </div>
        </div>
        <div class="box-separator">
            <hr>
        </div>
        
         <input type="hidden" name="id" id="id" value="{{$Users['id']}}">
       
        
       
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css"/>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">


//.datepicker("setDate",'now');  
$(function() {   
      $( "#selected_date" ).datepicker({   
      defaultDate: "+1w",  
      //changeMonth: true,
      //changeYear: true,
      //changeDay: true,
      //showButtonPanel: true,   
     // numberOfMonths: 1,  
      onClose: function( selectedDate ) {  
        var dateVal = document.getElementById("selected_date").value;
        //var customerId = document.getElementById("id").value;
        //alert(Id);
        document.getElementById('orderForm').submit(); 
        /*$.ajax({
		      url: "{{ URL('admin/user/detail') }}/"+Id+"/"+dateVal,
		      type: "GET",
		      contentType: false,
		      cache: false,
		      processData:false,
		    success: function( data, textStatus, jqXHR ) {
		      //window.location.reload();
		      //$("#LoadingProgress").fadeOut('fast');
		    },
		    error: function( jqXHR, textStatus, errorThrown ) {

		    }
		})*/
        //alert("Your typed in " + dateVal);
        //$( "#to_date" ).datepicker( "option", "minDate", selectedDate );  
      }  
    })
    /*$( "#to_date" ).datepicker({
      defaultDate: "+1w",
      //  changeMonth: true,
      //  numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });  */
  }); 


  
</script>
@stop
