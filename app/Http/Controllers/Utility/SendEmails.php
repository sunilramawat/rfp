<?php

namespace App\Http\Controllers\Utility;

use App\Mail\sendmail;
use App\Models\EmailContent;
use Illuminate\Support\Facades\Mail;

final class SendEmails{

	public static  function sendUserEmailforgot($to,$name,$newpassword,$forgottype){
	
		if($forgottype == 1){
			$email_record = EmailContent::where('ec_key','=','FORGOT_PASSWORD')->first();
		}else{
			$email_record = EmailContent::where('ec_key','=','FORGOT_USERNAME')->first();
		}

		if (isset($email_record) && !empty($email_record)) {

			$subject = $email_record->ec_subject;
			$content = $email_record->ec_message;
			$content = str_replace("{{receiver}}", '<b>' . $name . '</b>', $content);
			$content = str_replace("{{link}}", '<b>' . $newpassword . '</b>', $content);
			
			$data = array('message'=> $content,'subject' => $subject);
			$SendMail = Mail::to($to)->send(new sendmail($data));	

		}
	

	}


	public static  function sendUserRegisterEmail($to,$name,$code,$forgottype,$userid){
	
		
		$email_record = EmailContent::where('ec_key','=','WELCOME_USER')->first();
		
		if (isset($email_record) && !empty($email_record)) {
			$link  = url('activation').'/'.$userid.'/'.$code;
			
			$subject = $email_record->ec_subject;
			$content = $email_record->ec_message;
			$content = str_replace("{{receiver}}", '<b>' . $name . '</b>', $content);
			//$content = str_replace("{{link}}", '<b>' . $newpassword . '</b>', $content);
			$content = str_replace("{{link}}", '<a href="'.$link.'">' . $link . '</a>', $content);
			$data = array('message'=> $content,'subject' => $subject);
			$SendMail = Mail::to($to)->send(new sendmail($data));	

		}
	

	}	

	public static  function sendContact($to,$email,$phone,$name,$msg){

		//$email_record = EmailContent::where('ec_key','=','FORGOT_PASSWORD')->first();
		
		$subject = 'Contact to Trade Admin?';
		$content = '<p>Hello Admin,</br></br></p>

					<p>You have receive message form application user:</br></br></p>

					<p>Full name : '.$name.'</p>
					<p>Email address : '.$email.'</p>
					<p>Phone : '.@$phone.'</p>
					<p>Message : '.$msg.'</p>

					<p>Regards,</br>
					Trade App  Team</p>';
		
		$data = array('message'=> $content,'subject' => $subject);
		$SendMail = Mail::to($to)->send(new sendmail($data));	
		//print_r($SendMail); exit;
		//echo $to; exit;

	}

	public static  function sendRestPasswordEmail($to,$message,$name,$userId,$code){
	
		
		//$email_record = EmailContent::where('ec_key','=','FORGOT_PASSWORD')->first();
			
			$link  = url('admin-activation').'/'.$userId.'/'.$code;
			$subject = 'Forgot Your Password for Trade Admin?';
			$content = '<p>Hello {{receiver}},</br></br></p>

						<p>It seems you have forgotten your password for your Trade App  account. No worries we have you covered.</p>
						<p>Please use the following Link:</br></br></p>

						<p>{{link}}</br></br></p>

						<p>Regards,</br>
						Trade App  Team</p>';
			$content = str_replace("{{receiver}}", '<b>' . $name . '</b>', $content);
			$content = str_replace("{{link}}", '<a href="'.$link.'">' . $message . '</a>', $content);
			$data = array('message'=> $content,'subject' => $subject);
			echo '<pre>'; print_r($data); exit;
			$SendMail = Mail::to($to)->send(new sendmail($data));	

		}


		public static  function sendApproveEmail($to,$name){
	
		
		//$email_record = EmailContent::where('ec_key','=','FORGOT_PASSWORD')->first();
			
			//$link  = url('admin-activation').'/'.$userId.'/'.$code;
			$subject = 'Welcome to Hopple on Demand';
			$content = "<h3 style='display: block;width: 100%; padding: 5px 0 15px; font-size: 18px; font-weight: 500;color:#000000; margin: 0;line-height: 21px; text-align: center;'>Dear {$name},</h3><p style='-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: 'Roboto, sans-serif;font-size: 14px; color:#000000;margin: 0;padding: 0 0 5px; line-height: 21px;display: block;width: 100%;'>Your account has been activated.<br>Now you can login to your Hopple on Demand account.</p>";
			$content = str_replace("{{receiver}}", '<b>' . $name . '</b>', $content);
			//$content = str_replace("{{link}}", '<a href="'.$link.'">' . $message . '</a>', $content);
			$data = array('message'=> $content,'subject' => $subject);
			//echo '<pre>'; print_r($data); exit;
			$SendMail = Mail::to($to)->send(new sendmail($data));	

		}
	

		
}