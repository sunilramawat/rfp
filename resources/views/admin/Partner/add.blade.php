@extends('admin.mainlayout')
@section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}" />
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Partner </h1>
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
          <div class="box-main-title">Add Partners</div>
          <div class="box-main-top-right">
             <a href="{{URL('admin/partner/view')}}" <button type="button" class="btn btn-primary">Back</button></a>
          </div>
        </div>
          @if ($errors->any())
            <div class="alert alert-danger">
               <ul>
                  @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                  @endforeach
               </ul>
               @if ($errors->has('email'))
               @endif
            </div>
          @endif
        <div class="box-main-content mb-3">
          <div class="row">

            <div class="col-md-12 col-xl-6">
              {!!Form::open(['url'=>'admin/partner/save','name' => 'orderForm' , 'enctype' => 'multipart/form-data', 'method'=>'post' ,'onsubmit'=>"return validateForm()"]) !!}  

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      
                      <lable class="control-label">Photo </lable>
                    </div>
                   
                    <div class="col-md-9 col-12">
                      <div class="row align-items-end">
                      </div>
                    
                    <div class="manage-supplierdetail-profile"><img id="preview" src="" /></div>
                      <label class="manage-supplierdetail-browse">
                      
                      <input type="file" name="image" onchange="previewImage(this)" accept="image/*"/>
                        
                        <button class="btn btn-primary">Browse</button>
                      </label>
                    </div>
                  
                </div>
            </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      
                      <lable class="control-label">Name </lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('name',null,['class' => 'form-control','placeholder' => 'Enter Partner Name','required' => 'required']) !!}
                    </div>
                   
                  
                </div>
            </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Description</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::textarea('desc',null,['class' => 'form-control','placeholder' => 'Enter Description','required' => 'required','cols'=>"50",'rows'=>"5",]) !!}
                    </div>
                  </div>
                </div>
                 <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Region</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                     
                    <select class="form-control" id="region" name="region">
                           <option hidden disabled selected value="">Select Region Type</option>
                            @foreach ($region as $regionregionkey => $neededregion)
                            <option value="{{$regionregionkey}}">{{$neededregion}}</option>
                            @endforeach
                    </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Type</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('type_text',null,['class' => 'form-control','placeholder' => 'Enter type','required' => 'required']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Location</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="location" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('location',null,['class' => 'form-control','placeholder' => 'Enter Location','required' => 'required']) !!}
                   
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      
                      <lable class="control-label">Opening </lable>
                    </div>
                    <div class="col-md-9 col-12">
                     
                    <div class="input-group date" id="opening" data-target-input="nearest">
                      
                       {!!Form::text('opening',null,['class' => 'form-control datetimepicker-input','data-target' => '#opening','required' => 'required']) !!}
                      <div class="input-group-append" data-target="#opening" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="far fa-clock"></i></div>
                      </div>
                      </div>
                    </div>
                </div>
              </div> 
              
             


                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      
                      <lable class="control-label">Closing </lable>
                    </div>
                   
                    <div class="col-md-9 col-12">

                    <div class="input-group date" id="closing" data-target-input="nearest">
                     

                       {!!Form::text('closing',null,['class' => 'form-control datetimepicker-input','data-target' => '#closing','required' => 'required']) !!}
                      <div class="input-group-append" data-target="#closing" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="far fa-clock"></i></div>
                      </div>
                      </div>
                    

                      </div>
                    
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Suitable for</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                    
                        <select  class="form-control" name="suitable" id="suitable">
                         <option hidden disabled selected value="">Select Suitable for</option>
                         <option value="1">Male</option>
                         <option value="2">Female</option>
                         <option value="3">No Pref</option>
                        </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Event Type</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                    
                    <select class="form-control" id="event_type" name="event_type">
                           <option hidden disabled selected value="">Select Event Type</option>
                            @foreach ($eventType as $eventTypekey => $neededeventType)
                            <option value="{{$eventTypekey}}">{{$neededeventType}}</option>
                            @endforeach
                    </select>
                    </div>
                  </div>
                </div>
                <div class="form-group" id="catdiv">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Category</lable>
                    </div>
                    <div class="col-md-9 col-12">
                       <select class="form-control" id="category" name="category">
                            <option  value="">Select Category</option>
                            @foreach ($category as $categorykey => $categoryval)
                            <option value="{{$categorykey}}">{{$categoryval}}</option>
                            @endforeach
                    </select>
                    </div>
                  </div>
                </div>
                <div class="form-group" id="subcatdiv">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Subcategory</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                        <select class="browser-default custom-select" name="subcategory" id="subcategory">
                        </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Premium:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                      <label class="switch">
                           <input id="toggle" value="1" name="toggle" type="checkbox" >
                        <span class="switchslider round"></span>
                      </label>
                       <input id="is_premium"  name="is_premium" type="hidden">
                     
                    </div>
                  </div>
                </div>
                <div class="form-group" id="promocodediv">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Promo code</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('promo_code',null,['class' => 'form-control','placeholder' => 'Enter Promo Code']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group"  id="promodetaildiv">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Promo Detail</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('promo_detail',null,['class' => 'form-control','placeholder' => 'Enter Promo Detail']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Status:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                      <label class="switch">
                       
                           <input id="toggle1" value="1" name="toggle1" type="checkbox" >
                        <span class="switchslider round"></span>
                      </label>
                       <input id="status"  name="status" type="hidden">
                     
                    </div>
                  </div>
                </div>
                <!--  <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Discount:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                      <label class="switch">
                       
                           <input id="toggle2" value="1" name="toggle2" type="checkbox" >
                        <span class="switchslider round"></span>
                      </label>
                       <input id="is_discount"  name="is_discount" type="hidden">
                     
                    </div>
                  </div>
                </div> -->
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Is Recommend:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                      <label class="switch">
                       
                           <input id="toggle3" value="1" name="toggle3" type="checkbox" >
                        <span class="switchslider round"></span>
                      </label>
                       <input id="is_recommend"  name="is_recommend" type="hidden">
                     
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      
                    </div>
                    <div class="col-md-8 col-12">
                      <!--  <a href="">{!! Form::button('Cancle',array('class'=>'btn btn-deflaut')) !!} </a> -->
                       {!! Form::submit('Submit',array('class'=>'btn btn-primary')) !!}
                     <!--  <button type="button" class="btn btn-primary">Submit</button> -->
                      
                    </div>
                  </div>
                </div>
              {!!Form::close()!!}
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  
  
<script type="text/javascript">
  $('#promodetaildiv').hide(); 
  $('#promocodediv').hide(); 
  $('#catdiv').hide(); 
  $('#subcatdiv').hide();
  $('#event_type').on('change',function(e) {
   var event_id = e.target.value;
   //alert(event_id);
   if(event_id == 4){
     $('#catdiv').show(); 
     $('#subcatdiv').show(); 
   }else{
    $('#catdiv').hide(); 
    $('#subcatdiv').hide();
   }
 });
  $('input[name=toggle]').change(function(){
    var mode1 = $(this).prop('checked') == true ? true:  false; 
    $("#is_premium").val(mode1);
    if(mode1 != false){
      $('#promodetaildiv').show(); 
      $('#promocodediv').show(); 
    }else{
      $('#promodetaildiv').hide(); 
      $('#promocodediv').hide();
    }
    //promodetaildiv
  });
  $('input[name=toggle1]').change(function(){
    var mode2 = $(this).prop('checked') == true ? true:  false; 
     $("#status").val(mode2);
  
  });
  $('input[name=toggle2]').change(function(){
    var mode3 = $(this).prop('checked') == true ? true:  false; 
     $("#is_discount").val(mode3);
  
  });
   $('input[name=toggle3]').change(function(){
    var mode4 = $(this).prop('checked') == true ? true:  false; 
     $("#is_recommend").val(mode4);
  
  });
  $(function () {
  
      //Timepicker
    $('#opening').datetimepicker({
      format: 'LT'
    })
    
      //Timepicker
    $('#closing').datetimepicker({
      format: 'LT'
    })

  });

  function previewImage(input) {
    var preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.setAttribute('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    } else {
      preview.setAttribute('src', 'placeholder.png');
    }
  }


  </script> 

  <script type="text/javascript">
    $(document).ready(function () {
     
        $('#category').on('change',function(e) {
         var cat_id = e.target.value;
         $.ajax({
               url:"{{URL('admin/partner/subcat')}}",
               type:"POST",
               data: {
                   cat_id: cat_id
                },
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function (data) {
                console.log(data);
                $('#subcategory').empty();

               $.each(data.subcategories,function(index,subcategory){
                $('#subcategory').append('<option value="'+subcategory.sc_id+'">'+subcategory.sc_name+'</option>');
                })
               }
           })
        });
    });


    ////
    function validateForm() {
      console.log(document.forms["orderForm"]);
    var opening = document.forms["orderForm"]["opening"].value;
    var closing = document.forms["orderForm"]["closing"].value;
    /*if (from_date != "" || to_date != "") {
      if (from_date == ""){
      //alert(from_date);
        document.getElementById('error-from_date').innerHTML = "Please Enter From Date"
        //alert("Please Select From Date");
        return false;
      }
      if(to_date == ""){
          $('#to_date').val(from_date);
        //document.getElementById('error-to_date').innerHTML = "Please Enter To Date"
        //return false;
      }
      
    }*/
    if (opening == "" ) {
      document.getElementById('error-from_date').innerHTML = "Please Enter From Date";
      return false;
    }
  } 
  </script>

        
@stop

