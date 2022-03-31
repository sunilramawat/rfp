@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Faq </h1>
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
          <div class="box-main-title">Faq List</div>
        </div>
        {!!Form::open(['url'=>'admin/faq/edit-save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!} 
            {!!Form::hidden('id',$Faq->id) !!}
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-12">
              <form>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Question :</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('question',$Faq->question,['class' => 'form-control','placeholder' => 'Enter Question','required' => 'required']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                     
                      <lable class="control-label">Answer :</lable>
                    </div>
                    <div class="col-md-9 col-12">
                    {!!Form::textarea('answer',$Faq->answer,['class' => 'form-control','placeholder' => 'Enter Answer','required' => 'required']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-2 col-12">
                      
                    </div>
                    <div class="col-md-8 col-12">
                    	{!! Form::submit('Update',array('class'=>'btn btn-primary mr-2')) !!}
                      
                      
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

@stop