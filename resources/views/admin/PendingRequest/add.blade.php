@extends('Admin.mainlayout')
@section('content')

<section class="content-header">
      <h1>
        Add Users
        <small>Dashboard</small>
      </h1>
</section>
<section class="content">
  <div class="box with-border" >
    <div class="box-body">
       @include('Admin.alert_message')

        {!!Form::open(['url'=>'admin/company/save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!}    
            <div class="form-group">
              <div class="col-md-12">
                {!!Form::label('name','User Name') !!} <span style="color:red;">*</span>
             
                {!!Form::text('name',null,['class' => 'form-control','placeholder' => 'Please Enter Company Name','required' => 'required']) !!}
     
              </div>
              <div class="col-md-12" style="margin-top: 10px;">
                {!!Form::label('email','Email') !!} 
             
                {!!Form::text('email',null,['class' => 'form-control','placeholder' => 'Please Enter Company Email']) !!}
     
              </div>
              <div class="col-md-12" style="margin-top: 10px;">
                {!!Form::label('url','Website URL') !!} 
             
                {!!Form::text('url',null,['class' => 'form-control','placeholder' => 'Please Enter Website URL']) !!}
     
              </div>

              <div class="col-md-12" style="margin-top: 10px;">
                {!!Form::label('logo','Logo') !!} 
             
                {!!Form::file('logo',null,['class' => 'form-control']) !!}
     
              </div>
              
              <div class="col-md-12" style="margin-top: 20px; float:right;">
                <div class="row">
                    <div class="col-md-1 ">
                      <a href="">{!! Form::button('Cancle',array('class'=>'btn btn-deflaut')) !!} </a>
                    </div>  
                    <div class="col-md-1 ">
                      {!! Form::submit('Save',array('class'=>'btn btn-primary')) !!}
                    </div>
                    
                </div>    
              </div>  
            </div>  
        {!!Form::close()!!}                           
    </div>
  </div>    
</section>  
@stop