@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Trade </h1>
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
            <button type="button" class="btn btn-primary">Back</button>
          </div>
        </div>
        <div class="box-main-content mb-3">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              {!!Form::open(['url'=>'admin/trade/save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!}   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <!-- {!!Form::label('name','Trade Name') !!} -->
                      <lable class="control-label">Trade Name:</lable>
                    </div>
                    <div class="col-md-9 col-12">
                     <!--  <input type="text" name="" class="form-control" placeholder="Enter Trade Name"> -->
                      {!!Form::text('trade_name',null,['class' => 'form-control','placeholder' => 'Enter Trade Name','required' => 'required']) !!}
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
    <!-- /.content -->


@stop


