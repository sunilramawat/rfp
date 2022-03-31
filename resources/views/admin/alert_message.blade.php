@if ($errors->any())
<div class="alert alert-danger alert-dismissable validalert" role="alert" style="font-size: 14px;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <ul class="validalert">

        @foreach ($errors->all() as $error)
            @if($error == 'validation.captcha')
               <li>Please fill valid captcha</li>
            @elseif($error == 'The new password format is invalid.')   
                <li>Password must contain one lowercase letter, one number and be atleast 6 characters long.</li>
             @else
                @if($error == 'The new password must be at least 6 characters.')
                @else
                <li>{{ $error }}</li>
                @endif
            @endif
        @endforeach
    </ul>
</div>
@endif

@if(session()->has('error'))
<div class="alert alert-danger alert-dismissable" role="alert" style="font-size: 14px;">
    <i class="fa fa-ban"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    &nbsp; {{ session()->get('error') }}
</div>
@endif

@if(session()->has('success'))
<div class="alert alert-success alert-dismissable" role="alert" style="font-size: 14px;">
    <i class="fa fa-check"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    &nbsp;{{ session()->get('success') }}
</div>
@endif


