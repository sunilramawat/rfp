@extends('admin.mainlayout')
@section('content')
  
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Question Answer </h1>
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
          <div class="box-main-title">Add New</div>
          <div class="box-main-top-right">
            <a href="{{URL('admin/faq/view')}}"><button type="button" class="btn btn-primary">Back</button></a>
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
              {!!Form::open(['url'=>'admin/faq/save',  'name' => 'orderForm', 'enctype' => 'multipart/form-data', 'method'=>'post','onsubmit'=>"return validateForm()"]) !!}   
                 <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Question :</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('question',null,['class' => 'form-control','placeholder' => 'Enter Question','required' => 'required']) !!}
                    </div>
                  </div>
                </div>
               <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label"></lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <a href="#" class="addRow"><i class="btn btn-success">Add Answer</i></a>
                    </div>
                  </div>
                </div>
              
             
                <div class="form-group" id="ans" >
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
    <!-- /.content -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">      
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
  function validateForm() {
    var question = document.forms["orderForm"]["question"].value;
    if(question.length > 45){
      alert('The question may not be greater than 45 characters.');
      return false;
    }
    //  var to_date = document.forms["orderForm"]["to_date"].value;
    if (question == ""){
        alert('Please Enter question.');
        return false;
    }
      /*if(to_date == ""){
        document.getElementById('error-to_date').innerHTML = " Please Enter To Date"
        return false;
      }*/
      
    
  } 
  $(document).ready(function () {
    var i=1; 
    $(".addRow").click(function(){
      
      var tr ='<div class="row"  id="row'+i+'">'+
            '<div class="col-md-3 col-12">'+
              '<lable class="control-label">Answer:</lable>'+
            '</div>'+
            '<div class="col-md-6 col-12">'+
              '<input type="answer" name="answer[]" class="form-control answer" required="required">'+
            '</div>'+
            '<div class="col-md-3 col-12">'+
              '<span name="remove[]" rid="'+i+'" class="btn btn-danger btn_remove">Remove</span>'+
            '</div>'+
          '</div>'+
        '</div><br>';
      $('#ans').append(tr);  
        i++; 
    });
  });

        $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("rid");   
           //alert(button_id);
           $('#row'+button_id+'').remove();  

      });  

</script>
@stop


