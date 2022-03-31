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
          <div class="box-main-title">View Partners</div>
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
              {!!Form::open(['url'=>'admin/partner/edit-save','name' => 'orderForm' , 'enctype' => 'multipart/form-data', 'method'=>'post' ,'onsubmit'=>"return validateForm()"]) !!} 

              {!!Form::hidden('id',$Partner->id) !!} 

                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-8">
                      
                      <lable class="control-label">Photo </lable>
                    </div>
                   
                    <div class="col-md-9 col-12">
                      <div class="row align-items-end">
                      </div>
                    
                      <div class="manage-supplierdetail-profile">
                       @if($Partner->photo !=  '' )         
                              <img id="preview" src="{{URL('public/images/'.$Partner->photo)}}" />
                          @else
                          @endif
                      </div>
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
                      {!!Form::text('name',$Partner->name,['class' => 'form-control','readonly','placeholder' => 'Enter Partner Name','required' => 'required']) !!}
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
                      {!!Form::textarea('desc',$Partner->desc,['class' => 'form-control','readonly','placeholder' => 'Enter Description','required' => 'required','cols'=>"50",'rows'=>"5",]) !!}
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
                     
                    <select class="form-control" id="region" name="region" disabled="disabled">
                           <option hidden disabled selected value="">Select Region Type</option>
                            @foreach ($region as $regionregionkey => $neededregion)
                            <option value="{{$regionregionkey}}" {{ $regionregionkey == $Partner->region ? 'selected' : ''  }}>{{$neededregion}}</option>
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
                      {!!Form::text('type_text',$Partner->type_text,['class' => 'form-control','readonly','placeholder' => 'Enter type','required' => 'required']) !!}
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
                      {!!Form::text('location',$Partner->location,['class' => 'form-control','readonly','placeholder' => 'Enter Location','required' => 'required']) !!}
                   
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
                      
                       {!!Form::text('opening',$Partner->opening,['class' => 'form-control datetimepicker-input','readonly','data-target' => '#opening','required' => 'required']) !!}
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
                     

                       {!!Form::text('closing',$Partner->closing,['class' => 'form-control datetimepicker-input','readonly','data-target' => '#closing','required' => 'required']) !!}
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
                    
                        <select  class="form-control" name="suitable" id="suitable" disabled="disabled">
                         <option hidden disabled selected value="">Select Suitable for</option>
                         <option value="1"  {{ $Partner->suitable == 1 ? 'selected' : '' }}>Male</option>
                         <option value="2" {{ $Partner->suitable == 2 ? 'selected' : '' }}>Female</option>
                         <option value="3" {{ $Partner->suitable == 3 ? 'selected' : '' }}>No Pref</option>
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
                    
                    <select class="form-control" id="event_type" name="event_type" disabled="disabled">
                           <option hidden disabled selected value="">Select Event Type</option>
                            @foreach ($eventType as $eventTypekey => $neededeventType)
                            <option value="{{$eventTypekey}}" {{ $eventTypekey == $Partner->event_type ? 'selected' : '' }}>{{$neededeventType}}</option>
                            @endforeach
                    </select>
                    </div>
                  </div>
                </div>
                <div class="form-group" id="catdiv" >
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Category</lable>
                    </div>
                    <div class="col-md-9 col-12">
                      <select class="form-control" id="category" name="category" disabled="disabled">
                            <option  value="">Select Category</option>
                            @foreach ($category as $categorykey => $categoryval)
                            <option value="{{$categorykey}}" {{ $categorykey == $Partner->category ? 'selected' : '' }}>{{$categoryval}}</option>
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
                        <select class="browser-default custom-select" name="subcategory" id="subcategory" disabled="disabled">
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
                      {{$Partner->is_premium === 1 ? 'premium' : 'Normal' }}
                     
                    </div>
                  </div>
                </div>
                <div class="form-group" id="promocodediv" >
                  <div class="row">
                    <div class="col-md-3 col-8">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Promo code</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('promo_code',$Partner->promo_code,['class' => 'form-control','readonly','placeholder' => 'Enter Promo Code']) !!}
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
                      {!!Form::text('promo_detail',$Partner->promo_detail,['class' => 'form-control','readonly','placeholder' => 'Enter Promo Detail']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Status: </lable>
                    </div>
                    <div class="col-md-9 col-12">
                      {{ $Partner->status === 1 ? 'Active' : 'Unactive' }}
                     
                    </div>
                  </div>
                </div>
                 <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Discount:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                       {{ $Partner->is_discount === 1 ? 'Yes' : 'No' }}
                     
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <lable class="control-label">Recommend:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                       {{ $Partner->is_recommend === 1 ? 'Yes' : 'No' }}
                     
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      
                    </div>
                    <div class="col-md-8 col-12">
                      <!--  <a href="">{!! Form::button('Cancle',array('class'=>'btn btn-deflaut')) !!} </a> -->
                      
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
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  
<script type="text/javascript">
   var event_type_id = "<?php echo $Partner->event_type; ?>" ;
   var is_premium_id = "<?php echo $Partner->is_premium; ?>" ;
   if(event_type_id == 4){
    $('#catdiv').show(); 
    $('#subcatdiv').show();
   }else{
    $('#catdiv').hide(); 
    $('#subcatdiv').hide(); 
   }

  if(is_premium_id  == 1){
       $('#promodetaildiv').show(); 
      $('#promocodediv').show();
   }else{
    $('#promodetaildiv').hide(); 
    $('#promocodediv').hide(); 
   }  
  
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
     
         var cat_id1 = "<?php echo $Partner->category; ?>" ;
         $.ajax({
               url:"{{URL('admin/partner/subcat')}}",
               type:"POST",
               data: {
                   cat_id: cat_id1
                },
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               success:function (data) {
                console.log(data);
                $('#subcategory').empty();

               $.each(data.subcategories,function(index,subcategory){
                $('#subcategory').append('<option value="'+subcategory.sc_id+'">'+subcategory.sc_name+'</option>');
                })
               }
           });
          
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

