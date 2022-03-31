@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">CMS </h1>
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
          <div class="box-main-title">CMS List</div>
        </div>
        {!!Form::open(['url'=>'admin/cms/edit-save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!} 
        {!!Form::hidden('id',$Cms->id) !!}
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-12">
              <form>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-2 col-12">
                      <lable class="control-label">Title:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      
                      {!!Form::text('p_title',$Cms->p_title,['class' => 'form-control','placeholder'=>'Title']) !!}
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-2 col-12">
                      <lable class="control-label">Description:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                       <textarea class="textarea" name="p_description" id="p_description" placeholder="Place some text here">{{$Cms->p_description}}</textarea>
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