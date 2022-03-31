<div style="width: 800px; margin: 10px auto;">
	<div class="header_email" style="float: left; width: 100%; background: #01b0f1; height: 62px;min-height: 62px;max-height: 62px;">
        
         <img style="margin:12px;" src="{{ URL('public/images/logo_dashboard.png') }}">  

    </div>
	<div class="center_email" style="float: left; width: 798px; border-left: 1px solid #eeeeee; border-right:1px solid #eeeeee; background:#f5f5f5; min-height:225px">
		<div style="float:left; width: 100%;">&nbsp;</div>
		<div style="display: table; width: 740px; margin: 0 auto;word-wrap: break-word;line-height: 25px;">
			{!! stripslashes( $data['message']) !!}
		</div>
		<div style="float: left; width: 100%;">&nbsp;</div>
	</div>
    <div class="footer_email" style="color: #fff;float: left; width: 100%; background: #47484a;height: 42px;min-height: 42px;max-height: 42px;border-top: 3px solid #3c8dbc;">
        <p style="text-align: center;">
            Copyright ©  @php echo date('Y'); @endphp - @php echo date('Y')+1; @endphp   Hopple All Right Reserved.
        </p>
    </div>
</div>