@extends('Admin.mainlayout')
@section('content')


<section class="content-header">
    <h1> 
    	<b> Users </b>
    </h1>
    <br>
    <div style="margin-left:20px;">
    <h4><b>Edit Users </b></h4>
</section>

<section class="content">
  <div  >
    <div class="box-body">
    	{!!Form::open(['url'=>'admin/user/save-edit', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!}   
    		<div class="form-group">
              	<div class="col-md-12">
	              	<div class="row">
	              		 {!!Form::hidden('id',$Users->id) !!}
	              		<div class="col-md-2">
	                		{!!Form::label('firstname','First Name') !!} 
	             		</div>
	             		<div class="col-md-6">

	                		{!!Form::text('firstname',$Users->first_name,['class' => 'form-control','placeholder'=>'Please enter first name']) !!}
	                	</div>	
	                </div>	
	            </div>

              	<div class="col-md-12" style="margin-top:20px; ">
	              	<div class="row">
	              		<div class="col-md-2">
	                		{!!Form::label('name','Last Name') !!}
	             		</div>
	             		<div class="col-md-6">

	   			
			                {!!Form::text('lastname',$Users->last_name,['class' => 'form-control','placeholder' => 'Please enter last name']) !!}
			     
	                	</div>	
	                </div>	
	            </div>

	            <div class="col-md-12" style="margin-top:20px; ">
	              	<div class="row">
	              		<div class="col-md-2">
	                		{!!Form::label('email','Email') !!}
	             		</div>
	             		<div class="col-md-6">

	   			
			                {!!Form::text('email',$Users->email,['class' => 'form-control','placeholder' => 'Please enter email']) !!}
			     
	                	</div>	
	                </div>	
	            </div>

	            <div class="col-md-12" style="margin-top:20px; ">
	              	<div class="row">
	              		<div class="col-md-2">
	                		{!!Form::label('status','Status') !!}
	             		</div>
	             		<div class="col-md-6">

			     
	                	</div>	
	                </div>	
	            </div>

	            <div class="col-md-12" style="margin-top:20px; ">
	              	<div class="row">
	              		<div class="col-md-2">
	                		
	             		</div>
	             		<div class="col-md-6">
		             		<div class="row">	
		             			<div class="col-md-2">
		             				{!! Form::submit('Submit',array('class'=>'btn btn-primary')) !!}
				     			</div>

				     			<div class="col-md-2">
		             				<a href="">{!! Form::button('Cancel',array('class'=>'btn btn-light')) !!} </a>
				     			</div>
				     		</div>	
	                	</div>	
	                </div>	
	            </div>

	            
			</div>  
    	{!!Form::close()!!}       	
	</div>
  </div>
</section>    	
	
@stop