<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | Recover Password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{URL('public/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{URL('public/admin/dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
  <!-- Custom CSS File -->
  <link rel="stylesheet" href="{{URL('public/admin/docs/assets/css/custom.css')}}">
</head>
<body class="hold-transition login-page">
  <div class="login-logo">
        <a href="">
          <img src="{{URL('public/admin/dist/img/logo-yellow.png')}}" alt="">
        </a>
      </div>
      <!-- /.login-logo -->
<div class="login-box">
  <div class="card">
    <div class="card-body login-card-body">
      
      <div class="login-title-box">
          <h4 class="login-title">Reset Password</h4>
          <p class="login-sub-title">Password must contain atleast 6 characters long.</p>
      </div>

        @include('admin.alert_message')

        {!!Form::open(['url'=>'do-reset-password','method'=>'post'])!!}
          {{ Form::hidden('userid', $id) }}
          {{ Form::hidden('code', $code) }}
        <div class="form-group input-group mb-3">
          {!!Form::label('newpassword','New Password') !!} 
          {!!Form::password('new_password',['class' => 'form-control','placeholder' => 'Enter New Password','required' => 'required' ,'maxlength'=>'8']) !!}

          <!-- <label for="">New Password</label>
          <input type="password" class="form-control" placeholder="Enter Password"> -->
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="form-group input-group mb-3">
          {!!Form::label('confirmassword','Confirm Password') !!} 
          {!!Form::password('confirm_password',['class' => 'form-control','placeholder' => 'Enter Confirm Password','required' => 'required','maxlength'=>'8']) !!}
          <!-- <label for="">Confirm Password</label>
          <input type="password" class="form-control" placeholder="Enter Password"> -->
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 mt-3">
            <!-- {!! Form::submit('Save',array('class'=>'btn btn-primary btn-block')) !!} -->
             <button type="submit" class="btn btn-primary btn-block">Save</button> 
          </div>
          <!-- /.col -->
        </div>
      {!! Form::close() !!}
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{URL('public/admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{URL('public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{URL('public/admin/dist/js/adminlte.min.js')}}"></script>

</body>
</html>
