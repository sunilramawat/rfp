<?php 

namespace App\Http\Controllers;

final class Msg{

	function responseMsg($msgId){

	  $msg = array();
	  $msg[200] = 'Login successful.';
	  $msg[201] = 'Username available.';//
	  $msg[203] = 'Please verify your entred mobile number';
	  $msg[204] = 'Congratulations! You successfully changed your password.';//
	  $msg[205] = 'Congratulations! You successfully login.';
	  $msg[206] = 'Terms and condition accepted.';
	  $msg[207] = 'User profile.'; 
	  $msg[208] = 'Profile updated successfully.';//same  217 code
	  //$msg[209] = 'An SMS has been sent to you with a verification code. Please use that code to verify your account and complete the registration process.';
	  $msg[209] = 'Your account is not verified. Please check your inbox for the verification link.';
	  $msg[212] = 'Subscription plan list.';
	  $msg[213] = 'Post detail.';
	  $msg[214] = 'Photo deleted.';
	  $msg[215] = 'Match deleted.';
	  $msg[216] = 'Logout successful.';
	  $msg[217] = 'Profile updated successfully.';
	  $msg[218] = 'Post created successfully.';
	  $msg[219] = 'Post liked.';
	  $msg[220] = 'Favourited.';
	  $msg[221] = 'Subscription purchased successfully.';
	  $msg[222] = 'Post reported.';
	  $msg[223] = 'Group created.';
	  $msg[224] = 'Group detail.';
	  $msg[227] = 'Logout successful.';
	  $msg[228] = 'Group join request.';
	  $msg[236] = 'Notification deleted.';
	  $msg[237] = 'Your registration is successful. You can now login into the app.';
	  $msg[248] = 'User deleted successfully.';
	  $msg[252] = 'Thanks, we\'ve successfully added your review!';
	  $msg[262] = 'User dashboard.';
	  $msg[272] = 'User verified successfully.';
	  $msg[273] = 'User details.';
	  $msg[274] = 'User details updated successfully.';
	  $msg[275] = 'Please verify your account. To verify your account check your email for verification code.';
	  $msg[276] = 'Unread count.';
	  $msg[277] = 'Notification list.';
	  $msg[278] = 'Message Sent.';
	  $msg[279] = 'User  registered successfully.';
	  $msg[280] = 'Follow user list.';
	  $msg[281] = 'Group user list.';
	  $msg[288] = 'Page list.';
	  $msg[293] = 'App version check.';
	  $msg[297] = 'All details updated successfully.';
	  $msg[299] = 'Review List.';
	  $msg[300] = 'Question List.';
	  $msg[301] = 'Answered successfully.';
	  $msg[302] = 'Post deleted.';
	  $msg[303] = 'Match detail.';
	  $msg[304] = 'Mark as admin.';
	  $msg[305] = 'Remove as admin.';
	  $msg[306] = 'User remove form group.';
	  $msg[307] = 'Group deleted.';
	  $msg[308] = 'Request accepted.';
	  $msg[309] = 'Request decline.';
	  $msg[310] = 'Request not found.';
	  $msg[311] = 'Msg Sent.';

	 


	  $msg[400] = 'Please provide a valid verification code.';
	  $msg[401] = 'Incorrect phone or password.';
	  //$msg[402] = 'Please verify your account. To verify your account please check your email for verification code.';
	  $msg[402] = 'Your account is still pending verification.  Please check your email (including Spam/Junk folders) and follow the instructions to verify your account.';
	  $msg[403] = 'Please provide all required fields.';
	  $msg[404] = 'User is not verified.';
	  $msg[405] = 'Error in updating user details.';
	  $msg[406] = 'invalid parameter';
	  $msg[407] = 'No contact found';
	  $msg[408] = 'User registration failed';
	  $msg[409] = 'Accepted image formats - png, jpg and jpeg';
	  $msg[410] = 'The email address you have entered already exists.';
	  $msg[411] = 'The phone or password you have entered is incorrect. Please check and try again';
	  $msg[412] = 'Your current password is incorrect. Please check and try again.';
	  $msg[413] = 'User token required';
	  $msg[414] = 'Sorry, your session seems to have expired.';
	  $msg[415] = 'Password and confirm password should be same.';
	  $msg[416] = 'Error in save new password';
	  $msg[417] = 'Error in receiver details.';
	  $msg[418] = 'Fill all required parameter.';
	  $msg[419] = 'This account isn\'t verified';
	  $msg[420] = 'New password cannot be the same as the old password';
	  $msg[421] = 'This email id has already been registered';
	  $msg[422] = 'Invalid code';
	  $msg[423] = 'There was some problem while processing your request. Please try again later.';
	  $msg[424] = 'This Itune Account is already use with another user.';
	  $msg[425] = 'Result not found';
	  $msg[426] = 'This record does not exist.';
	  $msg[427] =  'The credentials you entered are incorrect. Please check and try again.';
	  $msg[428] = 'You have already mark as intrested.';
	  $msg[429] = 'We have not found subscription.';
	  $msg[430] = 'The number you entred is not registered with us! please enter the correct number and try again.';
	  $msg[431] = 'The photo id you entred is not found! please enter the correct id and try again.';
	  
	  $msg[432] = 'Remove Favourite.';
	  $msg[433] = 'Post unliked.';
	  $msg[434] = 'Invalid input.';
	  $msg[435] = 'Failed to add users.';
	  $msg[436] = 'Post already reported.';
	  $msg[437] = 'My group list.';
	  $msg[438] = 'User not found.';
	  $msg[445] = 'No notifications found';
	  $msg[446] = 'No image exists';
	  $msg[447] = 'Account deleted.';
	  $msg[461] = 'Invalid user';
	  $msg[462] = 'You don’t have access to this chat.';
	  $msg[463] = 'You have not received any message.';
	  $msg[468] = 'User verification request failed.';
	  $msg[469] = 'User details update request failed.';
	  $msg[470] = 'Password reset request failed';
	  $msg[471] = 'Make default request failed';
	  $msg[472] = 'Transaction failed.';
	  $msg[497] = 'Invalid request';
	  $msg[498] = 'Your account has been deactivated by the administrator.';
	  $msg[499] = 'Email address and username provided are already registered with us.';
	 
	  $msg[500] = 'The email you entered already exists in the system. If you are trying to create a account for yourself, login with your existing email/phone and password from the login section. Otherwise, please try using a different email to create your account.';
	 
	  $msg[501] = 'The number you entred is already registered with us please try a different phone number.';
	 
	  $msg[502] = 'A verification code has been sent to your email address. Please use the verification code to verify your email address.';
	  $msg[503] = 'A verification code has been sent to your phone number. Please use the verification code to verify your phone number.';
	  $msg[504] = 'It seems that this email id is associated with another username. If you are that user, please login and again tap the link.';
	  $msg[505] = 'Email address provided is already registered with us. Please use new email address for you new registered company account.';
	  $msg[507] = 'Sorry, this email address/phone no is not registered with us. Please check to make sure you entered it correctly, or try a different email address/ phone no.';
	  $msg[508] = 'You cannot access this page because your role is currently awaiting approval. You will be notified as soon as approval is complete.';
	  $msg[509] = 'A verification link has been sent to your email address. Please use the verification link to verify your email address.';
	 
	  $msg[601] = 'An OTP sent to your registered phone. Please use that OTP to verify your account, then follow the instructions to reset your password.'; 
	  $msg[602] = 'An OTP sent to your registered phone number. Please use that OTP to verify your account, then follow the instructions to reset your password.';
	  $msg[603] = 'Your username has been sent to the email address you entered. Please check it and try again.'; 
	  $msg[604] = 'Your username has been sent to the phone number you entered. Please check it and try again.'; 
	  $msg[631] = 'post not found.';
	  $msg[632] = 'Problem occurred in update profile.';
	  $msg[633] = 'Error in uploaded_file.';
	  $msg[634] = 'Error in fetching data.';
	  $msg[636] = 'Email verify successfully.';
 	  $msg[637] = 'Your not verified your email.';
 	  $msg[638] = 'Congratulations! You successfully registered and now you can log in to your account.';
 	  $msg[639] = 'An OTP you entered is incorrect.';
 	  $msg[640] = 'Falied to add chip.';
 	  $msg[641] = 'Category List.';
 	  $msg[642] = 'Logout successfully.';
	  $msg[643] = 'Account has been deleted';
	  $msg[644] = 'The unique id has already been taken';
	  $msg[645] = 'Password updated successfully.';
	  $msg[646] = 'Set Mark Default.';
	  $msg[647] = 'Partner List.';
	  $msg[648] = 'Query sent successfully.';
	  $msg[649] = 'Group request already sent.';



	    if( isset($msg[$msgId]) ){
	        $message = $msg[$msgId];
	    }else{
	        $message = '';
	    }
	    return $message;
	}


}