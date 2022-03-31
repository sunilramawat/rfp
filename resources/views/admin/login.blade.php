
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | Log in</title>
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
            <h4 class="login-title">Log In</h4>
        </div>
        @include('admin.alert_message')
       
        {!!Form::open(['url'=>'admin/do-login','method'=>'post'])!!}
        <div class="form-group input-group mb-3">
            <!-- <label for="">Email Address</label>
            <input type="email" class="form-control" placeholder="Enter Email Address"> -->
            {!!Form::label('email','Email Address') !!}
            {!!Form::text('email',null,['class' => 'form-control','placeholder' => 'Enter Email Address','required' => 'required','maxlength'=>'50']) !!}
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="form-group input-group mb-3">
          <!-- <label for="">Password</label>
          <input type="password" class="form-control" placeholder="Enter Password"> -->
          {!!Form::label('password','Password') !!} 
          {!!Form::password('password',['class' => 'form-control','placeholder' => 'Enter Password','required' => 'required','maxlength'=>'8']) !!}
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group input-group mb-0 mb-sm-3">
              {!!Form::label('captcha','Captcha') !!} 
              <!-- <input type="password" class="form-control border-right" placeholder="Enter Captcha"> -->
              {!!Form::text('captcha',null,['class' => 'form-control border-right','placeholder' => 'Enter captcha','required' => 'required','maxlength'=>'4']) !!}

            </div>
          </div>
          <div class="col-lg-6 6 col-md-6 col-sm-6 mb-2 mb-sm-0">
               <div class="captcha">
                   <span>{!! captcha_img() !!}</span>
                   <a onclick="refreshCaptcha()">       
                   <button type="button" class="refresh-button mt-4 pt-2 reload" id="reload">
                     <img src="{{URL('public/admin/dist/img/refresh.svg')}}" alt="">

                  </button> </a>
                </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-12">
            <div class="form-group">
              <div class="icheck-primary">
              
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              
               <label class="form-check-label" for="remember" >
                      {{ __('Remember Me') }}
               </label>
             </div>
          </div>
      
            <!-- <div class="icheck-primary">
               <input type="checkbox" id="remember">
              <span class="custom-checked">
                  <img src="{{URL('public/admin/dist/img/checked.svg')}}" alt="">
              </span> -
              <label for="remember">
                Remember Me
              </label>
            </div> -->
          </div>
          <!-- /.col -->
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-6 custom-xs-4">
             {!! Form::submit('Login',array('class'=>'btn btn-primary btn-block btn-small')) !!}
            <!-- <button type="submit" class="btn btn-primary btn-block">Login</button> -->
          </div>
          <div class="col-lg-8 col-md-8 col-sm-6  custom-xs-8">
            <p class="forgot-link">
             <a href="{{URL('admin/forgot')}}">Forgot Password?</a>
              
            </p>
          </div>
          <!-- /.col -->
        </div>
      </form>
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
<script type="text/javascript">
    function refreshCaptcha(){

    $("#LoadingProgress").fadeIn('fast');
    $.ajax({
        url: "{{ URL('admin/refreshCaptcha') }}",
        type: "GET",
        contentType: false,
        cache: false,
        processData:false,
      success: function( data, textStatus, jqXHR ) {
        $(".captcha span").html(data.captcha);
      },
      error: function( jqXHR, textStatus, errorThrown ) {

      }
    });
  }

</script>