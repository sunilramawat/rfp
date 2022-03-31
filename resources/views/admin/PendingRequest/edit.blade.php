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
          <div class="box-main-title">Supplier Detail</div>
        </div>
        {!!Form::open(['url'=>'admin/supplier/edit-save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!} 
        {!!Form::hidden('id',$Supplier->id) !!}
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <div class="form-group">
                  <div class="row align-items-end">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Logo:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="manage-supplierdetail-profile"><img id="preview" src="{{URL('public/admin/dist/img/profile.png')}}" /></div>
                      <label class="manage-supplierdetail-browse">
                      
						<input type="file" name="image" onchange="previewImage(this)" accept="image/*"/>
                        
                        <button class="btn btn-primary">Browse</button>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Business Name:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      
                      {!!Form::text('business_name',$Supplier->business_name,['class' => 'form-control','placeholder'=>'Business Name']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Email Address:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                     
                      {!!Form::text('email',$Supplier->email,['class' => 'form-control','placeholder'=>'Email Address']) !!}
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Phone code:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      {!!Form::text('phone_code',$Supplier->phone_code,['class' => 'form-control','placeholder'=>'Phone Code']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Phone Number:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                     
                       {!!Form::text('phone_number',$Supplier->phone_number,['class' => 'form-control','placeholder'=>'Phone Number']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Business Identification:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                     
                       {!!Form::text('business_identification_number',$Supplier->business_identification_number,['class' => 'form-control','placeholder'=>'Business Identification']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Location:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      
                       {!!Form::text('address',$Supplier->address,['class' => 'form-control','placeholder'=>'Location']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Status:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <label class="switch">

                       
                           <input id="toggle" value="1" name="toggle" type="checkbox" {{ $Supplier->is_block === false ? 'checked' : '' }}>
                        <span class="switchslider round"></span>
                      </label>
                       <input id="is_block"  name="is_block" type="hidden">
                     
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      
                    </div>
                    <div class="col-md-8 col-12">
                    	{!! Form::submit('Update',array('class'=>'btn btn-primary mr-2')) !!}
                      <a href="">{!! Form::button('View Products',array('class'=>'btn btn-primary')) !!} </a>
                      
                    </div>
                  </div>
                </div>
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

 $('input[name=toggle]').change(function(){
	var mode1 = $(this).prop('checked') == true ? false : true; 
	 $("#is_block").val(mode1);
	
});
</script>
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
@stop