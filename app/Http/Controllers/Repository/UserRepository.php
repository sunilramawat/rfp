<?php

namespace App\Http\Controllers\Repository;
use App\User;
use App\Models\Photo;
use App\Models\Post;
use App\Models\GroupPost;
use App\Models\Comment;
use App\Models\GroupComment;
use App\Models\Vote;
use App\Models\GroupVote;
use App\Models\Partner;
use App\Models\Like;
use App\Models\GroupLike;
use App\Models\Follow;
use App\Models\CommentLike;
use App\Models\GroupCommentLike;
use App\Models\Favourite;
use App\Models\GroupFavourite;
use App\Models\Notification;
use App\Models\PendingMatches;
use App\Models\Categories;
use App\Models\Room;
use App\Models\RoomMember;
use App\Models\RoomMessage;
use App\Models\Group;
use App\Models\Debet;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumLike;
use App\Models\ForumComment;
use App\Models\ForumCommentLike;
use App\Models\VoteThemOut;
use App\Models\VoteThemOutLike;
use App\Models\VoteThemOutComment;
use App\Models\VoteThemOutCommentLike;

use App\Models\GroupMember;
use App\Models\SubCategories;
use App\Models\Gender;
use App\Models\Faq;
use App\Models\Answer;
use App\Models\UserAnswer;
use App\Models\ReportList;
use App\Models\Religion;
use App\Models\Report;
use App\Models\PartnerType;
use App\Models\Region;
use App\Models\Subscription;
use App\Models\Transaction;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Service\ApiService;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;
use Carbon\Carbon;	
use Auth;
use DB;

Class UserRepository extends User{

	public function check_user($data){
		if(isset($data['facebook_id'])){
			$user_list = User::Where('facebook_id',@$data['facebook_id'])->first();
			//print_r($user_list); exit;
		}elseif(isset($data['google_id'])){
			$user_list = User::Where('google_id',@$data['google_id'])->first();
		}elseif(isset($data['apple_id'])){
			$user_list = User::Where('apple_id',@$data['apple_id'])->first();
		}elseif(isset($data['email'])){
			$user_list = User::Where('email',@$data['email'])
				->where('user_status','!=',0)->first();
		}else{
			$user_list = User::Where('phone',@$data['phone'])
				->where('user_status','!=',0)->first();
		}

		return $user_list;				
	}

	public function check_unactive_user($data){
		if(isset($data['email'])){
			$user_list = User::where('email',@$data['email'])->first();
		}else{
			$user_list = User::where('phone',@$data['phone'])->first();
		}
		//echo '<pre>'; print_r($user_list); die;
		return $user_list;				
	}

	public function register($data){

		$CustomVerfication = new CustomVerfication();
		$SendEmail = new SendEmails();
		$code = 1234;//$CustomVerfication->generateRandomNumber(4);
		$rescod  = "";
		
		if(!isset($data['id'])){
			$create_user = new User();
			$create_user->username = @$data['username']?@$data['username']:'';
			$create_user->photo = @$data['photo'];
			$create_user->phone = @$data['phone'];
			$create_user->country_code = @$data['country_code'];
			$create_user->user_type = @$data['user_type']?$data['user_type']:1;
			$create_user->bio = @$data['bio'];
			
			/*$create_user->pollitical_orientation = @$data['pollitical_orientation']?$data['pollitical_orientation']:'';
			$create_user->city = @$data['city']?$data['city']:'';
			$create_user->country = @$data['country']?$data['country']:'';
			$create_user->gender = @$data['gender']?$data['gender']:'';
			*/
			$create_user->rank = @$data['rank']?$data['rank']:0;
			$create_user->followers = 0;
			$create_user->followings = 0;
			$create_user->posts = @$data['posts']?$data['posts']:0;
			$create_user->first_name = @$data['first_name'];
			$create_user->last_name = @$data['last_name'];
			$create_user->added_date = date ( 'Y-m-d H:i:s' );
			$create_user->user_status = '0';
			$create_user->is_approved = '0';
			$create_user->user_status = '0';

			$create_user->activation_code = $code;
			$create_user->password = hash::make($code);
			$create_user->is_email_verified = '0';
			$create_user->is_phone_verified = '0';
	        $create_user->last_login= date ( 'Y-m-d H:i:s' );
	        $create_user->token_id = mt_rand(); 
			$create_user->created_at = date ( 'Y-m-d H:i:s' );
			$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		
		}else{
			$create_user = User::find($data['id']);
			$follower_count  = $this->follower_count($data['id']);
        	$following_count  = $this->following_count($data['id']);
			$create_user->username = @$data['username']?$data['username']:$create_user['username'];
			$create_user->photo = @$data['photo']?$data['photo']:$create_user['photo'];
			$create_user->phone = @$data['phone']?$data['phone']:$create_user['phone'];
			$create_user->country_code = @$data['country_code']?$data['country_code']:$create_user['country_code'];
			$create_user->user_type = @$data['user_type']?$data['user_type']:$create_user['user_type'];
			$create_user->bio = @$data['bio']?$data['bio']:$create_user['bio'];
			
			$create_user->pollitical_orientation = @$data['pollitical_orientation']?$data['pollitical_orientation']:intval($create_user['pollitical_orientation']);
			$create_user->city = @$data['city']?$data['city']:intval($create_user['city']);
			$create_user->country = @$data['country']?$data['country']:intval($create_user['country']);
			$create_user->gender = @$data['gender']?$data['gender']:intval($create_user['gender']);
			
			$create_user->rank = @$data['rank']?$data['rank']:$create_user['rank'];
			$create_user->followers = @$follower_count;
			$create_user->followings = @$following_count;
			$create_user->posts = @$data['posts']?$data['posts']:$create_user['posts'];
			$create_user->first_name = @$data['first_name']?$data['first_name']:$create_user['first_name'];
			$create_user->last_name = @$data['last_name']?$data['last_name']:$create_user['last_name'];
			$create_user->added_date = $create_user['added_date'];
			$create_user->user_status = $create_user['user_status'];
			$create_user->is_approved =  $create_user['is_approved'];
			$create_user->activation_code = $code;
			$create_user->password = hash::make($code);
			$create_user->is_email_verified = $create_user['is_email_verified'];
			$create_user->is_phone_verified = $create_user['is_phone_verified'];
	        $create_user->last_login= date ( 'Y-m-d H:i:s' );
	        $create_user->token_id = mt_rand(); 
			$create_user->created_at =  $create_user['created_at'];
			$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		}
		//$create_user->email 	= @$data['email'] ? $data['email']: '';
		//$create_user->password 	= hash::make(@$data['password']) ? hash::make(@$data['password']): '';
		
		$create_user->save();
		$userid = $create_user->id; 
		$message = "Your Rfp verification Code is ". $code;
		
		if(isset($data['phone'])){
			$phone = $data['country_code'].''.$data['phone'];
            $verify_type = 1;
            $create_user->activation_code = $code;
            $user = User::find($userid);
            /*$sidname = getenv("CHAT_ENV").$userid; 
            if(empty($user['sid'])){
            	$chat_sid_create = $CustomVerfication->chat_user($sidname);
            	$user = User::find($userid);
				$user->sid = $chat_sid_create ;
				$user->save();
            }*/
            
				$verify = $CustomVerfication->phoneVerification($message,$phone);
            //$verify = $CustomVerfication->phoneVerification($message,"+917340337597");

		}else{
            $verify_type = 2;
        }

        $data['forgot_type'] = 1;

        if(@$data['email'] != ''){

            $email = $create_user->email;
            $name = $create_user->name;
            $code =  $code;

            //$url =  url("activation/".$code);
			//$newpassword = $url;

            $SendEmail->sendUserRegisterEmail($email,$name,$code,$data['forgot_type'],$userid);
        	
        }

		return $create_user;
	}


	public function social_register($data){
		if(@$data['facebook_id']){
			$code = @$data['facebook_id'];
		}elseif(@$data['google_id']){
			$code = @$data['google_id'];
		}elseif(@$data['apple_id']){
			$code = @$data['apple_id'];
		}



		$CustomVerfication = new CustomVerfication();
		$SendEmail = new SendEmails();
		$rescod  = "";
		
		if(!isset($data['id'])){
			$create_user = new User();
			$follower_count  = 0;
        	$following_count  = 0;
		}else{
			$create_user = User::find($data['id']);
			$follower_count  = $this->follower_count($data['id']);
        	$following_count  = $this->following_count($data['id']);
		}
		//$create_user->email 	= @$data['email'] ? $data['email']: '';
		//$create_user->password 	= hash::make(@$data['password']) ? hash::make(@$data['password']): '';


		$create_user->username = @$data['username'];
		$create_user->bio = @$data['bio'];
		
		$create_user->website = @$data['website'];
		$create_user->fb_link = @$data['fb_link'];
		$create_user->linkedin_link = @$data['linkedin_link'];
		$create_user->twitter_link = @$data['twitter_link'];
		$create_user->Instagram = @$data['Instagram'];
		
		$create_user->rank = @$data['rank']?$data['rank']:0;
		$create_user->followers = @$follower_count;
		$create_user->followings = @$following_count;
		$create_user->posts = @$data['posts']?$data['posts']:0;
		$create_user->user_type =  @$data['user_type']?$data['user_type']:1;
		
		$create_user->facebook_id = @$data['facebook_id'];
		$create_user->google_id = @$data['google_id'];
		$create_user->apple_id = @$data['apple_id'];
		$create_user->first_name = @$data['first_name'];
		$create_user->last_name = @$data['last_name'];
		$create_user->phone = @$data['phone'];
		$create_user->added_date = date ( 'Y-m-d H:i:s' );
		$create_user->user_status = 1;
		$create_user->is_approved = '0';
		$create_user->activation_code = $code;
		$create_user->password = hash::make($code);
		$create_user->is_email_verified = '0';
		$create_user->is_phone_verified = '0';
        $create_user->last_login= date ( 'Y-m-d H:i:s' );
        $create_user->token_id = mt_rand(); 
		$create_user->created_at = date ( 'Y-m-d H:i:s' );
		$create_user->updated_at = date ( 'Y-m-d H:i:s' );
		
		$create_user->save();
		$userid = $create_user->id; 
		
		
        return $userid;
	}

	public function getuser($data){
		if(!empty($data['code'])){
			
			if(isset($data['email'])){
				$query = User::where('activation_code','=',$data['code'])
					->where('email',@$data['email'])
					->first();
			}else{
				$find = 0;
				$query = User::where('activation_code','=',$data['code'])
					->where('phone',@$data['phone'])
					->where('user_status','!=',2)
					->first();
				if(!empty($query)){
					$find = 1;	
				}else{
					$query = User::where('activation_code','=',$data['code'])
					->where('phone_tmp',@$data['phone'])
					->first();
					$find = 2; // to blank phone_tmp and  update in phone
				}

			}	
			if(!empty($query)){
				$user = User::find($query->id);
				//$user->password = Hash::make($data['password']);
		        //$user->activation_code = '';
		        //$user->user_status = 1;
				if(isset($data['email'])){
		            $user->is_email_verified = 1;
		        }else{
		           // $user->is_phone_verified = 1;
		        }
		        if($find == 1){
		        	$user->is_phone_verified  = 1;
		        	$user->user_status  = 1;
		        	
		        }
		        if($find == 2){
		        	$user->phone = $data['phone'];
		        	$user->phone_tmp = '';
		        }

	        	$user->save();



	        	$userData['code'] = 205;
	        	//$userData['email'] = $user->email; 
	        	//$userData['password'] = $user->password; 
	        	$userData['id'] = $user->id; 
	        	$userData['phone'] = $user->phone; 
	        	//$userData['access_token'] = $data['token']; 
		        
			}else{

				$userData['code'] = 422;	

	        }

		}else{

			$userData['code'] = 422;	

		}

		return $userData;
	}

	public function login($data){
		if(!empty($data['phone']))
		{
			$query = User::where('phone',$data['phone'])->first();
		}elseif (!empty($data['email'])) {
		
			$query = User::where('email',$data['email'])->first();			
		
		}else{
		
			$query = User::where('phone',$data['phone'])->where('email',$data['email'])->first();
		
		}
		

		return $query;
	}

	public function  clear_user_token($data){

		$clear_token = User::where('device_id',$data)->first();
		$clear_token->device_id = "";
		$clear_token->save();  
	}

	public function get_user_detail($data)
	{
		$token_id =  mt_rand();
		$query = User::find($data['id']);
		$query->token_id    = $token_id;
        $query->last_login  = date ( 'Y-m-d H:i:s' );
    	$query->device_id   = $data['device_id'];
        $query->device_type = $data['device_type'];
        if(@$data['first_name'] != ''){
        	$query->first_name  = @$data['first_name'];
    	}
    	if(@$data['last_name'] != ''){
       	 $query->last_name 	= @$data['last_name'];
    	}
    	if(@$data['photo'] != ''){
      		$query->photo 		= @$data['photo'];
      	}
      
        $query->save();

    	
        $follower_count  = $this->follower_count($data['id']);
        $following_count  = $this->following_count($data['id']);
        $userdata['username'] = @$query['username']?$query['username']:'';
		$userdata['bio'] = @$query['bio']?$query['bio']:'';
		
		$userdata['pollitical_orientation'] = @$query['pollitical_orientation']?$query['pollitical_orientation']:'';
		$userdata['city'] = @$query['city']?$query['city']:'';
		$userdata['country'] = @$query['country']?$query['country']:'';
		$userdata['gender'] = @$query['gender']?$query['gender']:'';

		$userdata['rank'] = @$query['rank']?$query['rank']:0;
		$userdata['followers'] = @$follower_count;
		$userdata['followings'] = @$following_count;
		$userdata['posts'] = @$query['posts']?$query['posts']:0;
		$userdata['user_type'] =  @$query['user_type']?$query['user_type']:1;

    	$userdata['id'] 		 = $query['id'];
       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $query['device_id']?$query['device_id']:'';
        $userdata['device_type'] = $query['device_type']? intval($query['device_type']):'';
        $userdata['first_name']  = $query['first_name']?$query['first_name']:'';
        $userdata['last_name'] 	 = $query['last_name']?$query['last_name']:'';
        $userdata['device_token']= $query['device_token']?$query['device_token']:'';
        $userdata['access_token']= $data['token'];
        $userdata['user_status'] = $query['user_status']?$query['user_status']:'';
        $userdata['is_active_profile']= $query['is_active_profile']?$query['is_active_profile']:0;
        $userdata['is_notification']= $query['is_notification']?$query['is_notification']:0;
        $userdata['photo']= $query['photo']?$query['photo']:'';
        $userdata['phone']= $query['phone']?$query['phone']:'';
        $userdata['country_code']= $query['country_code']?$query['country_code']:'';
      
	        


		return $userdata;
	}

	public function forgot_password($data,$user){

		$data['forgot_type'] = 1;
		$SendEmail = new SendEmails();
		$getuser = User::find($user->id);
		$PhoneVerification = new CustomVerfication();
		$rescod = "";
		if($data['forgot_type'] == 1){

			if(@$data['phone'] != ''){
		        $pass = 1234;  //mt_rand (1000, 9999) ;
                $getuser->forgot_password_code = $pass;
                $getuser->activation_code  = $pass;

            }else{

                $pass = mt_rand (1000, 9999) ;
                $getuser->forgot_password_code = $pass;
            }

            $getuser->forgot_password_date = date ( 'Y-m-d H:i:s' );
            unset($getuser->password);

            //print_r($getuser);die;
            $getuser->save();


            if(@$data['email'] != ''){
                $email = $getuser->email;
                $name = $getuser->name;
                $newpassword =  $pass;
                $SendEmail->sendUserEmailforgot($email,$name,$newpassword,$data['forgot_type']);
            	$rescod = 601;
            	
            }

            $lastId = $getuser->id;
            $country_code = '';
			$code =  $pass ;

			$message = "Your Pump Tracker verification code is ". $code;

			if(@$data ['phone'] != ''){
                //$verify = $PhoneVerification->phoneVerification($message,$data['phone']);
                $rescod = 601;
            }
		}

		return $rescod;
	}

	public function getdoctor(){

		$getdoctor 	=	User::select('id','name')->where('user_type',1)
						->where('user_status',1)->where('is_approved',1)->get();
		return $getdoctor; 
	}

	public function getuserById($data){
		//print_r($data); exit;
		$user 	=	User::find($data);
		//echo '<pre>'; print_r($user); exit;
		$follower_count  = 0;
        $following_count  =0;
        $post_count  = 0;
        $check_is_follow =0;
		if($user->id != 1){
			$follower_count  = $this->follower_count($user->id);
	        $following_count  = $this->following_count($user->id);
	        $post_count  = $this->post_count($user->id);
	        $check_is_follow  = $this->check_is_follow($user->id);
		}
		$userdata['id'] = $user->id;
	    $userdata['username'] = @$user['username']?$user['username']:'';
        $userdata['phone'] = @$user['phone']?$user['phone']:'';
        $userdata['country_code'] = @$user['country_code']?$user['country_code']:'';
        $userdata['photo'] = @$user['photo']?$user['photo']:'';
		$userdata['bio'] = @$user['bio']?$user['bio']:'';
		$userdata['pollitical_orientation'] = @$user['pollitical_orientation']?$user['pollitical_orientation']:'';
		
		$userdata['city'] = @$user['city']?$user['city']:'';
		$userdata['country'] = @$user['country']?$user['country']:0;
		$userdata['gender'] = @$user['gender']?$user['gender']:0;
		$userdata['rank'] = @$user['rank']?$user['rank']:0;
		$userdata['followers'] = @$follower_count;
		$userdata['followings'] = @$following_count;
		$userdata['is_follow'] = $check_is_follow;
		$userdata['is_verified'] = @$user['is_verified']?$user['is_verified']:0;
		$userdata['posts'] = @$post_count;
		$userdata['user_type'] =  @$user['user_type']?$user['user_type']:1;
       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $user['device_id']?$user['device_id']:'';
        $userdata['device_type'] = $user['device_type']?intval($user['device_type']):'';
        $userdata['first_name']  = $user['first_name']?$user['first_name']:'';
        $userdata['last_name'] 	 = $user['last_name']?$user['last_name']:'';
        $userdata['device_token']= $user['device_token']?$user['device_token']:'';
        //$userdata['access_token']= $user['token'];
        $userdata['user_status'] = $user['user_status']?$user['user_status']:'';
        $userdata['is_active_profile']= $user['is_active_profile']?$user['is_active_profile']:0;
        $userdata['is_notification']= $user['is_notification']?$user['is_notification']:0;
        $userdata['reset_key']= $user['reset_key']?$user['reset_key']:0;
       	//echo '<pre>'; print_r($userdata); exit;
		return $userdata;

	}
	public function getotheruserById($data){
		$follower_count  = $this->follower_count($user->id);
        $following_count  = $this->following_count($user->id);
		$userdata['id'] = $user->id;
        $userdata['username'] = @$user['username']?$user['username']:'';
        $userdata['phone'] = @$user['phone']?$user['phone']:'';
        $userdata['country_code'] = @$user['country_code']?$user['country_code']:'';
        $userdata['photo'] = @$user['photo']?$user['photo']:'';
		$userdata['bio'] = @$user['bio']?$user['bio']:'';
		$userdata['pollitical_orientation'] = @$user['pollitical_orientation']?$user['pollitical_orientation']:'';
		
		$userdata['city'] = @$user['city']?$user['city']:'';
		$userdata['country'] = @$user['country']?$user['country']:'';
		$userdata['gender'] = @$user['gender']?$user['gender']:'';
			


		$userdata['rank'] = @$user['rank']?$user['rank']:0;
		$userdata['followers'] = @$follower_count;
		$userdata['followings'] = @$following_count;
		$userdata['is_follow'] = 0;
		$userdata['is_verified'] = 0;
		$userdata['posts'] = @$user['posts']?$user['posts']:0;
		$userdata['user_type'] =  @$user['user_type']?$user['user_type']:1;

       	$userdata['last_login']  = date ( 'Y-m-d H:i:s' );
        $userdata['device_id'] 	 = $user['device_id']?$user['device_id']:'';
        $userdata['device_type'] = $user['device_type']? intval($user['device_type']):'';
        $userdata['first_name']  = $user['first_name']?$user['first_name']:'';
        $userdata['last_name'] 	 = $user['last_name']?$user['last_name']:'';
        $userdata['device_token']= $user['device_token']?$user['device_token']:'';
        //$userdata['access_token']= $user['token'];
        $userdata['user_status'] = $user['user_status']?$user['user_status']:'';
        $userdata['is_active_profile']= $user['is_active_profile']?$user['is_active_profile']:0;
        $userdata['is_notification']= $user['is_notification']?$user['is_notification']:0;

		//print_r($data);die;
		//	print_r($user);die;
       	// $userData['user_type'] = $user->user_type;
        //$userData['phone'] = $user->phone ? $user->phone : '';
        //$userData['address'] = @$user->address ? $user->address : '';
        //$userData['zip'] = @$user->zip ? $user->zip :'';
       //	$userData['forgot_password_code'] = $user->forgot_password_code ? $user->forgot_password_code : '';
        /*if($user->user_type == 2){
        
        }*/
        
        //$userData['photo'] = @$user->photo ? URL('/public/images/'.@$user->photo) : URL('/public/images/profile.png');
        //$userData['license_photo'] = $user->license_photo ? URL('/public/images/'.@$user->license_photo):'';
        
		return $userData;
	}

	public function getupdateprofile($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		//echo '<pre>'; print_r($user); exit;
		/*if($user->is_email_verified != 1){
	
			$user->email 	= 	@$data['email'] ? $data['email']:$user->email;
		} 	

		if($user->is_phone_verified != 1){

        	$user->phone 	= 	@$data['phone'] ? $data['phone']:$user->phone;
		}*/	
		/*if(isset($data['d_o_b'])){
			$dob = Carbon::createFromFormat('d/m/Y', $data['d_o_b']);
			//print_r($dob); exit;
			$age = 0;
			if(!empty($dob)){
				$age = Carbon::parse($dob)->diff(Carbon::now())->y;
			}
		}*/
		/*if(isset($data['email'])){

			$query = User::where('email',@$data['email'])->where('id','!=',@$data['Id'])->count();

		}else if(isset($data['phone'])){

			$query = User::where('phone',@$data['phone'])->where('id','!=',@$data['Id'])->count();

		}

		$code = 1234;//$CustomVerfication->generateRandomNumber(4);*/
		$is_verify  = 0;
		if($query == 0){
			
			$user->first_name 	= 	@$data['first_name'] ? $data['first_name'] : $user->first_name;
			$user->last_name 	= 	@$data['last_name'] ? $data['last_name'] : $user->last_name;
			$user->user_type 	= 	@$data['user_type'] ? $data['user_type'] : $user->user_type;
			$user->username 	= 	@$data['username'] ? $data['username'] : $user->username;
			$user->bio 	= 	@$data['bio'] ? $data['bio'] : $user->bio;
			$user->pollitical_orientation 	= 	@$data['pollitical_orientation'] ? $data['pollitical_orientation'] : $user->pollitical_orientation;
			$user->photo 	= 	@$data['photo'] ? $data['photo'] : $user->photo;
			$user->city 	= 	@$data['city'] ? $data['city'] : $user->city;
			$user->country 	= 	@$data['country'] ? $data['country'] : $user->country;
			$user->gender 	= 	@$data['gender'] ? $data['gender'] : $user->gender;
			if(@$data['is_notification'] == 0){

				$user->is_notification 	= 	0;
			}else{
				$user->is_notification 	= 	@$data['is_notification'] ? $data['is_notification'] :$user->is_notification;
			}

			/*if($user->is_email_verified == 0){
				$SendEmail = new SendEmails();
				$user->email 	= 	@$data['email'] ? $data['email'] : $user->email;
				$is_verify  = 1;
			}
			if($user->phone == @$data['phone']){

			}else{
				$user->phone_tmp 	= 	@$data['phone'] ? $data['phone'] : $user->phone;
				$message = "Your Hopple verification Code is ". $code;
		
				if(isset($data['phone'])){
					$code = 1234;//$CustomVerfication->generateRandomNumber(4);
					$phone = $data['phone'];
		            $verify_type = 1;
		            $user->activation_code = $code;
					//$verify = $CustomVerfication->phoneVerification($message,$data['phone']);
		            //$verify = $CustomVerfication->phoneVerification($message,"+917340337597");

				}else{
		            $verify_type = 2;
		        }
			}
			if($is_verify  == 1){

	            $email = @$data['email'];
	            $name = $user->first_name;
	            $code =  $code;
	            $user->activation_code = $code;
	            //$url =  url("activation/".$code);
				//$newpassword = $url;

	            $SendEmail->sendUserRegisterEmail($email,$name,$code,0,$data['Id']);
			}*/

		/*	if(@$data['occupation'] == null){
				$user->occupation 			= 	@$data['occupation']?$data['occupation']:'';
			}else{
				$user->occupation 			= 	@$data['occupation']?$data['occupation']:$user->occupation ;

			}
		*/	//dd($data); 
		
			//$user->email 		=	@$data['email'] 	? $data['email'] : $user->email;
	        /*$user->lat 		=	@$data['lat'] ? $data['lat'] : $user->lat;
	        $user->lng 		=	@$data['lng'] ? $data['lng'] : $user->lng;*/
	        //$user->zip 		= 	@$data['zip'] ? $data['zip'] : $user->zip; 
	        
	        /*if (@$data['photo'] != "") {
				$extension_photo = $data['photo']->getClientOriginalExtension();
				if(strtolower($extension_photo) == 'jpg' || strtolower($extension_photo) == 'png' || strtolower($extension_photo) == 'jpeg' ) {
					$FileLogo_photo = time() .'123'.'.' .$data['photo']->getClientOriginalExtension();
					$destinationPath_photo = 'public/images';
					$data['photo']->move($destinationPath_photo, $FileLogo_photo);
					$documentFile_photo = $destinationPath_photo . '/' . $FileLogo_photo;
					$user->photo = $FileLogo_photo;
				}
			}*/		
			//print_r($user); exit;
			$user->save();

			$userData['code'] = 200;
			$userData['id'] = $user->id;
			$follower_count  = $this->follower_count($user->id);
        	$following_count  = $this->following_count($user->id);
	        //$userData['user_type'] = $user->user_type ? $user->user_type : '';
	        $userData['email'] = $user->email ? $user->email : '';
	        $userData['phone'] = $user->phone ? $user->phone : '';
	        $userData['country_code'] = $user->country_code ? $user->country_code : '';
	        $userData['photo'] = $user->photo ? $user->photo : '';
	        $userData['device_id'] = $user->device_id ? $user->device_id :'';
	        $userData['device_type'] = $user->device_type ? intval($user->device_type) : '';
	        $userData['first_name'] = $user->first_name ? $user->first_name : '';
	        $userData['last_name'] = $user->last_name ? $user->last_name : '';
	        $userData['username'] = $user->username ? $user->username : '';
			$userData['bio'] = @$user->bio?$user->bio:'';
	       
			$userData['pollitical_orientation'] = @$user->pollitical_orientation?$user->pollitical_orientation:'';
		
			$userData['city'] = @$user->city?$user->city:'';
			$userData['country'] = @$user->country?$user->country:'';
			$userData['gender'] = @$user->gender?$user->gender:'';

			$userData['rank'] = @$user->rank ? $user->rank : 0;
			$userData['followers'] = @$follower_count;
			$userData['followings'] = @$following_count;
			$userdata['is_follow'] = 0;
			$userdata['is_verified'] = 0;
			$userData['posts'] = @$user->posts ? $user->posts : 0;
			$userData['user_type'] =  @$user->user_type ? intval($user->user_type) : 1 ;

	         
		 	$userData['is_active_profile'] 			= 	 $user->is_active_profile?$user->is_active_profile : 0 ;
			$userData['is_email_verified'] 			= 	 $user->is_email_verified   ? $user->is_email_verified   : 0;

       		$userData['last_login']  = date ( 'Y-m-d H:i:s' );
		    $userData['device_token']= $user->device_token ? $user->device_token : '';
	        //$userdata['access_token']= $user['token'];
	        $userData['user_status'] = $user->user_status ? $user->user_status : '';
	        $userData['is_notification'] = $user->is_notification ? intval($user->is_notification) : 0;
	        //echo '<pre>'; print_r($userData); exit;
		   	}else{

	   		$userData['code'] = 410;
	   	}
	   /*	$sid = getenv("TWILIO_ACCOUNT_SID");
		$token = getenv("TWILIO_AUTH_TOKEN");
		$twilio = new Client($sid, $token);
	   	if(!empty($user->sid)){
		   	$user = $twilio->conversations->v1->users($user_chat_id)
	          ->update([
	                       "friendlyName" => $userData['first_name'],
	                   ]
	          );
     	}*/
		return $userData;
	}

	public function requestVerification($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		
		if($query == 0){
			
			$user->first_name 	= 	@$data['first_name'];
			$user->d_o_b  	= 	@$data['d_o_b '];
			
			$user->is_verified 	= 	1 ;
			if (@$data['upload_docs'] != "") {
				/*$extension_photo = $data['upload_docs']->getClientOriginalExtension();
				if(strtolower($extension_photo) == 'jpg' || strtolower($extension_photo) == 'png' || strtolower($extension_photo) == 'jpeg' ) {
					$FileLogo_photo = time() .'123'.'.' .$data['upload_docs']->getClientOriginalExtension();
					$destinationPath_photo = 'public/images';
					$data['upload_docs']->move($destinationPath_photo, $FileLogo_photo);
					$documentFile_photo = $destinationPath_photo . '/' . $FileLogo_photo;
					$user->upload_docs = $FileLogo_photo;
				}*/
				$user->upload_docs = $data['upload_docs'];
			
			$user->save();

			$userData['code'] = 200;
			
	   		
		   	}else{

		   		$userData['code'] = 410;
		   	}

			return $userData;
		}
	}

	public function visibilty_profile($data){
		
		$user 	=	User::find($data['Id']);
		$query  = 0;
		/*if($user->is_email_verified != 1){

			$user->email 	= 	@$data['email'] ? $data['email']:$user->email;
		} 	

		if($user->is_phone_verified != 1){

        	$user->phone 	= 	@$data['phone'] ? $data['phone']:$user->phone;
		}*/	


		if(isset($data['email'])){

			$query = User::where('email',@$data['email'])->where('id','!=',@$data['Id'])->count();

		}else if(isset($data['phone'])){

			$query = User::where('phone',@$data['phone'])->where('id','!=',@$data['Id'])->count();

		}

		
		if($query == 0){
			
			$user->occupation_status	= 	@$data['occupation_status'] ? $data['occupation_status'] : $user->occupation_status;
			$user->religion_status	= 	@$data['religion_status'] ? $data['religion_status'] : $user->religion_status;
			$user->height_status 	= 	@$data['height_status'] ? $data['height_status'] : $user->height_status;
			$user->pref_willing_to_dutch_status 	= 	@$data['pref_willing_to_dutch_status'] ? $data['pref_willing_to_dutch_status'] : $user->pref_willing_to_dutch_status;
			$user->pref_non_smoker_status 			= 	@$data['pref_non_smoker_status'] ? $data['pref_non_smoker_status'] : $user->pref_non_smoker_status;
			
			$user->save();

			$userData['code'] = 200;
	   	
	   	}else{

	   		$userData['code'] = 410;
	   	}

		return $userData;
	}


	public function create_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			$send_notification = 0;
			if(@$data['post_id']){
				$post = Post::where('id','=',@$data['post_id'])
					->where('u_id','=',$data['userid'])
					->first();
			}else{
				$post = new Post();
				$send_notification = 1;
			}
			
			$post->u_id = @$data['userid'] ? $data['userid']: '';
			$post->category = @$data['category'] ? $data['category']: '';
			$post->post_type = @$data['post_type'] ? $data['post_type']: 0;
			if(@$data['post_type'] == 4){// Poll Post
				$post->poll_one = @$data['poll_one'] ? $data['poll_one']: '';
				$post->poll_two = @$data['poll_two'] ? $data['poll_two']: '';
				$post->poll_three = @$data['poll_three'] ? $data['poll_three']: '';
				$post->poll_four = @$data['poll_four'] ? $data['poll_four']: '';
			}
		
			$post->posted_time = date ( 'Y-m-d H:i:s' );
			$post->description = @$data['description'] ? $data['description']: '';
			$post->created_at =  date ( 'Y-m-d H:i:s' );
			$post->updated_at =  date ( 'Y-m-d H:i:s' );
			if(@$data['imgUrl'] !=  ''){
				$post->imgUrl = @$data['imgUrl'];
			}
			$post->save();
			$lastid = $post->id;
			if(@$data['photo'] !=  ''){ // 2 for Reels 3 For Status
				if (strpos($data['photo'],',') !== false) {	
					//$datanew =  json_decode($data['photo'],true);
					$datanew =explode(',', $data['photo']);
					foreach ($datanew as $photokey => $photovalue) {
						//print($photovalue); exit;
						if(!empty($photovalue)){
							$photo = new Photo();
							$photo->p_u_id = @$data['userid'] ? $data['userid']: '';
							$photo->p_type = @$data['post_type'] ? $data['post_type']: '';
							$photo->p_photo = @$photovalue ? $photovalue: '';
							$photo->post_id = $lastid;
							//print($photo);
							$photo->save();
						}
						//$lastid = $photo->p_id;
					}
				}else{
					$photo = new Photo();
					$photo->p_u_id = @$data['userid'] ? $data['userid']: '';
					$photo->p_type = @$data['post_type'] ? $data['post_type']: '';
					$photo->p_photo = @$data['photo'] ? $data['photo']: '';
					$photo->post_id = $lastid;
					//print($photo);
					$photo->save();
				}	
				
				
			}
			$partner_array['code'] = 200;
			$list = Post::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
					->where('posts.id', $lastid)
					->leftjoin('users','posts.u_id','users.id')
					->first();
			//echo '<pre>';print_r($list); exit;

			
			$is_my_favourite = DB::table('favourities')
	            ->where('post_id','=',$list['id'])
	            ->where('f_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_favourite == 1){

	            $partner_array['post_data']['is_favorited']  =  true;
	        }else{
	            $partner_array['post_data']['is_favorited']  =  false;

	        }


	        $is_my_like = DB::table('likes')
	                        ->where('post_id','=',$list['id'])
	            ->where('l_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_like == 1){

	            $partner_array['post_data']['is_liked']  =  true;
	        }else{
	            $partner_array['post_data']['is_liked']  =  false;

	        }
	        $partner_array['post_data']['is_reposted']  =  false;
			$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
			$partner_array['category']            =   @$list['category'] ? $list['category'] : '';
	        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
	        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
	        
	        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
	        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
	        $partner_array['post_data']['like_count']  =   @$list['like_count'] ? $list['like_count'] : 0;
	        $partner_array['post_data']['favourite_count']  =   @$list['favourite_count'] ? $list['favourite_count'] : 0;
	        $partner_array['post_data']['comment_count']  =   @$list['comment_count'] ? $list['comment_count'] : 0;

	        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
	        $partner_array['post_data']['retweet_count']  =   @$list['retweet_count'] ? $list['retweet_count'] : 0;
	        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
	        
	        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
	        
	        if(!empty($list['poll_one'])){
	            $partner_array['post_data']['options'][0]['id']  =   1;
	            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
	            $partner_array['post_data']['options'][0]['percentage']  =   0;
	            $partner_array['post_data']['options'][0]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_two'])){
	            $partner_array['post_data']['options'][1]['id']  =   2;
	            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
	            $partner_array['post_data']['options'][1]['percentage']  =  0;
	            $partner_array['post_data']['options'][1]['is_voted']  =   0;
	        }
	        if(!empty($list['poll_three'])){
	            $partner_array['post_data']['options'][2]['id']  =   3;
	            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
	            $partner_array['post_data']['options'][2]['percentage']  =  0;
	            $partner_array['post_data']['options'][2]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_four'])){
	            $partner_array['post_data']['options'][3]['id']  =   4;
	            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
	            $partner_array['post_data']['options'][3]['percentage']  =  0;
	            $partner_array['post_data']['options'][3]['is_voted']  =  0;
	            
	        }
	        $photoData = DB::table('photos')
            ->where('post_id','=',$list['id'])
            ->get();
            $photo_array = array();
            $Photo_list = array();
            foreach ($photoData as $photoDatakey => $photoDatavalue) {
                $photo_array['photo_id']  =  @$photoDatavalue->p_id ? $photoDatavalue->p_id : '';
                $photo_array['imgUrl']  =  @$photoDatavalue->p_photo ? $photoDatavalue->p_photo : '';
                array_push($Photo_list,$photo_array);
               
            }
            $partner_array['post_data']['photos']  =   $Photo_list;
	       	if($send_notification  == 1){
	        	$sender = $data['userid'];
	        	$message ="created a new post.";
	        	$n_type = $partner_array['post_type'];
	        	$ref_id = $lastid;//post_id
	        	$push_type = 1; //1 for normal 2 for seclient 
	        	// get follower list and send notification
	        	$ApiService = new ApiService();
	        	$follower = $ApiService->followUser($sender);
	        	if($follower->error_code == 280){
	                $data = $follower->data;   
	                $followerresponseOld = [
	                    'data'  => $data->toArray()    
	                ];
	                 // print_r($Check->data); exit;           
	                //echo '<pre>';print_r($followerresponseOld['data']['data']); exit;
	               
	                foreach($followerresponseOld['data']['data']  as $followerlist){
	                	$userArr = $followerlist['userid'];
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					}
				}
			}
	

		}else{

			$partner_array['code'] = 633;

		}

		return $partner_array;
	}



	public function delete_post($data){
		$deleteanswer =  Post::where('id',$data['post_id'])
		->where('u_id',$data['userid'])
		->delete();	

		$deleteanswerq =  Post::where('repost_id',$data['post_id'])
		->delete();	
		return 1;
	}

	// Post Related Fundtion
	

	// Total follower count on user
	public function follower_count($userid){
		$follower_count = DB::table('follows')
		    ->where('user_id','=',@$userid)
		    ->count();
        return $follower_count;
	}
	// is_follow
	public function check_is_follow($userid){
		$check_is_follow = DB::table('follows')
		    ->where('follow_by','=',Auth::user()->id)
		    ->where('user_id','=',@$userid)
		    ->count();
        return $check_is_follow;
	}

	// Total following count on user
	public function following_count($userid){
		$following_count = DB::table('follows')
		    ->where('follow_by','=',@$userid)
		    ->count();
        return $following_count;
	}

	// Total vote count on post
	public function total_vote_count($postid){
		$total_vote_count = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->count();
        return $total_vote_count;
	}

	// Total vote count on post
	public function vote_count($postid){
		$vote_count_one = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',1)
		    ->count();

		$vote_count_two = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',2)
		    ->count();    

		$vote_count_three = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',3)
		    ->count();    

		$vote_count_four = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',4)
		    ->count();
		
		$total_vote_count = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->count();
		$userId = Auth::user()->id;
		$check_is_vote = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_user_id','=',@$userId)
		    ->first();    
		//echo '<pre>'; print_r($check_is_vote->v_option); exit;   
		$vote_poll['one'] =  $vote_count_one;  
		$vote_poll['two'] =  $vote_count_two;  
		$vote_poll['three'] = $vote_count_three;  
		$vote_poll['four'] =  $vote_count_four;
		$vote_poll['total_vote_count'] =  $total_vote_count;
		$vote_poll['is_voted_one'] = 0 ;
		$vote_poll['is_voted_two'] = 0 ;
		$vote_poll['is_voted_three'] = 0 ;
		$vote_poll['is_voted_four'] = 0 ;
		if(!empty($check_is_vote)){	
			if($check_is_vote->v_option == 1){
				$vote_poll['is_voted_one'] =  1; 
			}elseif($check_is_vote->v_option == 2){
				$vote_poll['is_voted_two'] =  1 ;
			}elseif($check_is_vote->v_option == 3){
				$vote_poll['is_voted_three'] =  1 ;
			}elseif($check_is_vote->v_option == 4){
				$vote_poll['is_voted_four'] =  1 ;
			}else{
				$vote_poll['is_voted_one'] = 0 ;
				$vote_poll['is_voted_two'] = 0 ;
				$vote_poll['is_voted_three'] = 0 ;
				$vote_poll['is_voted_four'] = 0 ;
			}
		}

		if($vote_count_one != 0){
			$vote_poll['one_per'] =  $vote_count_one/$total_vote_count*100;  
		}else{
			$vote_poll['one_per'] =  0;  
		}


		if($vote_count_two != 0){
			$vote_poll['two_per'] =  $vote_count_two/$total_vote_count*100;   
		}else{
			$vote_poll['two_per'] =  0;  
		}

		if($vote_count_three != 0){
			$vote_poll['three_per'] =  $vote_count_three/$total_vote_count*100;   
		}else{
			$vote_poll['three_per'] =  0;  
		}

		if($vote_count_four != 0){
			$vote_poll['four_per'] =  $vote_count_four/$total_vote_count*100;   
		}else{
			$vote_poll['four_per'] =  0;  
		}
		//print_r($vote_poll); exit;
	
        return $vote_poll;
	}
	// like count with name
	public function user_plus_like_count($postid){
		//check own
		$you = 0 ;
		
		    
		//echo '<pre>'; print_r($own_user);
		$user_plus_like_count = DB::table('likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        
		$own_user = DB::table('likes')
		    ->where('post_id','=',@$postid)
		    ->where('l_user_id','=',@Auth::user()->id ? Auth::user()->id : 1)
		    ->first();
		if(!empty($own_user)){
			$own = 'you and';
			$you = 1 ; 
		}else{
			if($user_plus_like_count > 1){
				$own_user = DB::table('likes')
			    ->select('users.*','likes.*')
			    ->where('post_id','=',@$postid)
			    ->leftjoin('users','likes.l_user_id','users.id')
				->first();
				//echo '<pre>'; print_r(); exit;
				$own = $own_user->first_name. ' and';
				$you = 1;
			}
		}

        if($you == 1){
        	$user_plus_like_count = $user_plus_like_count-1;
        	$user_plus_like_count = $own.' '.$user_plus_like_count;
        }else{

        }
        return $user_plus_like_count;
	}
	// Total like count on post
	public function like_count($postid){
		$like_count = DB::table('likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $like_count;
	}
	
	// Total favourite count on post
	public function favourite_count($postid){
		$favourite_count = DB::table('favourities')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $favourite_count;
	}
	// Total Comment count on post
	public function comment_count($postid){
		$comment_count = DB::table('comments')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $comment_count;
	}
	// Retweet Cont on post
	public function repost_count($postid){
		$repost_count = DB::table('posts')
            ->where('repost_id','=',@$postid)
            ->count();    
        return $repost_count;
	}


	//post count
	public function post_count($user_id){
		$post_count = DB::table('posts')
            ->where('u_id','=',@$user_id)
            ->count();    
        return $post_count;
	}

	public function group_post_count_groupId($g_id){
		$group_post_count_groupId = DB::table('group_posts')
            ->where('g_id','=',@$g_id)
            ->count();    
        return $group_post_count_groupId;
	}


	public function check_my_group_state($g_id,$u_id){
		$check_my_group_state = DB::table('group_members')
            ->where('gm_u_id','=',@$u_id)
            ->where('gm_g_id','=',@$g_id)
            ->first();    
        return $check_my_group_state;
	}
	// Get Own like on post
	public function my_like_count($postid,$user_id){
		$is_my_like = DB::table('likes')
            ->where('post_id','=',$postid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($is_my_like == 1){

            $mylike  =  true;
        }else{
            $mylike   =  false;

        }
        return $mylike;
	}
	
	// Get Own favourite on post
	public function is_my_favourite($postid,$user_id){
		$is_my_favourite = DB::table('favourities')
                ->where('post_id','=',@$postid)
                ->where('f_user_id','=',$user_id)
                ->count();
            if($is_my_favourite == 1){

                $is_my_favourite  =  true;
            }else{
                $is_my_favourite  =  false;

            }
            return $is_my_favourite;
    }

    // Total Comment/Reply like count
	public function comment_like_count($commentid){
		$comment_like_count = DB::table('comment_likes')
		    ->where('c_id','=',@$commentid)
		    ->count();
        return $comment_like_count;
	}

	// Get own like  on Comment/Reply
	public function my_comment_like_count($commentid,$user_id){
		$my_comment_like_count = DB::table('comment_likes')
            ->where('c_id','=',$commentid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($my_comment_like_count == 1){

            $mycommentlike  =  true;
        }else{
            $mycommentlike   =  false;

        }
        return $mycommentlike;
	}


	// Total like count on post
	public function member_count($group_id){
		$member_count = DB::table('group_members')
		    ->where('gm_g_id','=',@$group_id)
		    ->where('gm_status',1)
		    ->count();
        return $member_count;
	}

	public function is_group_member($group_id){
		$userId = Auth::user()->id;
		$is_group_member = DB::table('group_members')
		    ->where('gm_g_id','=',@$group_id)
		    ->where('gm_u_id','=',@$userId)
		    ->count();
		   // echo $group_id; exit;
        return $is_group_member;
	}

	
    ////////////////////////Forum All Count//////////////////////////////
	// like count with name
	public function forum_user_plus_like_count($postid){
		//check own
		$you = 0 ;
		
		    
		//echo '<pre>'; print_r($own_user);
		$user_plus_like_count = DB::table('forum_likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        
		$own_user = DB::table('forum_likes')
		    ->where('post_id','=',@$postid)
		    ->where('l_user_id','=',@Auth::user()->id ? Auth::user()->id : 1)
		    ->first();
		if(!empty($own_user)){
			$own = 'you and';
			$you = 1 ; 
		}else{
			if($user_plus_like_count > 1){
				$own_user = DB::table('forum_likes')
			    ->select('users.*','forum_likes.*')
			    ->where('post_id','=',@$postid)
			    ->leftjoin('users','forum_likes.l_user_id','users.id')
				->first();
				//echo '<pre>'; print_r(); exit;
				$own = $own_user->first_name. ' and';
				$you = 1;
			}
		}

        if($you == 1){
        	$user_plus_like_count = $user_plus_like_count-1;
        	$user_plus_like_count = $own.' '.$user_plus_like_count;
        }else{

        }
        return $user_plus_like_count;
	}
	
	// Total like count on post
	public function forum_like_count($postid){
		$like_count = DB::table('forum_likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $like_count;
	}

	// Get Own like on post
	public function forum_my_like_count($postid,$user_id){
		$is_my_like = DB::table('forum_likes')
            ->where('post_id','=',$postid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($is_my_like == 1){

            $mylike  =  true;
        }else{
            $mylike   =  false;

        }
        return $mylike;
	}
	
	// Total Comment count on post
	public function forum_comment_count($postid){
		$comment_count = DB::table('forum_comments')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $comment_count;
	}
	
	 // Total Comment/Reply like count
	public function forum_comment_like_count($commentid){
		$comment_like_count = DB::table('forum_comment_likes')
		    ->where('c_id','=',@$commentid)
		    ->count();
        return $comment_like_count;
	}

	// Get own like  on Comment/Reply
	public function forum_my_comment_like_count($commentid,$user_id){
		$my_comment_like_count = DB::table('forum_comment_likes')
            ->where('c_id','=',$commentid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($my_comment_like_count == 1){

            $mycommentlike  =  true;
        }else{
            $mycommentlike   =  false;

        }
        return $mycommentlike;
	}


    //////////////////////////End Fromum ////////////////////////////////

     ////////////////////////Vote Them out All Count//////////////////////////////
	// like count with name
	public function vote_them_user_plus_like_count($postid){
		//check own
		$you = 0 ;
		
		    
		//echo '<pre>'; print_r($own_user);
		$user_plus_like_count = DB::table('vote_them_out_likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        
		$own_user = DB::table('vote_them_out_likes')
		    ->where('post_id','=',@$postid)
		    ->where('l_user_id','=',@Auth::user()->id ? Auth::user()->id : 1)
		    ->first();
		if(!empty($own_user)){
			$own = 'you and';
			$you = 1 ; 
		}else{
			if($user_plus_like_count > 1){
				$own_user = DB::table('vote_them_out_likes')
			    ->select('users.*','vote_them_out_likes.*')
			    ->where('post_id','=',@$postid)
			    ->leftjoin('users','vote_them_out_likes.l_user_id','users.id')
				->first();
				//echo '<pre>'; print_r(); exit;
				$own = $own_user->first_name. ' and';
				$you = 1;
			}
		}

        if($you == 1){
        	$user_plus_like_count = $user_plus_like_count-1;
        	$user_plus_like_count = $own.' '.$user_plus_like_count;
        }else{

        }
        return $user_plus_like_count;
	}
	
	// Total like count on post
	public function vote_them_like_count($postid){
		$like_count = DB::table('vote_them_out_likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $like_count;
	}

	// Get Own like on post
	public function vote_them_my_like_count($postid,$user_id){
		$is_my_like = DB::table('vote_them_out_likes')
            ->where('post_id','=',$postid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($is_my_like == 1){

            $mylike  =  true;
        }else{
            $mylike   =  false;

        }
        return $mylike;
	}
	
	// Total Comment count on post
	public function vote_them_comment_count($postid){
		$comment_count = DB::table('vote_them_out_comments')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $comment_count;
	}
	
	 // Total Comment/Reply like count
	public function vote_them_comment_like_count($commentid){
		$comment_like_count = DB::table('vote_them_out_comment_likes')
		    ->where('c_id','=',@$commentid)
		    ->count();
        return $comment_like_count;
	}

	// Get own like  on Comment/Reply
	public function vote_them_my_comment_like_count($commentid,$user_id){
		$my_comment_like_count = DB::table('vote_them_out_comment_likes')
            ->where('c_id','=',$commentid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($my_comment_like_count == 1){

            $mycommentlike  =  true;
        }else{
            $mycommentlike   =  false;

        }
        return $mycommentlike;
	}


    //////////////////////////End Vote Them Out //////////////////////////

	////////////////////////Group AllCount///////////////////////////////
	// Total vote count on post
	public function group_total_vote_count($postid){
		$total_vote_count = DB::table('votes')
		    ->where('v_post_id','=',@$postid)
		    ->count();
        return $total_vote_count;
	}
	// Total view count on post
	public function get_view_count($photo_id){
		$like_count = DB::table('view_counts')
		    ->where('v_p_id','=',@$photo_id)
		    ->count();
        return $like_count;
	}

	// Total vote count on post
	public function group_vote_count($postid){
		$vote_count_one = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',1)
		    ->count();

		$vote_count_two = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',2)
		    ->count();    

		$vote_count_three = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',3)
		    ->count();    

		$vote_count_four = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_option','=',4)
		    ->count();
		
		$total_vote_count = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->count();
		$userId = Auth::user()->id;
		$check_is_vote = DB::table('group_votes')
		    ->where('v_post_id','=',@$postid)
		    ->where('v_user_id','=',@$userId)
		    ->first();    
		//echo '<pre>'; print_r($check_is_vote->v_option); exit;   
		$vote_poll['one'] =  $vote_count_one;  
		$vote_poll['two'] =  $vote_count_two;  
		$vote_poll['three'] = $vote_count_three;  
		$vote_poll['four'] =  $vote_count_four;
		$vote_poll['total_vote_count'] =  $total_vote_count;
		$vote_poll['is_voted_one'] = 0 ;
		$vote_poll['is_voted_two'] = 0 ;
		$vote_poll['is_voted_three'] = 0 ;
		$vote_poll['is_voted_four'] = 0 ;
		if(!empty($check_is_vote)){	
			if($check_is_vote->v_option == 1){
				$vote_poll['is_voted_one'] =  1; 
			}elseif($check_is_vote->v_option == 2){
				$vote_poll['is_voted_two'] =  1 ;
			}elseif($check_is_vote->v_option == 3){
				$vote_poll['is_voted_three'] =  1 ;
			}elseif($check_is_vote->v_option == 4){
				$vote_poll['is_voted_four'] =  1 ;
			}else{
				$vote_poll['is_voted_one'] = 0 ;
				$vote_poll['is_voted_two'] = 0 ;
				$vote_poll['is_voted_three'] = 0 ;
				$vote_poll['is_voted_four'] = 0 ;
			}
		}

		if($vote_count_one != 0){
			$vote_poll['one_per'] =  $vote_count_one/$total_vote_count*100;  
		}else{
			$vote_poll['one_per'] =  0;  
		}


		if($vote_count_two != 0){
			$vote_poll['two_per'] =  $vote_count_two/$total_vote_count*100;   
		}else{
			$vote_poll['two_per'] =  0;  
		}

		if($vote_count_three != 0){
			$vote_poll['three_per'] =  $vote_count_three/$total_vote_count*100;   
		}else{
			$vote_poll['three_per'] =  0;  
		}

		if($vote_count_four != 0){
			$vote_poll['four_per'] =  $vote_count_four/$total_vote_count*100;   
		}else{
			$vote_poll['four_per'] =  0;  
		}
		//print_r($vote_poll); exit;
	
        return $vote_poll;
	}
	// Total like count on post
	public function group_like_count($postid){
		$like_count = DB::table('group_likes')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $like_count;
	}
	
	// Total favourite count on post
	public function group_favourite_count($postid){
		$favourite_count = DB::table('group_favourities')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $favourite_count;
	}
	// Total Comment count on post
	public function group_comment_count($postid){
		$comment_count = DB::table('group_comments')
		    ->where('post_id','=',@$postid)
		    ->count();
        return $comment_count;
	}
	// Retweet Cont on post
	public function group_repost_count($postid){
		$repost_count = DB::table('group_posts')
            ->where('repost_id','=',@$postid)
            ->count();    
        return $repost_count;
	}
	//post count
	public function group_post_count($user_id){
		$post_count = DB::table('group_posts')
            ->where('u_id','=',@$user_id)
            ->count();    
        return $post_count;
	}
	// Get Own like on post
	public function group_my_like_count($postid,$user_id){
		$is_my_like = DB::table('group_likes')
            ->where('post_id','=',$postid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($is_my_like == 1){

            $mylike  =  true;
        }else{
            $mylike   =  false;

        }
        return $mylike;
	}
	
	// Get Own favourite on post
	public function group_is_my_favourite($postid,$user_id){
		$is_my_favourite = DB::table('group_favourities')
                ->where('post_id','=',@$postid)
                ->where('f_user_id','=',$user_id)
                ->count();
            if($is_my_favourite == 1){

                $is_my_favourite  =  true;
            }else{
                $is_my_favourite  =  false;

            }
            return $is_my_favourite;
    }

    // Total Comment/Reply like count
	public function group_comment_like_count($commentid){
		$comment_like_count = DB::table('group_comment_likes')
		    ->where('c_id','=',@$commentid)
		    ->count();
        return $comment_like_count;
	}

	// Get own like  on Comment/Reply
	public function group_my_comment_like_count($commentid,$user_id){
		$my_comment_like_count = DB::table('group_comment_likes')
            ->where('c_id','=',$commentid)
            ->where('l_user_id','=',$user_id)
            ->count();
        if($my_comment_like_count == 1){

            $mycommentlike  =  true;
        }else{
            $mycommentlike   =  false;

        }
        return $mycommentlike;
	}

	///////////////////////////////////////////////////////////////////// 

	// Save Notification
	public function notification_save($receiver_id,$notify,$message,$sender_name,$n_type,$receiver_name,$device_token){
		$notification = new Notification();
		$notification->n_u_id = @$receiver_id;
		$notification->n_sender_id = Auth::user()->id;
		$notification->n_type = $n_type;
		$notification->n_data = json_encode($notify);
		$notification->n_message = $message;
		$notification->n_name = $sender_name;
		$notification->n_receiver_name = $receiver_name;
		$notification->n_fcm_token = $device_token;
		$notification->n_status  = 0;
		$notification->n_added_date  =  date ( 'Y-m-d H:i:s' );
		$notification->n_update_date  =  date ( 'Y-m-d H:i:s' );
		$notification->save();
	}

    // Get post detail Model
    public function post_response($postid,$result=null){
    	$list = Post::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
					->where('posts.id', $postid)
					->leftjoin('users','posts.u_id','users.id')
					->first();
		//echo '<pre>';print_r($list); exit;
		    
        
        $like_count  = $this->like_count($postid);
        $favourite_count  = $this->favourite_count($postid);
        $comment_count  = $this->comment_count($postid);
        $repost_count  = $this->repost_count($postid);  
        $is_my_like = $this->my_like_count($postid,Auth::user()->id);      
        $is_my_favourite = $this->is_my_favourite($postid,Auth::user()->id);      
        $user_plus_like_count  = $this->user_plus_like_count($postid);
		$partner_array['result']            =   $result;
		
		

        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
        $partner_array['post_data']['is_liked']  =  $is_my_like;
       
        $partner_array['post_data']['is_reposted']  =  false;
		$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =  $like_count;
        $partner_array['post_data']['user_plus_like_count']  =   $user_plus_like_count;
                        
        $partner_array['post_data']['favourite_count']  =   $favourite_count;
        $partner_array['post_data']['comment_count']  =   $comment_count;

        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
        $partner_array['post_data']['retweet_count']  =  $repost_count;

        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
        $vote_count = $this->vote_count($postid);
        //echo '<pre>'; print_r($vote_count); exit;
        $partner_array['post_data']['total_votes']  =  $vote_count['total_vote_count'];
		//$partner_array['post_data']['is_voted'] =  $vote_count['is_voted'];

        	
			
        //print_r($vote_count); exit;	
        if(!empty($list['poll_one'])){
            $partner_array['post_data']['options'][0]['id']  =   1;
            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
            $partner_array['post_data']['options'][0]['percentage']  =   $vote_count['one_per'];
            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['one'];
            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['is_voted_one'];
        }
        if(!empty($list['poll_two'])){
            $partner_array['post_data']['options'][1]['id']  =   2;
            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count['two_per'];
            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['two'];
            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['is_voted_two'];
        }
        if(!empty($list['poll_three'])){
            $partner_array['post_data']['options'][2]['id']  =   3;
            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count['three_per'];
            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['three'];
            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['is_voted_three'];
        }
        if(!empty($list['poll_four'])){
            $partner_array['post_data']['options'][3]['id']  =   4;
            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count['four_per'];
            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['four'];
            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['is_voted_four'];

            
        }
        return $partner_array;
    } 





	public function comment_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			
			$post = Post::where('id','=',$data['post_id'])
				->where('u_id','=',$data['userid'])
				->first();
			
			$comment = new Comment();
			$comment->u_id = @$data['userid'] ? $data['userid']: '';
			$comment->post_id = @$data['post_id'] ? $data['post_id']: '';
			$send_notification = 1;
			if(@$data['c_id']){
				$send_notification = 2;
				$comment->parent_id = @$data['c_id'] ? $data['c_id']: '';
				$maincomment = Comment::where('c_id', $data['c_id'])->first();
				$comment_user_id  = $maincomment['u_id'];
			}
			
			$comment->description = @$data['description'] ? $data['description']: '';
			$comment->created_at =  date ( 'Y-m-d H:i:s' );
			$comment->updated_at =  date ( 'Y-m-d H:i:s' );
			$comment->save();
			$lastid = $comment->c_id;
			$comment_count= Comment::where('post_id', $data['post_id'])->count();
			$postData 	=	Post::where('id', $data['post_id'])->first();
			$postData->comment_count 	= 	$comment_count ? $comment_count : 0;
			//echo '<pre>'; print_r($postData['u_id']); exit;
			$postData->save();
			$userData['code'] = 200;
			//$userData['c_id'] = @$lastid;

			$commentvalue = Comment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','comments.*')
			->where('comments.c_id', $lastid)
			->leftjoin('users','comments.u_id','users.id')
			->first();
			if(!empty($comment)){
				$partner_array['post_data']['comments'] =array();
				
				$userData['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->comment_like_count($commentvalue['c_id']);
      
				$userData['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $userData['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $userData['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $userData['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $userData['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $userData['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $userData['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $userData['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $userData['is_liked']  =  $myowncommenton;

				
			}
			//send notification to post user
			if($send_notification == 1){ // send notification to post user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $postData['u_id']){
						$sender = $data['userid'];
			        	$message ="Commented on your post.";
			        	$n_type = 4;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $postData['u_id'];
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}else{//Reply notification to  comment user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $comment_user_id){
						$sender = $data['userid'];
			        	$message ="Reply on your comment.";
			        	$n_type = 5;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $comment_user_id;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}

	
		}else{

			$userData['code'] = 633;

		}

		return $userData;
	}

	public function report_text_list($data){

		$category = ReportList::where('status',1)->paginate(100,['*'],'page_no');
	
		
		return $category;
	}

	public function report($arg,$userId){
		$checkreport = Report::where('user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checkreport)){
			$report = new Report();
			$report->user_id = $userId;
			$report->post_id = $arg['post_id'];
			$report->reported_user = intval($arg['reported_user']);
			$report->report_desc = @$arg['desc'];
			//echo '<pre>'; print_r($report); exit;
			$report->save();
			return 1;
		}else{
			return 0;
		}		
	}

	public function repost($data){
		//print_r($data); exit;
		$post_old = Post::where('id','=',@$data['post_id'])
					->first();
		if($post_old['description'] !=  ''){
			
			
			$post = new Post();
			
			
			$post->u_id = @$post_old['u_id'] ? $post_old['u_id']: 0;
			$post->repost_u_id = @$data['userid'] ? $data['userid']: '';
			$post->repost_id = @$post_old['id'] ? $post_old['id']: 0;
			$post->post_type = @$post_old['post_type'] ? $post_old['post_type']: 0;
			$post->post_type = @$post_old['post_type'] ? $post_old['post_type']: 0;
			
			$post->posted_time = date ( 'Y-m-d H:i:s' );
			$post->description = @$data['description'] ? $data['description']: '';
			$post->created_at =  date ( 'Y-m-d H:i:s' );
			$post->updated_at =  date ( 'Y-m-d H:i:s' );
			
			$post->save();
			$lastid = $post->id;

			$partner_array['code'] = 200;
			$list = Post::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
					->where('posts.id', @$data['post_id'])
					->leftjoin('users','posts.u_id','users.id')
					->first();
			//echo '<pre>';print_r($list); exit;

			
			$is_my_favourite = DB::table('favourities')
	            ->where('post_id','=',$list['id'])
	            ->where('f_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_favourite == 1){

	            $partner_array['post_data']['is_favorited']  =  true;
	        }else{
	            $partner_array['post_data']['is_favorited']  =  false;

	        }


	        $is_my_like = DB::table('likes')
	                        ->where('post_id','=',$list['id'])
	            ->where('l_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_like == 1){

	            $partner_array['post_data']['is_liked']  =  true;
	        }else{
	            $partner_array['post_data']['is_liked']  =  false;

	        }
	        $partner_array['post_data']['is_reposted']  =  false;
			$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
	        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
	        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
	        
	        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
	        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
	        $partner_array['post_data']['like_count']  =   @$list['like_count'] ? $list['like_count'] : 0;
	        $partner_array['post_data']['favourite_count']  =   @$list['favourite_count'] ? $list['favourite_count'] : 0;
	        $partner_array['post_data']['comment_count']  =   @$list['comment_count'] ? $list['comment_count'] : 0;

	        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
	        $partner_array['post_data']['retweet_count']  =   @$list['retweet_count'] ? $list['retweet_count'] : 0;
	        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
	        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
	        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
	        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
	        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
	        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
	        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
	        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

	        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
	        
	        if(!empty($list['poll_one'])){
	            $partner_array['post_data']['options'][0]['id']  =   1;
	            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
	            $partner_array['post_data']['options'][0]['percentage']  =   0;
	            $partner_array['post_data']['options'][0]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_two'])){
	            $partner_array['post_data']['options'][1]['id']  =   2;
	            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
	            $partner_array['post_data']['options'][1]['percentage']  =  0;
	            $partner_array['post_data']['options'][1]['is_voted']  =   0;
	        }
	        if(!empty($list['poll_three'])){
	            $partner_array['post_data']['options'][2]['id']  =   3;
	            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
	            $partner_array['post_data']['options'][2]['percentage']  =  0;
	            $partner_array['post_data']['options'][2]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_four'])){
	            $partner_array['post_data']['options'][3]['id']  =   4;
	            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
	            $partner_array['post_data']['options'][3]['percentage']  =  0;
	            $partner_array['post_data']['options'][3]['is_voted']  =  0;
	            
	        }
			/*$userData['code'] = 200;
			$userData['p_id'] = @$lastid;
			$userData['imgUrl'] = @$post_old->imgUrl;
			$userData['post_type'] = @$post_old->post_type;
			if(@$post_old['post_type'] == 3){
				$userData['poll_one'] = @$post_old->poll_one;
				$userData['poll_two'] = @$post_old->poll_two;
				if(@$post_old->poll_three != ''){
					$userData['poll_three'] = @$post_old->poll_three;
				}
				if(@@$post_old->poll_four != ''){
					$userData['poll_four'] = @$post_old->poll_four;
				}
			}
			$userData['created_at'] = @$post->created_at;
			$userData['updated_at'] = @$post->updated_at;
			$userData['u_id'] = @$post->u_id;*/
	

		}

		return $partner_array;
	}

	///////////////////////////////Group Section///////////////////////////////
	public function mygroup_list($data){
		$model 		= "App\Models\Group";	
		$g_type = @$data['g_type'];
		$userId = @$data['userId'];
		$query = $model::query();
			//$userId = Auth::user()->id;

			if(isset($g_type)){
				//echo $selected_date ; exit;
				$query =$query->where('groups.g_type','=',@$g_type);
			}

			if(isset($userId)){
				$query =$query->where('group_members.gm_u_id','=',@$userId);
				$query =$query->where('group_members.gm_status',1);
			}

			$query = $query->select('groups.*','group_members.*')
					->where('groups.g_status',1)
					//->where('group_members.gm_u_id',$userId)
					->leftjoin('group_members','groups.g_id','group_members.gm_g_id')
					->orderBy('groups.g_id', 'DESC')
					->groupBy('groups.g_id')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('groups.g_status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		
		
		return $partner;
	}

	public function createGroup($data){
		//print_r($data); exit;
		$userId = Auth::user()->id;
		if($data['g_title'] !=  ''){
			$send_notification  = 0;
			if(@$data['g_id']){
				$is_new = 0;
				$group = Group::where('g_id','=',@$data['g_id'])
					->first();
				$group->g_id = @$data['g_id'] ? $data['g_id']: 0;	
			}else{
				$is_new = 1;
				$send_notification  = 1;
				$group = new Group();
			}
			$group->g_type = @$data['g_type'] ? $data['g_type']: 0;
			$group->g_photo = @$data['g_photo'] ? $data['g_photo']: '';
			$group->g_title = @$data['g_title'] ? $data['g_title']: '';
			$group->g_desc = @$data['g_desc'] ? $data['g_desc']: '';
			$group->g_desc = @$data['g_desc'] ? $data['g_desc']: '';
			$group->is_free = @$data['is_free'] ? $data['is_free']: 0;
			$group->g_tags = @$data['g_tags'] ? $data['g_tags']: '';
			
			
			$group->g_added_date =  date ( 'Y-m-d H:i:s' );
			//echo '<pre>'; print_r($group); exit;
			$group->save();
			$lastid = $group->g_id;
			if($is_new == 1){// if new group then add first user in this Group as admin
				
				$group_member = new GroupMember();
				$group_member->gm_u_id = @$userId;
				$group_member->gm_g_id = @$lastid;
				$group_member->gm_user_type = 1;
				$group_member->gm_status = 1;
				$group_member->gm_added_date =  date ( 'Y-m-d H:i:s' );
				$group_member->save();
			

				$all_invited_user = explode(',', $data['g_invited_user']);
				//print_r($all_invited_user); exit;
				$user = User::find($userId);
				$sender_name = $user['first_name'];
				foreach ($all_invited_user as $all_invited_userkey => $all_invited_uservalue)
				{
					
					// save invited users
					$group_member = new GroupMember();
					$group_member->gm_u_id = $all_invited_uservalue;
					$group_member->gm_g_id = @$lastid;
					$group_member->gm_user_type = 2;
					$group_member->gm_status = 1;
					$group_member->gm_added_date =  date ( 'Y-m-d H:i:s' );
					$group_member->save();

					$receiver_detail = User::find($all_invited_uservalue);
					$receiver_name = @$receiver_detail['first_name'];
					$fcm_token = @$receiver_detail['fcm_token'];
					
					if($send_notification  == 1){
						$sender = $userId;
						$message ="has joined you in ".$data['g_title'];
						$n_type = 12;
						$ref_id = $lastid;//group id
						$push_type = 1; //1 for normal 2 for seclient 
						// get follower list and send notification
						   
					    $userArr = $all_invited_uservalue;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
							
						
					}

				}
			}
			$partner_array['code'] = 200;
			$partner_array['data'] = $lastid;

			
	        
			
	

		}else{

			$partner_array['code'] = 633;

		}
		//echo '<pre>'; print_r($partner_array); exit;

		return $partner_array;
	}

	public function joinGroup($data){
		//print_r($data); exit;
		$userId = Auth::user()->id;
		if($data['g_id'] !=  ''){
			//$userId = @$data['userId'];
			$lastid = @$data['g_id'];
			$group = Group::where('g_id','=',@$data['g_id'])
					->first();
					//echo '<pre>'; print_r($group['is_free']); exit;
			$checkreport = GroupMember::where('gm_u_id', $userId)->where('gm_g_id', $lastid)->first();
			if(empty($checkreport)){
			
				$group_member = new GroupMember();
				$group_member->gm_u_id = @$userId;
				$group_member->gm_g_id = @$lastid;
				$group_member->gm_user_type = 2;
				if($group['is_free'] ==1){
					$group_member->gm_status = 1;
				}else{
					$group_member->gm_status = 0;

				}
				$group_member->gm_added_date =  date ( 'Y-m-d H:i:s' );
				$group_member->save();
			

				//$all_invited_user = explode(',', $data['userId']);
				//print_r($all_invited_user); exit;
				//$user = User::find($userIdSender);
				//$sender_name = $user['first_name'];
				/*foreach ($all_invited_user as $all_invited_userkey => $all_invited_uservalue)
				{*/
					
					// save invited users
					/*$group_member = new GroupMember();
					$group_member->gm_u_id = $userId;//$all_invited_uservalue;
					$group_member->gm_g_id = @$lastid;
					$group_member->gm_user_type = 2;
					$group_member->gm_status = 0;
					$group_member->gm_added_date =  date ( 'Y-m-d H:i:s' );
					$group_member->save();*/

					/*$receiver_detail = User::find($all_invited_uservalue);
					$receiver_name = @$receiver_detail['first_name'];
					$fcm_token = @$receiver_detail['fcm_token'];
				
					$message =  $sender_name." has invited to you join his group ".$data['g_title'];
					$data['userid'] = $userId;
					$data['name'] = $user['first_name'];
					$data['message'] = $message;
					$data['n_type'] = 1;
					$notify = array ();
					$notify['receiver_id'] = $all_invited_uservalue;
					$notify['relData'] = $data;
					$notify['message'] = $message;
					//print_r($notify); exit;
					$test =  $this->sendPushNotification($notify); 
					$n_type = 1;
					$this->notification_save($all_invited_uservalue,$notify,$message,$sender_name,$n_type,$receiver_name,$fcm_token);*/
					$partner_array['code'] = 200;
				}else{
					$partner_array['code'] = 633;

				}
			}else{//  already  added
				$partner_array['code'] = 649;
			}
		
		//echo '<pre>'; print_r($partner_array); exit;

		return $partner_array;
	}

	public function acceptDecline($arg,$userId){
		$checkfollow = GroupMember::where('gm_id', $arg['gm_id'])
		->where('gm_status', 0)
		->first();
		if(!empty($checkfollow)){
			if($arg['status'] == 1){
				GroupMember::where('gm_id', $arg['gm_id'])
		       		->update([
		           'gm_status' => 1
	        	]);
				$result= 1;
		    }else{ //declne
				$deletefollow =  GroupMember::where('gm_id',$arg['gm_id'])->delete();	
				$result= 3;
		    }
		}else{
			$result = 2;
		}		
		return $result;
	}

	public function groupCancleRequest($arg,$userId){
		$checkfollow = GroupMember::where('gm_g_id', $arg['g_id'])
		->where('gm_u_id', $userId)
		->where('gm_status', 0)
		->first();
		if(!empty($checkfollow)){
				//echo '<pre>';print_r($checkfollow); exit;
				$deletefollow =  GroupMember::where('gm_id',$checkfollow['gm_id'])->delete();	
				$result= 3;
		   
		}else{
			$result = 2;
		}		
		return $result;
	}


	public function group_detail($data){
		$userId = Auth::user()->id;
		$list = Group::where('g_id', $data)
			//->leftjoin('users','posts.u_id','users.id')
			->first();
		$partner_array['g_id']   =   @$list['g_id'] ? $list['g_id'] : 0;
		$partner_array['g_type']   =   @$list['g_type'] ? $list['g_type'] : 0;
		$partner_array['g_photo']   =   @$list['g_photo'] ? $list['g_photo'] : '';
		$partner_array['g_title']   =   @$list['g_title'] ? $list['g_title'] : '';
		$partner_array['g_desc']   =   @$list['g_desc'] ? $list['g_desc'] : '';
		$partner_array['g_status']   =   @$list['g_status'] ? $list['g_status'] : '';
		$partner_array['g_desc']  =   @$list['g_desc'] ? $list['g_desc'] : '';
        $partner_array['is_free']  =   @$list['is_free'] ? $list['is_free'] : '';
        $is_group_member  = $this->is_group_member($list['g_id']);
        $member_count  = $this->member_count($list['g_id']);
        $group_post_count_groupId  = $this->group_post_count_groupId($list['g_id']);
		$partner_array['post_count']   =   $group_post_count_groupId;
		$partner_array['is_group_member']   =   $is_group_member;
        $partner_array['member_count']  =  $member_count;
        $check_my_group_state  = $this->check_my_group_state($list['g_id'],$userId);
        $partner_array['current_group_state']  = 2;
        $partner_array['is_group_admin']  = 0;
        if(!empty($check_my_group_state)){
        	//echo '<pre>'; print_r($check_my_group_state->gm_status); exit;
        	if($check_my_group_state->gm_status == 0){
        		$partner_array['current_group_state']  =  0;
        	}else{
        		$partner_array['current_group_state']  =  $check_my_group_state->gm_status;
        	}

        	if($check_my_group_state->gm_user_type == 1){
       			 $partner_array['is_group_admin']  = 1;
        	}else{
       			 $partner_array['is_group_admin']  = 2;
        	}
        }


			
	
		return $partner_array;
	}

	public function groupUser($data){
		$model 		= "App\Models\GroupMember";	
		$post_type = @$data['post_type'];
		$group_id = @$data['g_id'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_members.*')
				->where('group_members.gm_g_id',$group_id)
				->where('group_members.gm_status',1)
				->leftjoin('users','group_members.gm_u_id','users.id')
				->orderBy('users.first_name', 'ASC')
				->paginate(100,['*'],'page_no');

		$query->total_count = $model::where('group_members.gm_g_id',$group_id)
		->where('group_members.gm_status',1)
				->count();
		$users = $query;
		//echo '<pre>'; print_r($users); exit;
		return $users;
	}

	public function requestList($data){
		$model 		= "App\Models\GroupMember";	
		$post_type = @$data['post_type'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_members.*')
				->where('group_members.gm_status',0)
				->where('group_members.gm_g_id',$data['g_id'])
				//->where('group_members.gm_u_id',$userId)
				->leftjoin('users','group_members.gm_u_id','users.id')
				->orderBy('users.first_name', 'ASC')
				->paginate(100,['*'],'page_no');

		$query->total_count = $model::where('group_members.gm_status',0)
				->where('group_members.gm_g_id',$data['g_id'])
				->count();
		$users = $query;
		return $users;
	}

	public function makeAdmin($arg){

		$group = Group::where('g_id', $arg['g_id'])->first();
		//echo '<pre>'; print_r($group); exit;
		if(!empty($group)){
		
			GroupMember::where('gm_u_id', $arg['userid'])
	       		->update([
	           'gm_user_type' => 1
        	]);
			
			//$userData = Photo::where('p_id', $arg['p_id'])->first();
			$userData['code'] = 200;
			/*$userData['p_id'] = @$userData->p_id;
			$userData['p_photo'] = @$userData->p_photo? URL('/public/images/'.$userData->p_photo):'';
			$userData['p_u_id'] = @$userData->p_u_id;
			$userData['is_default'] = @$userData->is_default;*/
		}else{
			$userData['code'] = 438;
			//print_r($userData); exit;
		}
		return $userData;
	}


	public function removeAdmin($arg){

		$group = Group::where('g_id', $arg['g_id'])->first();
		//echo '<pre>'; print_r($group); exit;
		if(!empty($group)){
		
			GroupMember::where('gm_u_id', $arg['userid'])
	       		->update([
	           'gm_user_type' => 2
        	]);
			
			//$userData = Photo::where('p_id', $arg['p_id'])->first();
			$userData['code'] = 200;
			/*$userData['p_id'] = @$userData->p_id;
			$userData['p_photo'] = @$userData->p_photo? URL('/public/images/'.$userData->p_photo):'';
			$userData['p_u_id'] = @$userData->p_u_id;
			$userData['is_default'] = @$userData->is_default;*/
		}else{
			$userData['code'] = 438;
			//print_r($userData); exit;
		}
		return $userData;
	}

	public function removeGroupUser($data){
		//echo '<pre>'; print_r($data); exit;
		$deleteanswer =  GroupMember::where('gm_u_id',$data['userid'])
		->where('gm_g_id', $data['g_id'])
		->delete();	

		return 1;
	}



	public function deleteGroup($arg){

		$group = Group::where('g_id', $arg['g_id'])->first();
		//echo '<pre>'; print_r($group); exit;
		if(!empty($group)){
		
			GroupMember::where('gm_g_id', $arg['g_id'])
	       		->delete();
			
			Group::where('g_id',$arg['g_id'])
			->delete();	
			//$userData = Photo::where('p_id', $arg['p_id'])->first();
			$userData['code'] = 200;
			/*$userData['p_id'] = @$userData->p_id;
			$userData['p_photo'] = @$userData->p_photo? URL('/public/images/'.$userData->p_photo):'';
			$userData['p_u_id'] = @$userData->p_u_id;
			$userData['is_default'] = @$userData->is_default;*/
		}else{
			$userData['code'] = 438;
			//print_r($userData); exit;
		}
		return $userData;
	}

	public function groupcreatePost($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			$send_notification  = 1;
			if(@$data['post_id']){
				$post = GroupPost::where('id','=',@$data['post_id'])
					->where('u_id','=',$data['userid'])
					->first();
			}else{
				$send_notification  == 1;
				$post = new GroupPost();
			}
			//echo $send_notification; exit;
			$post->u_id = @$data['userid'] ? $data['userid']: '';
			$post->g_id = @$data['g_id'] ? $data['g_id']: '';
			$post->post_type = @$data['post_type'] ? $data['post_type']: 0;
			if(@$data['post_type'] == 3){
				$post->poll_one = @$data['poll_one'] ? $data['poll_one']: '';
				$post->poll_two = @$data['poll_two'] ? $data['poll_two']: '';
				$post->poll_three = @$data['poll_three'] ? $data['poll_three']: '';
				$post->poll_four = @$data['poll_four'] ? $data['poll_four']: '';
			}
			if(@$data['post_type'] == 2){
				$post->stock_name  = @$data['stock_name'] ? $data['stock_name']: '';
				$post->stock_target_price  = @$data['stock_target_price'] ? $data['stock_target_price']: '';
				$post->time_left   = @$data['time_left'] ? $data['time_left']: '';
				$post->term   = @$data['term'] ? $data['term']: '';
				$post->trend   = @$data['trend'] ? $data['trend']: '';
				$post->recommendation   = @$data['recommendation'] ? $data['recommendation']: '';

			}
			$post->posted_time = date ( 'Y-m-d H:i:s' );
			$post->description = @$data['description'] ? $data['description']: '';
			$post->created_at =  date ( 'Y-m-d H:i:s' );
			$post->updated_at =  date ( 'Y-m-d H:i:s' );
			if(@$data['imgUrl'] !=  ''){
				$post->imgUrl = @$data['imgUrl'];
			}
			$post->save();
			$lastid = $post->id;
			$partner_array['code'] = 200;
			$list = GroupPost::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
					->where('group_posts.id', $lastid)
					->leftjoin('users','group_posts.u_id','users.id')
					->first();
			//echo '<pre>';print_r($list); exit;

			
			$is_my_favourite = DB::table('group_favourities')
	            ->where('post_id','=',$list['id'])
	            ->where('f_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_favourite == 1){

	            $partner_array['post_data']['is_favorited']  =  true;
	        }else{
	            $partner_array['post_data']['is_favorited']  =  false;

	        }


	        $is_my_like = DB::table('group_likes')
	                        ->where('post_id','=',$list['id'])
	            ->where('l_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_like == 1){

	            $partner_array['post_data']['is_liked']  =  true;
	        }else{
	            $partner_array['post_data']['is_liked']  =  false;

	        }
	        $partner_array['post_data']['is_reposted']  =  false;
			$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
	        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
	        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
	        
	        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
	        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
	        $partner_array['post_data']['like_count']  =   @$list['like_count'] ? $list['like_count'] : 0;
	        $partner_array['post_data']['favourite_count']  =   @$list['favourite_count'] ? $list['favourite_count'] : 0;
	        $partner_array['post_data']['comment_count']  =   @$list['comment_count'] ? $list['comment_count'] : 0;

	        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
	        $partner_array['post_data']['retweet_count']  =   @$list['retweet_count'] ? $list['retweet_count'] : 0;
	        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
	        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
	        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
	        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
	        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
	        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
	        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
	        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

	        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
	        
	        if(!empty($list['poll_one'])){
	            $partner_array['post_data']['options'][0]['id']  =   1;
	            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
	            $partner_array['post_data']['options'][0]['percentage']  =   0;
	            $partner_array['post_data']['options'][0]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_two'])){
	            $partner_array['post_data']['options'][1]['id']  =   2;
	            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
	            $partner_array['post_data']['options'][1]['percentage']  =  0;
	            $partner_array['post_data']['options'][1]['is_voted']  =   0;
	        }
	        if(!empty($list['poll_three'])){
	            $partner_array['post_data']['options'][2]['id']  =   3;
	            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
	            $partner_array['post_data']['options'][2]['percentage']  =  0;
	            $partner_array['post_data']['options'][2]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_four'])){
	            $partner_array['post_data']['options'][3]['id']  =   4;
	            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
	            $partner_array['post_data']['options'][3]['percentage']  =  0;
	            $partner_array['post_data']['options'][3]['is_voted']  =  0;
	            
	        }

	        if($send_notification  == 1){
	        	$sender = $data['userid'];
	        	if(@$data['post_type'] == 1){
	        		$message ="has created a new post.";
	        		$n_type = 13;
	        	}elseif(@$data['post_type'] == 2){
	        		$message ="has created a forecast.";
	        		$n_type = 15;
	        		
	        	}else{
	        		$message ="has created a post/poll.";
	        		$n_type = 14;
	        	}
	        	
	        	$ref_id = $lastid;//post_id
	        	$push_type = 1; //1 for normal 2 for seclient 
	        	// get follower list and send notification
	        	$post_group['g_id'] = $data['g_id'];
	        	$ApiService = new ApiService();
	        	$Check = $ApiService->groupUser($post_group);
	            if($Check->error_code == 281){
	                $data = $Check->data;   
	                $responseOld = [
	                    'data'  => $data->toArray()    
	                ];
                 // print_r($Check->data); exit;           
                //print_r($Check); exit;
                $user_list['users'] = array();
                foreach($responseOld['data']['data']  as $groupuserlist){
	                	$userArr = $groupuserlist['userid'];
						if($groupuserlist['userid'] != Auth::user()->id){
							$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						}
					}
				}
			}

		}else{

			$partner_array['code'] = 633;

		}

		return $partner_array;
	}

	public function grouprepost($data){
		//print_r($data); exit;
		$post_old = GroupPost::where('id','=',@$data['post_id'])
					->first();
		if($post_old['description'] !=  ''){
			
			
			$post = new GroupPost();
			
			
			$post->u_id = @$post_old['u_id'] ? $post_old['u_id']: 0;
			$post->g_id = @$post_old['g_id'] ? $post_old['g_id']: 0;
			$post->repost_u_id = @$data['userid'] ? $data['userid']: '';
			$post->repost_id = @$post_old['id'] ? $post_old['id']: 0;
			$post->post_type = @$post_old['post_type'] ? $post_old['post_type']: 0;
			$post->post_type = @$post_old['post_type'] ? $post_old['post_type']: 0;
			
			$post->posted_time = date ( 'Y-m-d H:i:s' );
			$post->description = @$data['description'] ? $data['description']: '';
			$post->created_at =  date ( 'Y-m-d H:i:s' );
			$post->updated_at =  date ( 'Y-m-d H:i:s' );
			
			$post->save();
			$lastid = $post->id;

			$partner_array['code'] = 200;
			$list = GroupPost::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
					->where('group_posts.id', @$data['post_id'])
					->leftjoin('users','group_posts.u_id','users.id')
					->first();
			//echo '<pre>';print_r($list); exit;

			
			$is_my_favourite = DB::table('group_favourities')
	            ->where('post_id','=',$list['id'])
	            ->where('f_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_favourite == 1){

	            $partner_array['post_data']['is_favorited']  =  true;
	        }else{
	            $partner_array['post_data']['is_favorited']  =  false;

	        }


	        $is_my_like = DB::table('group_likes')
	                        ->where('post_id','=',$list['id'])
	            ->where('l_user_id','=',Auth::user()->id)
	            ->count();
	        if($is_my_like == 1){

	            $partner_array['post_data']['is_liked']  =  true;
	        }else{
	            $partner_array['post_data']['is_liked']  =  false;

	        }
	        $partner_array['post_data']['is_reposted']  =  false;
			$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
			$partner_array['g_id']            =   @$list['g_id'] ? $list['g_id'] : '';
	        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
	        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
	        
	        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
	        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
	        $partner_array['post_data']['like_count']  =   @$list['like_count'] ? $list['like_count'] : 0;
	        $partner_array['post_data']['favourite_count']  =   @$list['favourite_count'] ? $list['favourite_count'] : 0;
	        $partner_array['post_data']['comment_count']  =   @$list['comment_count'] ? $list['comment_count'] : 0;

	        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
	        $partner_array['post_data']['retweet_count']  =   @$list['retweet_count'] ? $list['retweet_count'] : 0;
	        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
	        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
	        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
	        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
	        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
	        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
	        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
	        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

	        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
	        
	        if(!empty($list['poll_one'])){
	            $partner_array['post_data']['options'][0]['id']  =   1;
	            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
	            $partner_array['post_data']['options'][0]['percentage']  =   0;
	            $partner_array['post_data']['options'][0]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_two'])){
	            $partner_array['post_data']['options'][1]['id']  =   2;
	            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
	            $partner_array['post_data']['options'][1]['percentage']  =  0;
	            $partner_array['post_data']['options'][1]['is_voted']  =   0;
	        }
	        if(!empty($list['poll_three'])){
	            $partner_array['post_data']['options'][2]['id']  =   3;
	            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
	            $partner_array['post_data']['options'][2]['percentage']  =  0;
	            $partner_array['post_data']['options'][2]['is_voted']  =  0;
	        }
	        if(!empty($list['poll_four'])){
	            $partner_array['post_data']['options'][3]['id']  =   4;
	            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
	            $partner_array['post_data']['options'][3]['percentage']  =  0;
	            $partner_array['post_data']['options'][3]['is_voted']  =  0;
	            
	        }
			/*$userData['code'] = 200;
			$userData['p_id'] = @$lastid;
			$userData['imgUrl'] = @$post_old->imgUrl;
			$userData['post_type'] = @$post_old->post_type;
			if(@$post_old['post_type'] == 3){
				$userData['poll_one'] = @$post_old->poll_one;
				$userData['poll_two'] = @$post_old->poll_two;
				if(@$post_old->poll_three != ''){
					$userData['poll_three'] = @$post_old->poll_three;
				}
				if(@@$post_old->poll_four != ''){
					$userData['poll_four'] = @$post_old->poll_four;
				}
			}
			$userData['created_at'] = @$post->created_at;
			$userData['updated_at'] = @$post->updated_at;
			$userData['u_id'] = @$post->u_id;*/
	

		}

		return $partner_array;
	}
	public function groupvote($arg,$userId){
		$checklike = GroupVote::where('v_user_id', $userId)->where('v_post_id', $arg['v_post_id'])->first();
		if(empty($checklike)){
			$vote = new GroupVote();
			$vote->v_user_id = $userId;
			$vote->v_post_id = $arg['v_post_id'];
			$vote->v_option = $arg['v_option'];
			//echo '<pre>'; print_r($like); exit;
			$vote->save();
			$result= 1;
		}else{
			if($arg['v_option'] == $checklike['v_option']){
				$deletelike =  GroupVote::where('v_user_id', $userId)->where('v_post_id', $arg['v_post_id'])->delete();	
				$result = 0;
			}else{
				GroupVote::where('v_id', $checklike['v_id'])
	       		->update([
	           	'v_option' => $arg['v_option']
        		]);	
        		$result = 1;
			}
			
		}		
		
		$partner_array = $this->group_post_response($arg['v_post_id'],$result);
		

		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}

	public function groupdelete_post($data){
		//echo 'dsd'.rand(); exit;
		//echo '<pre>'; print_r($data); exit;
		$deleteanswer =  GroupPost::where('id',$data['post_id'])
		->where('u_id',$data['userid'])
		->delete();	

		$deleteanswerq =  GroupPost::where('repost_id',$data['post_id'])
		->delete();	
		return 1;
	}

	public function group_post_detail($data){
		$checkPost = GroupPost::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
			->where('group_posts.id', $data)
			->leftjoin('users','group_posts.u_id','users.id')
			->first();
		//echo '<pre>';print_r($checkPost); exit;
		$partner_array = array();
		if(!empty($checkPost)){
			 if($checkPost['repost_id'] != ''){
				$data = $checkPost['repost_id'];
			 	$is_repost = true;
			 	$repost_id = 1;
			 }else{
			 	$data = $data;
			 	$is_repost = false;
			 	$repost_id = 0;
			 }		
			$list = GroupPost::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
				->where('group_posts.id', $data)
				->leftjoin('users','group_posts.u_id','users.id')
				->first();
			$partner_array['id']   =   @$list['id'] ? $list['id'] : '';
			
			$postid =  $data;
	            
	        $like_count  = $this->group_like_count($postid);
	        $favourite_count  = $this->group_favourite_count($postid);
	        $comment_count  = $this->group_comment_count($postid);
	        $repost_count  = $this->group_repost_count($postid);  
	        $is_my_like = $this->group_my_like_count($postid,Auth::user()->id);      
	        $is_my_favourite = $this->group_is_my_favourite($postid,Auth::user()->id);      

			

	        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
	        $partner_array['post_data']['is_liked']  =  $is_my_like;
	        
	        $partner_array['post_data']['is_reposted']  =  $is_repost;
	        if($repost_id == 1){
	        	$partner_array['userid']        =   @$checkPost['userid'] ? $checkPost['userid'] : '';
		        $partner_array['picUrl']  =   @$checkPost['picUrl'] ? $checkPost['picUrl'] : '';
		        $partner_array['user_name']  =   @$checkPost['username'] ? $checkPost['username'] : '';
		        $partner_array['first_name']  =   @$checkPost['first_name'] ? $checkPost['first_name'] : '';
		        $partner_array['last_name']  =   @$checkPost['last_name'] ? $checkPost['last_name'] : '';
		        $partner_array['is_verified']  =   @$checkPost['is_verified'] ? $checkPost['is_verified'] : '';
		       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
		        $partner_array['user_type']  =   @$checkPost['user_type'] ? $checkPost['user_type'] : '';

	        }else{
	        	$partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
		        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
		        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
		        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
		        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
		        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
		       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
		        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
	        
	       

	        }
		    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
	        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
	        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
	        $partner_array['post_data']['like_count']  =   @$like_count;
	        $partner_array['post_data']['favourite_count']  =   @$favourite_count;
	        $partner_array['post_data']['comment_count']  =   @$comment_count;
	        $partner_array['post_data']['retweet_count']  =   @$repost_count;

	        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
	        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
	        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
	        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
	        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
	        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
	        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
	        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
	        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

	        //$partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
	        $vote_count = $this->group_vote_count($postid);
	        $partner_array['post_data']['total_votes']  =  $vote_count['total_vote_count'];
	        if(!empty($list['poll_one'])){
	            $partner_array['post_data']['options'][0]['id']  =   1;
	            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
	            $partner_array['post_data']['options'][0]['percentage']  =   $vote_count['one_per'];
	            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['one'];
	            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['is_voted_one'];
	        }
	        if(!empty($list['poll_two'])){
	            $partner_array['post_data']['options'][1]['id']  =   2;
	            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
	            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count['two_per'];
	            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['two'];
	            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['is_voted_two'];
	        }
	        if(!empty($list['poll_three'])){
	            $partner_array['post_data']['options'][2]['id']  =   3;
	            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
	            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count['three_per'];
	            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['three'];
	            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['is_voted_three'];

	        }
	        if(!empty($list['poll_four'])){
	            $partner_array['post_data']['options'][3]['id']  =   4;
	            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
	            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count['four_per'];
	            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['four'];
	            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['is_voted_four'];
	            
	        }
	        $comment = GroupComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_comments.*')
				->where('group_comments.post_id', $data)
				->WhereNull('group_comments.parent_id')
				->leftjoin('users','group_comments.u_id','users.id')
				->get();
			if(!empty($comment)){
				$partner_array['post_data']['comments'] =array();
			
				foreach ($comment as $commentkey => $commentvalue) {
					//print_r($commentvalue['c_id']);
					$partner_array['post_data']['comments'][$commentkey]['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

					$comment_like_count  = $this->group_comment_like_count($commentvalue['c_id']);
	      
					$partner_array['post_data']['comments'][$commentkey]['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
			        $partner_array['post_data']['comments'][$commentkey]['like_count']  =   $comment_like_count;
			       
			        $myowncommenton = $this->group_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
			        $partner_array['post_data']['comments'][$commentkey]['is_liked']  =  $myowncommenton;

			        $reply = GroupComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_comments.*')
					->where('group_comments.parent_id', $commentvalue['c_id'])
					->leftjoin('users','group_comments.u_id','users.id')
					->get();
					if(!empty($reply)){
						$partner_array['post_data']['comments'][$commentkey]['sub_comments']  =array();
						foreach ($reply as $replykey => $replyvalue) {
							$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['id']= $replyvalue['c_id']?$replyvalue['c_id']:0;
							$reply_like_count  = $this->group_comment_like_count($replyvalue['c_id']);
							$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['userid']=   @$replyvalue['userid'] ? $replyvalue['userid'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['picUrl']  =   @$replyvalue['picUrl'] ? $replyvalue['picUrl'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['user_name']  =   @$replyvalue['username'] ? $replyvalue['username'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['first_name']  =   @$replyvalue['first_name'] ? $replyvalue['first_name'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['last_name']  =   @$replyvalue['last_name'] ? $replyvalue['last_name'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['description']  =   @$replyvalue['description'] ? $replyvalue['description'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['posted_time']  =   @$replyvalue['created_at'] ? $replyvalue['created_at'] : '';

					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['like_count']  =   @$reply_like_count;
					      

					        $myownreplyon = $this->group_my_comment_like_count($replyvalue['c_id'],Auth::user()->id);
					        
					        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['is_liked']  =  $myownreplyon;

						}
					}
				}
			}
		}
		//print_r($comment); exit;
		return $partner_array;
	}

	public function groupfavourite($arg,$userId){
		$checklike = Groupfavourite::where('f_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$favourite = new favourite();
			$favourite->f_user_id = $userId;
			$favourite->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$favourite->save();
			$result = 1;
		}else{
			$deletelike =  Groupfavourite::where('f_id',$checklike['f_id'])->delete();	
			$result = 0;
		}		
		
		/*$favourite_count= favourite::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->favourite_count 	= 	$favourite_count ? $favourite_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		$partner_array = $this->group_post_response($arg['post_id'],$result);
		

		return $partner_array;
	}
	public function grouppost_list($data){
		$model 		= "App\Models\GroupPost";	
		$post_type = @$data['post_type'];
		$g_id = @$data['g_id'];
		$query = $model::query();
			

			if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
					->where('status',1)
					->where('group_posts.g_id',$g_id )
					->leftjoin('users','group_posts.u_id','users.id')
					->orderBy('group_posts.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->where('group_posts.g_id',$g_id )
					->count();
			$partner = $query;
			//print_r($partner); exit;
		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}

	public function grouplike($arg,$userId){
		$checklike = GroupLike::where('l_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$like = new GroupLike();
			$like->l_user_id = $userId;
			$like->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  GroupLike::where('l_id',$checklike['l_id'])->delete();	
			$result = 0;
		}		
		
		/*$like_count= Like::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->like_count 	= 	$like_count ? $like_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		
		$partner_array = $this->group_post_response($arg['post_id'],$result);
		if($result == 1){
			
			if(!empty($partner_array['userid'])){
				if($userId != $partner_array['userid']){
					$sender = $userId;
		        	$message ="Liked your post.";
		        	$n_type = 16;
		        	$ref_id = $arg['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $partner_array['userid'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		

		}
		return $partner_array;
	}

	public function groupcomment_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			
			$post = GroupPost::where('id','=',$data['post_id'])
				->where('u_id','=',$data['userid'])
				->first();
			
			$comment = new GroupComment();
			$comment->u_id = @$data['userid'] ? $data['userid']: '';
			$comment->post_id = @$data['post_id'] ? $data['post_id']: '';
			$comment->g_id = @$data['g_id'] ? $data['g_id']: '';
			$send_notification = 1;
			if(@$data['c_id']){
				$send_notification = 2;
				$comment->parent_id = @$data['c_id'] ? $data['c_id']: '';
				$maincomment = GroupComment::where('c_id', $data['c_id'])->first();
				$comment_user_id  = $maincomment['u_id'];
			}
			
			$comment->description = @$data['description'] ? $data['description']: '';
			$comment->created_at =  date ( 'Y-m-d H:i:s' );
			$comment->updated_at =  date ( 'Y-m-d H:i:s' );
			$comment->save();
			$lastid = $comment->c_id;
			$comment_count= GroupComment::where('post_id', $data['post_id'])->count();
			$postData 	=	GroupPost::where('id', $data['post_id'])->first();
			$postData->comment_count 	= 	$comment_count ? $comment_count : 0;
			//print_r($postData); exit;
			$postData->save();
			$userData['code'] = 200;
			//$userData['c_id'] = @$lastid;

			$commentvalue = GroupComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_comments.*')
			->where('group_comments.c_id', $lastid)
			->leftjoin('users','group_comments.u_id','users.id')
			->first();
			if(!empty($comment)){
				$partner_array['post_data']['comments'] =array();
				
				$userData['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->group_comment_like_count($commentvalue['c_id']);
      
				$userData['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $userData['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $userData['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $userData['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $userData['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $userData['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $userData['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $userData['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->group_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $userData['is_liked']  =  $myowncommenton;

				
			}
			//send notification to post user
			if($send_notification == 1){ // send notification to post user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $postData['u_id']){
						$sender = $data['userid'];
			        	$message ="Commented on your post.";
			        	$n_type = 17;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $postData['u_id'];
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}else{//Reply notification to  comment user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $comment_user_id){
						$sender = $data['userid'];
			        	$message ="Reply on your comment.";
			        	$n_type = 18;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $comment_user_id;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}
		
	
		}else{

			$userData['code'] = 633;

		}

		return $userData;
	}
	public function groupcomment_like($arg,$userId){
		$checklike = GroupCommentLike::where('l_user_id', $userId)->where('c_id', $arg['c_id'])->first();
		if(empty($checklike)){
			$like = new GroupCommentLike();
			$like->l_user_id = $userId;
			$like->c_id = $arg['c_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  GroupCommentLike::where('c_id',$checklike['c_id'])->delete();	
			$result = 0;
		}		
		
		
		$postData 	=	GroupComment::where('c_id', $arg['c_id'])->first();
		$partner_array = $this->group_post_detail($postData['post_id']);
		$partner_array['result'] = $result;
		if($result == 1){
			
			// send notification to comment user
			if(!empty($postData['u_id'])){// 
				if($userId != $postData['u_id']){
					$sender = $userId;
					if($postData['parent_id'] == ''){  //Like on comment
		        		$message ="Liked your comment.";
		        		$n_type = 19;
		        	}else{ // like in Reply
		        		$message ="Liked your reply.";
		        		$n_type = 20;
					}
		        	$ref_id = $postData['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $postData['u_id'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		
		}
		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}

	public function forum_topic_list($data){

		$category = ForumTopic::paginate(100,['*'],'page_no');
		
		return $category;
	}

	public function createForum($data){
		//print_r($data); exit;
		$userId = Auth::user()->id;
		$send_notification = 0;
		if($data['title'] !=  ''){
			if(@$data['id']){
				$is_new = 0;
				$forum = Forum::where('id','=',@$data['id'])
					->first();
				$forum->id = @$data['id'] ? $data['id']: 0;	
			}else{
				$is_new = 1;
				$send_notification = 1;
				$forum = new Forum();
			}
			$forum->topic_id = @$data['topic_id'] ? $data['topic_id']: '';
			$forum->photo = @$data['photo'] ? $data['photo']: '';
			$forum->title = @$data['title'] ? $data['title']: '';
			$forum->detail = @$data['detail'] ? $data['detail']: '';
			$forum->created_at =  date ( 'Y-m-d H:i:s' );
			$forum->updated_at =  date ( 'Y-m-d H:i:s' );
			
			//echo '<pre>'; print_r($room); exit;
			$forum->save();

			$lastid = $forum->id;

			
			$partner_array['code'] = 200;
			$partner_array['data'] = $lastid;

		
		}else{

			$partner_array['code'] = 633;

		}
		//echo '<pre>'; print_r($partner_array); exit;

		return $partner_array;
	}


	public function forum_list($data){
		$model 		= "App\Models\Forum";	
		$topic_id = @$data['topic_id'];
		$query = $model::query();
			

			if(isset($topic_id)){
				//echo $selected_date ; exit;
				$query =$query->where('topic_id','=',@$topic_id);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','users.pollitical_orientation','forums.*')
					->where('status',1)
					->leftjoin('users','forums.u_id','users.id')
					->orderBy('forums.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;
			//echo '<pre>';  print_r($partner); exit;
		
		return $partner;
	}


	public function forum_like($arg,$userId){
		$checklike = ForumLike::where('l_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$like = new ForumLike();
			$like->l_user_id = $userId;
			$like->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  ForumLike::where('l_id',$checklike['l_id'])->delete();	
			$result = 0;
		}		
		
		/*$like_count= Like::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->like_count 	= 	$like_count ? $like_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		
		$partner_array = $this->forum_response($arg['post_id'],$result);
		// send notification to post owner
		if($result == 1){
			
			if(!empty($partner_array['userid'])){
				if($userId != $partner_array['userid']){
					$sender = $userId;
		        	$message ="Liked your forum.";
		        	$n_type = 6;
		        	$ref_id = $arg['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $partner_array['userid'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		

		}

		return $partner_array;
	}


	public function forum_comment_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			
			$post = Forum::where('id','=',$data['post_id'])
				->where('u_id','=',$data['userid'])
				->first();
			
			$comment = new ForumComment();
			$comment->u_id = @$data['userid'] ? $data['userid']: '';
			$comment->post_id = @$data['post_id'] ? $data['post_id']: '';
			$send_notification = 1;
			if(@$data['c_id']){
				$send_notification = 2;
				$comment->parent_id = @$data['c_id'] ? $data['c_id']: '';
				$maincomment = ForumComment::where('c_id', $data['c_id'])->first();
				$comment_user_id  = $maincomment['u_id'];
			}
			
			$comment->description = @$data['description'] ? $data['description']: '';
			$comment->created_at =  date ( 'Y-m-d H:i:s' );
			$comment->updated_at =  date ( 'Y-m-d H:i:s' );
			$comment->save();
			$lastid = $comment->c_id;
			$comment_count= ForumComment::where('post_id', $data['post_id'])->count();
			$postData 	=	Forum::where('id', $data['post_id'])->first();
			//$postData->comment_count 	= 	$comment_count ? $comment_count : 0;
			//echo '<pre>'; print_r($postData['u_id']); exit;
			$postData->save();
			$userData['code'] = 200;
			//$userData['c_id'] = @$lastid;

			$commentvalue = ForumComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forum_comments.*')
			->where('forum_comments.c_id', $lastid)
			->leftjoin('users','forum_comments.u_id','users.id')
			->first();
			if(!empty($comment)){
				$partner_array['post_data']['comments'] =array();
				
				$userData['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->comment_like_count($commentvalue['c_id']);
      
				$userData['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $userData['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $userData['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $userData['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $userData['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $userData['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $userData['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $userData['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->forum_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $userData['is_liked']  =  $myowncommenton;

				
			}
			//send notification to post user
			if($send_notification == 1){ // send notification to post user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $postData['u_id']){
						$sender = $data['userid'];
			        	$message ="Commented on your post.";
			        	$n_type = 4;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $postData['u_id'];
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}else{//Reply notification to  comment user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $comment_user_id){
						$sender = $data['userid'];
			        	$message ="Reply on your forum comment.";
			        	$n_type = 5;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $comment_user_id;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}

	
		}else{

			$userData['code'] = 633;

		}

		return $userData;
	}


	public function forum_comment_like($arg,$userId){
		$checklike = ForumCommentLike::where('l_user_id', $userId)->where('c_id', $arg['c_id'])->first();
		if(empty($checklike)){
			$like = new ForumCommentLike();
			$like->l_user_id = $userId;
			$like->c_id = $arg['c_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  ForumCommentLike::where('c_id',$checklike['c_id'])->delete();	
			$result = 0;
		}		
		
		
		$postData 	=	ForumComment::where('c_id', $arg['c_id'])->first();
		$partner_array = $this->forum_detail($postData['post_id']);
		$partner_array['result'] = $result;

		if($result == 1){
			
			// send notification to comment user
			if(!empty($postData['u_id'])){// 
				if($userId != $postData['u_id']){
					$sender = $userId;
					if($postData['parent_id'] == ''){  //Like on comment
		        		$message ="Liked your comment.";
		        		$n_type = 7;
		        	}else{ // like in Reply
		        		$message ="Liked your reply.";
		        		$n_type = 8;
					}
		        	$ref_id = $postData['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $postData['u_id'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		
		}

		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}

    // Get post detail Model
    public function forum_response($postid,$result=null){
    	$list = Forum::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forums.*')
					->where('forums.id', $postid)
					->leftjoin('users','forums.u_id','users.id')
					->first();
		//echo '<pre>';print_r($list); exit;
		    
        
        $like_count  = $this->forum_like_count($postid);
        $comment_count  = $this->forum_comment_count($postid);
        $is_my_like = $this->forum_my_like_count($postid,Auth::user()->id);      
        $user_plus_like_count  = $this->forum_user_plus_like_count($postid);
		$partner_array['result']            =   $result;
		
		

        $partner_array['post_data']['is_liked']  =  $is_my_like;
       
        $partner_array['post_data']['is_reposted']  =  false;
		$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =  $like_count;
        $partner_array['post_data']['user_plus_like_count']  =   $user_plus_like_count;
                        
        $partner_array['post_data']['comment_count']  =   $comment_count;

        
        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;


        	
			
        return $partner_array;
    } 

    public function forum_detail($data){
		$checkPost = Forum::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forums.*')
			->where('forums.id', $data)
			->leftjoin('users','forums.u_id','users.id')
			->first();
		//echo '<pre>';print_r($checkPost); exit;
		 if($checkPost['repost_id'] != ''){
			$data = $checkPost['repost_id'];
		 	$is_repost = true;
		 	$repost_id = 1;
		 }else{
		 	$data = $data;
		 	$is_repost = false;
		 	$repost_id = 0;
		 }		
		$list = Forum::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forums.*')
			->where('forums.id', $data)
			->leftjoin('users','forums.u_id','users.id')
			->first();
		$partner_array['id']   =   @$list['id'] ? $list['id'] : '';
		
		$postid =  $data;
            
        $like_count  = $this->forum_like_count($postid);
        $comment_count  = $this->forum_comment_count($postid);
        //$repost_count  = $this->repost_count($postid);  
        $is_my_like = $this->forum_my_like_count($postid,Auth::user()->id);      
        //$is_my_favourite = $this->is_my_favourite($postid,Auth::user()->id);      

		

        //$partner_array['post_data']['is_favorited']  =  $is_my_favourite;
        $partner_array['post_data']['is_liked']  =  $is_my_like;
        
        $partner_array['post_data']['is_reposted']  =  $is_repost;
        if($repost_id == 1){
        	$partner_array['userid']        =   @$checkPost['userid'] ? $checkPost['userid'] : '';
	        $partner_array['picUrl']  =   @$checkPost['picUrl'] ? $checkPost['picUrl'] : '';
	        $partner_array['user_name']  =   @$checkPost['username'] ? $checkPost['username'] : '';
	        $partner_array['first_name']  =   @$checkPost['first_name'] ? $checkPost['first_name'] : '';
	        $partner_array['last_name']  =   @$checkPost['last_name'] ? $checkPost['last_name'] : '';
	        $partner_array['is_verified']  =   @$checkPost['is_verified'] ? $checkPost['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$checkPost['user_type'] ? $checkPost['user_type'] : '';

        }else{
        	$partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        
       

        }
	    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['category']            =   @$list['category'] ? $list['category'] : '';
        $partner_array['post_data']['photo']  =   @$list['photo'] ? $list['photo'] : '';
        $partner_array['post_data']['title']  =   @$list['title'] ? $list['title'] :'';
        $partner_array['post_data']['detail']  =   @$list['detail'] ? $list['detail'] : '';
        $partner_array['post_data']['topic_id']  =   @$list['topic_id'] ? $list['topic_id'] : 0;
        $partner_array['post_data']['created_at']  =   @$list['created_at'] ? $list['created_at'] : '';
                    
        $partner_array['post_data']['like_count']  =   @$like_count;
        //$partner_array['post_data']['favourite_count']  =   @$favourite_count;
        $partner_array['post_data']['comment_count']  =   @$comment_count;
        $partner_array['post_data']['retweet_count']  =   @$repost_count;

        //$partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
        /*$photoData = DB::table('photos')
        ->where('post_id','=',$list['id'])
        ->get();
        $photo_array = array();
        $Photo_list = array();
        foreach ($photoData as $photoDatakey => $photoDatavalue) {
            $photo_array['photo_id']  =  @$photoDatavalue->p_id ? $photoDatavalue->p_id : '';
            $photo_array['imgUrl']  =  @$photoDatavalue->p_photo ? $photoDatavalue->p_photo : '';
            array_push($Photo_list,$photo_array);
           
        }
        $partner_array['post_data']['photos']  =   $Photo_list;
        */
        //$vote_count = $this->vote_count($postid);
        
        $comment = ForumComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forum_comments.*')
			->where('forum_comments.post_id', $data)
			->WhereNull('forum_comments.parent_id')
			->leftjoin('users','forum_comments.u_id','users.id')
			->get();
		if(!empty($comment)){
			$partner_array['post_data']['comments'] =array();
		
			foreach ($comment as $commentkey => $commentvalue) {
				//print_r($commentvalue['c_id']);
				$partner_array['post_data']['comments'][$commentkey]['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->forum_comment_like_count($commentvalue['c_id']);
      
				$partner_array['post_data']['comments'][$commentkey]['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->forum_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $partner_array['post_data']['comments'][$commentkey]['is_liked']  =  $myowncommenton;

		        $reply = ForumComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','forum_comments.*')
				->where('forum_comments.parent_id', $commentvalue['c_id'])
				->leftjoin('users','forum_comments.u_id','users.id')
				->get();
				if(!empty($reply)){
					$partner_array['post_data']['comments'][$commentkey]['sub_comments']  =array();
					foreach ($reply as $replykey => $replyvalue) {
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['id']= $replyvalue['c_id']?$replyvalue['c_id']:0;
						$reply_like_count  = $this->forum_comment_like_count($replyvalue['c_id']);
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['userid']=   @$replyvalue['userid'] ? $replyvalue['userid'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['picUrl']  =   @$replyvalue['picUrl'] ? $replyvalue['picUrl'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['user_name']  =   @$replyvalue['username'] ? $replyvalue['username'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['first_name']  =   @$replyvalue['first_name'] ? $replyvalue['first_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['last_name']  =   @$replyvalue['last_name'] ? $replyvalue['last_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['description']  =   @$replyvalue['description'] ? $replyvalue['description'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['posted_time']  =   @$replyvalue['created_at'] ? $replyvalue['created_at'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['like_count']  =   @$reply_like_count;
				      

				        $myownreplyon = $this->forum_my_comment_like_count($replyvalue['c_id'],Auth::user()->id);
				        
				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['is_liked']  =  $myownreplyon;

					}
				}
			}
		}
		//print_r($comment); exit;
		return $partner_array;
	}



	////////////////////Vote Them Out ////////////////////
	public function create_vote_them($data){
		//print_r($data); exit;
		$userId = Auth::user()->id;
		$send_notification = 0;
		if($data['name'] !=  ''){
			if(@$data['id']){
				$is_new = 0;
				$vote = VoteThemOut::where('id','=',@$data['id'])
					->first();
				$vote->id = @$data['id'] ? $data['id']: 0;	
			}else{
				$is_new = 1;
				$send_notification = 1;
				$vote = new VoteThemOut();
			}
			$vote->name = @$data['name'] ? $data['name']: '';
			$vote->photo = @$data['photo'] ? $data['photo']: '';
			$vote->party_affiliation = @$data['party_affiliation'] ? $data['party_affiliation']: '';
			$vote->u_id = $userId;
			$vote->state = @$data['state'] ? $data['state']: '';
			$vote->district = @$data['district'] ? $data['district']: '';
			$vote->senate_or_house = @$data['senate_or_house'] ? $data['senate_or_house']: '';
			$vote->day_of_vote = @$data['day_of_vote'] ? $data['day_of_vote']: '';
			$vote->vote_description = @$data['vote_description'] ? $data['vote_description']: '';

			
			//echo '<pre>'; print_r($room); exit;
			$vote->save();

			$lastid = $vote->id;

			
			$partner_array['code'] = 200;
			$partner_array['data'] = $lastid;

		
		}else{

			$partner_array['code'] = 633;

		}
		//echo '<pre>'; print_r($partner_array); exit;

		return $partner_array;
	}


	public function vote_them_list($data){
		$model 		= "App\Models\VoteThemOut";	
		$topic_id = @$data['topic_id'];
		$query = $model::query();
			

			if(isset($topic_id)){
				//echo $selected_date ; exit;
				//$query =$query->where('topic_id','=',@$topic_id);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','users.pollitical_orientation','vote_them_outs.*')
					->where('status',1)
					->leftjoin('users','vote_them_outs.u_id','users.id')
					->orderBy('vote_them_outs.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;
			//echo '<pre>';  print_r($partner); exit;
		
		return $partner;
	}


	public function vote_them_like($arg,$userId){
		$checklike = VoteThemOutLike::where('l_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$like = new VoteThemOutLike();
			$like->l_user_id = $userId;
			$like->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  VoteThemOutLike::where('l_id',$checklike['l_id'])->delete();	
			$result = 0;
		}		
		
		/*$like_count= Like::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->like_count 	= 	$like_count ? $like_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		
		$partner_array = $this->vote_them_response($arg['post_id'],$result);
		// send notification to post owner
		if($result == 1){
			
			if(!empty($partner_array['userid'])){
				if($userId != $partner_array['userid']){
					$sender = $userId;
		        	$message ="Liked your forum.";
		        	$n_type = 6;
		        	$ref_id = $arg['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $partner_array['userid'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		

		}

		return $partner_array;
	}


	public function vote_them_comment_post($data){
		//print_r($data); exit;
		if($data['description'] !=  ''){
			
			$post = VoteThemOut::where('id','=',$data['post_id'])
				->where('u_id','=',$data['userid'])
				->first();
			
			$comment = new VoteThemOutComment();
			$comment->u_id = @$data['userid'] ? $data['userid']: '';
			$comment->post_id = @$data['post_id'] ? $data['post_id']: '';
			$send_notification = 1;
			if(@$data['c_id']){
				$send_notification = 2;
				$comment->parent_id = @$data['c_id'] ? $data['c_id']: '';
				$maincomment = VoteThemOutComment::where('c_id', $data['c_id'])->first();
				$comment_user_id  = $maincomment['u_id'];
			}
			
			$comment->description = @$data['description'] ? $data['description']: '';
			$comment->created_at =  date ( 'Y-m-d H:i:s' );
			$comment->updated_at =  date ( 'Y-m-d H:i:s' );
			$comment->save();
			$lastid = $comment->c_id;
			$comment_count= VoteThemOutComment::where('post_id', $data['post_id'])->count();
			$postData 	=	VoteThemOut::where('id', $data['post_id'])->first();
			//$postData->comment_count 	= 	$comment_count ? $comment_count : 0;
			//echo '<pre>'; print_r($postData['u_id']); exit;
			$postData->save();
			$userData['code'] = 200;
			//$userData['c_id'] = @$lastid;

			$commentvalue = VoteThemOutComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_out_comments.*')
			->where('vote_them_out_comments.c_id', $lastid)
			->leftjoin('users','vote_them_out_comments.u_id','users.id')
			->first();
			if(!empty($comment)){
				$partner_array['post_data']['comments'] =array();
				
				$userData['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->vote_them_comment_like_count($commentvalue['c_id']);
      
				$userData['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $userData['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $userData['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $userData['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $userData['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $userData['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $userData['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $userData['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->vote_them_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $userData['is_liked']  =  $myowncommenton;

				
			}
			//send notification to post user
			if($send_notification == 1){ // send notification to post user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $postData['u_id']){
						$sender = $data['userid'];
			        	$message ="Commented on your post.";
			        	$n_type = 4;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $postData['u_id'];
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}else{//Reply notification to  comment user only
				if(!empty($postData['u_id'])){
					if($comment->u_id != $comment_user_id){
						$sender = $data['userid'];
			        	$message ="Reply on your forum comment.";
			        	$n_type = 5;
			        	$ref_id = $data['post_id'];//post_id
			        	$push_type = 1; //1 for normal 2 for seclient 
		        	    
	                	$userArr = $comment_user_id;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
						
					}
				}
			}

	
		}else{

			$userData['code'] = 633;

		}

		return $userData;
	}


	public function vote_them_comment_like($arg,$userId){
		$checklike = VoteThemOutCommentLike::where('l_user_id', $userId)->where('c_id', $arg['c_id'])->first();
		if(empty($checklike)){
			$like = new VoteThemOutCommentLike();
			$like->l_user_id = $userId;
			$like->c_id = $arg['c_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  VoteThemOutCommentLike::where('c_id',$checklike['c_id'])->delete();	
			$result = 0;
		}		
		
		
		$postData 	=	VoteThemOutCommentLike::where('c_id', @$arg['c_id'])->first();
		$partner_array = $this->vote_them_detail($postData['post_id']);
		$partner_array['result'] = $result;

		if($result == 1){
			
			// send notification to comment user
			if(!empty($postData['u_id'])){// 
				if($userId != $postData['u_id']){
					$sender = $userId;
					if($postData['parent_id'] == ''){  //Like on comment
		        		$message ="Liked your comment.";
		        		$n_type = 7;
		        	}else{ // like in Reply
		        		$message ="Liked your reply.";
		        		$n_type = 8;
					}
		        	$ref_id = $postData['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $postData['u_id'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		
		}

		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}

    // Get post detail Model
    public function vote_them_response($postid,$result=null){
    	$list = VoteThemOut::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_outs.*')
					->where('vote_them_outs.id', $postid)
					->leftjoin('users','vote_them_outs.u_id','users.id')
					->first();
		//echo '<pre>';print_r($list); exit;
		    
        
        $like_count  = $this->vote_them_like_count($postid);
        $comment_count  = $this->vote_them_comment_count($postid);
        $is_my_like = $this->vote_them_my_like_count($postid,Auth::user()->id);      
        $user_plus_like_count  = $this->vote_them_user_plus_like_count($postid);
		$partner_array['result']            =   $result;
		
		

        $partner_array['post_data']['is_liked']  =  $is_my_like;
       
        $partner_array['post_data']['is_reposted']  =  false;

        $partner_array['post_data']['id']   =   @$list['id'] ? $list['id'] : '';
		$partner_array['post_data']['name']   =   @$list['name'] ? $list['name'] : '';
		$partner_array['post_data']['photo']   =   @$list['photo'] ? $list['photo'] : '';
		$partner_array['post_data']['party_affiliation']   =   @$list['party_affiliation'] ? $list['party_affiliation'] : '';
		$partner_array['post_data']['state']   =   @$list['state'] ? $list['state'] : '';
		$partner_array['post_data']['district']   =   @$list['district'] ? $list['district'] : '';
		$partner_array['post_data']['senate_or_house']   =   @$list['senate_or_house'] ? $list['senate_or_house'] : '';
		$partner_array['post_data']['day_of_vote']   =   @$list['day_of_vote'] ? $list['day_of_vote'] : '';
		$partner_array['post_data']['vote_description']   =   @$list['vote_description'] ? $list['vote_description'] : '';

		$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =  $like_count;
        $partner_array['post_data']['user_plus_like_count']  =   $user_plus_like_count;
                        
        $partner_array['post_data']['comment_count']  =   $comment_count;

        
        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;


        	
			
        return $partner_array;
    } 

    public function vote_them_detail($data){
		$checkPost = VoteThemOut::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_outs.*')
			->where('vote_them_outs.id', $data)
			->leftjoin('users','vote_them_outs.u_id','users.id')
			->first();
		//echo '<pre>';print_r($checkPost); exit;
		/* if($checkPost['repost_id'] != ''){
			$data = $checkPost['repost_id'];
		 	$is_repost = true;
		 	$repost_id = 1;
		 }else{
		*/ 	$data = $data;
		 	$is_repost = false;
		 	$repost_id = 0;
		// }		
		$list = VoteThemOut::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_outs.*')
			->where('vote_them_outs.id', $data)
			->leftjoin('users','vote_them_outs.u_id','users.id')
			->first();
		$partner_array['post_data']['id']   =   @$list['id'] ? $list['id'] : '';
		$partner_array['post_data']['name']   =   @$list['name'] ? $list['name'] : '';
		$partner_array['post_data']['photo']   =   @$list['photo'] ? $list['photo'] : '';
		$partner_array['post_data']['party_affiliation']   =   @$list['party_affiliation'] ? $list['party_affiliation'] : '';
		$partner_array['post_data']['state']   =   @$list['state'] ? $list['state'] : '';
		$partner_array['post_data']['district']   =   @$list['district'] ? $list['district'] : '';
		$partner_array['post_data']['senate_or_house']   =   @$list['senate_or_house'] ? $list['senate_or_house'] : '';
		$partner_array['post_data']['day_of_vote']   =   @$list['day_of_vote'] ? $list['day_of_vote'] : '';
		$partner_array['post_data']['vote_description']   =   @$list['vote_description'] ? $list['vote_description'] : '';
		
		$postid =  $data;
            
        $like_count  = $this->vote_them_like_count($postid);
        $comment_count  = $this->vote_them_comment_count($postid);
        //$repost_count  = $this->repost_count($postid);  
        $is_my_like = $this->vote_them_my_like_count($postid,Auth::user()->id);      
        //$is_my_favourite = $this->is_my_favourite($postid,Auth::user()->id);      

		

        //$partner_array['post_data']['is_favorited']  =  $is_my_favourite;
        $partner_array['post_data']['is_liked']  =  $is_my_like;
        
        $partner_array['post_data']['is_reposted']  =  $is_repost;
        if($repost_id == 1){
        	$partner_array['userid']        =   @$checkPost['userid'] ? $checkPost['userid'] : '';
	        $partner_array['picUrl']  =   @$checkPost['picUrl'] ? $checkPost['picUrl'] : '';
	        $partner_array['user_name']  =   @$checkPost['username'] ? $checkPost['username'] : '';
	        $partner_array['first_name']  =   @$checkPost['first_name'] ? $checkPost['first_name'] : '';
	        $partner_array['last_name']  =   @$checkPost['last_name'] ? $checkPost['last_name'] : '';
	        $partner_array['is_verified']  =   @$checkPost['is_verified'] ? $checkPost['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$checkPost['user_type'] ? $checkPost['user_type'] : '';

        }else{
        	$partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        
       

        }
	    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['category']            =   @$list['category'] ? $list['category'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =   @$like_count;
        //$partner_array['post_data']['favourite_count']  =   @$favourite_count;
        $partner_array['post_data']['comment_count']  =   @$comment_count;
        $partner_array['post_data']['retweet_count']  =   @$repost_count;

        //$partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
        /*$photoData = DB::table('photos')
        ->where('post_id','=',$list['id'])
        ->get();
        $photo_array = array();
        $Photo_list = array();
        foreach ($photoData as $photoDatakey => $photoDatavalue) {
            $photo_array['photo_id']  =  @$photoDatavalue->p_id ? $photoDatavalue->p_id : '';
            $photo_array['imgUrl']  =  @$photoDatavalue->p_photo ? $photoDatavalue->p_photo : '';
            array_push($Photo_list,$photo_array);
           
        }
        $partner_array['post_data']['photos']  =   $Photo_list;
        */
        //$vote_count = $this->vote_count($postid);
        
        $comment = VoteThemOutComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_out_comments.*')
			->where('vote_them_out_comments.post_id', $data)
			->WhereNull('vote_them_out_comments.parent_id')
			->leftjoin('users','vote_them_out_comments.u_id','users.id')
			->get();
		if(!empty($comment)){
			$partner_array['post_data']['comments'] =array();
		
			foreach ($comment as $commentkey => $commentvalue) {
				//print_r($commentvalue['c_id']);
				$partner_array['post_data']['comments'][$commentkey]['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->vote_them_comment_like_count($commentvalue['c_id']);
      
				$partner_array['post_data']['comments'][$commentkey]['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->vote_them_my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $partner_array['post_data']['comments'][$commentkey]['is_liked']  =  $myowncommenton;

		        $reply = VoteThemOutComment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','vote_them_out_comments.*')
				->where('vote_them_out_comments.parent_id', $commentvalue['c_id'])
				->leftjoin('users','vote_them_out_comments.u_id','users.id')
				->get();
				if(!empty($reply)){
					$partner_array['post_data']['comments'][$commentkey]['sub_comments']  =array();
					foreach ($reply as $replykey => $replyvalue) {
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['id']= $replyvalue['c_id']?$replyvalue['c_id']:0;
						$reply_like_count  = $this->vote_them_comment_like_count($replyvalue['c_id']);
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['userid']=   @$replyvalue['userid'] ? $replyvalue['userid'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['picUrl']  =   @$replyvalue['picUrl'] ? $replyvalue['picUrl'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['user_name']  =   @$replyvalue['username'] ? $replyvalue['username'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['first_name']  =   @$replyvalue['first_name'] ? $replyvalue['first_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['last_name']  =   @$replyvalue['last_name'] ? $replyvalue['last_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['description']  =   @$replyvalue['description'] ? $replyvalue['description'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['posted_time']  =   @$replyvalue['created_at'] ? $replyvalue['created_at'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['like_count']  =   @$reply_like_count;
				      

				        $myownreplyon = $this->vote_them_my_comment_like_count($replyvalue['c_id'],Auth::user()->id);
				        
				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['is_liked']  =  $myownreplyon;

					}
				}
			}
		}
		//print_r($comment); exit;
		return $partner_array;
	}

	///////////////////////////////////////



	public function roomList($data){
		$model 		= "App\Models\Room";	
		$g_id = @$data['g_id'];
		$query = $model::query();
		$userId = Auth::user()->id;
			

			if(isset($g_id)){
				$query =$query->where('room_members.rm_g_id','=',@$g_id);
			}

			if(isset($userId)){
				$query =$query->where('room_members.rm_u_id','=',@$userId);
				$query =$query->where('room_members.rm_status',1);
			}

			$query = $query->select('rooms.*','room_members.*')
					->where('rooms.r_status',1)
					->leftjoin('room_members','rooms.r_id','room_members.rm_r_id')
					->orderBy('rooms.r_id', 'DESC')
					->groupBy('rooms.r_id')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('rooms.r_status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		
		
		return $partner;
	}

	public function groupChat($data){
		//print_r($data); exit;
		//if($data['text'] !=  ''){
			
			$roomDetail = Room::where('r_id','=',$data['r_id'])
				->first();
			$room_name = $roomDetail->r_title;
			
			$room_message = new RoomMessage();
			$room_message->sender_id = @$data['userid'] ? $data['userid']: '';
			$room_message->rm_g_id = @$data['g_id'] ? $data['g_id']: '';
			$room_message->rm_r_id = @$data['r_id'] ? $data['r_id']: '';
			$room_message->message_type = @$data['message_type'] ? $data['message_type']: '';
			$room_message->media_url = @$data['media_url'] ? $data['media_url']: '';
			
			if(@$data['rm_id']){
				$comment->parent_id = @$data['rm_id'] ? $data['rm_id']: '';
			}
			
			$room_message->text = @$data['text'] ? $data['text']: '';
			$room_message->reply_id = @$data['reply_id'];
			$room_message->added_date = date ( 'Y-m-d H:i:s' );
			$room_message->save();
			$lastid = $room_message->rm_id;
			
			$model 		= "App\Models\RoomMessage";	
			$r_id = @$data['r_id'];
			$query = $model::query();
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','room_msgs.*')
					->where('rm_id','=',@$lastid)
					//->where('users.user_status',1)
					->leftjoin('users','room_msgs.sender_id','users.id')
					->first();
			$partner = $query;
			$userData['msg']  = $partner;
			$userData['code'] = 200;
			$userData['c_id'] = @$lastid;
			if($data['message_type'] == 1){
				$message = @$data['text'];
			}elseif($data['message_type'] == 2){
				$message = '📷 IMAGE';

			}else{
				$message = '📷 GIF';

			}
			//echo Auth::user()->id;
			$send_notification = 1;
			if($send_notification  == 1){
				$sender = $data['userid'];
				$message = $message;
				$n_type = 29;
				$ref_id['notification_title'] = Auth::user()->first_name.' @ '.$room_name;
				$ref_id['g_id'] = $data['g_id'];//post_id
				$ref_id['room_id'] = $data['r_id'];//post_id
				$push_type = 2; //1 for normal 2 for seclient 
				$model1 		= "App\Models\RoomMember";	
				$r_id = @$data['r_id'];
				$query = $model1::query();
				$room_user = $query->select('room_members.*')
						->where('rm_r_id','=',$r_id)
						->where('rm_status',1)
						->where('rm_u_id','!=',$data['userid'])
						->get();  
		        //echo '<pre>'; print_r($room_user); exit;
		        foreach($room_user  as $room_userlist){
		        	$userArr = $room_userlist->rm_u_id;
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
				}
				
			}

		
	
		//}else{

			//$userData['code'] = 633;

		//}

		return $userData;
	}


	public function groupChatMessageList($data){
		$model 		= "App\Models\RoomMessage";	
		$r_id = @$data['r_id'];
		$query = $model::query();
			

			/*if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('rm_r_id','=',@$r_id);
			}
			*/
				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','room_msgs.*')
					->where('rm_r_id','=',@$r_id)
					->leftjoin('users','room_msgs.sender_id','users.id')
					->orderBy('room_msgs.rm_id', 'DESC')
					->paginate(200,['*'],'page_no');

			$query->total_count = $model::where('rm_r_id','=',@$r_id)
			//->where('users.user_status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}


	// Get post detail Model
    public function group_post_response($postid,$result=null){
    	$list = GroupPost::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
					->where('group_posts.id', $postid)
					->leftjoin('users','group_posts.u_id','users.id')
					->first();
		//echo '<pre>';print_r($list); exit;
		    
        
        $like_count  = $this->group_like_count($postid);
        $favourite_count  = $this->group_favourite_count($postid);
        $comment_count  = $this->group_comment_count($postid);
        $repost_count  = $this->group_repost_count($postid);  
        $is_my_like = $this->group_my_like_count($postid,Auth::user()->id);      
        $is_my_favourite = $this->group_is_my_favourite($postid,Auth::user()->id);      

		$partner_array['result']            =   $result;
		
		

        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
        $partner_array['post_data']['is_liked']  =  $is_my_like;
       
        $partner_array['post_data']['is_reposted']  =  false;
		$partner_array['id']            =   @$list['id'] ? $list['id'] : '';
        $partner_array['userid']      =   @$list['userid'] ? $list['userid'] : '';
        $partner_array['picUrl']      =   @$list['picUrl'] ? $list['picUrl'] : '';
        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =  $like_count;
        $partner_array['post_data']['favourite_count']  =   $favourite_count;
        $partner_array['post_data']['comment_count']  =   $comment_count;

        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
        $partner_array['post_data']['retweet_count']  =  $repost_count;

        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
        $partner_array['post_data']['stock_name']  =   @$list['stock_name'] ? $list['stock_name'] : '';
        $partner_array['post_data']['stock_target_price']  =   @$list['stock_target_price'] ? $list['stock_target_price'] : '';
        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;

        $partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
        $vote_count = $this->group_vote_count($postid);
        //echo '<pre>'; print_r($vote_count); exit;
        $partner_array['post_data']['total_votes']  =  $vote_count['total_vote_count'];
		//$partner_array['post_data']['is_voted'] =  $vote_count['is_voted'];

        	
			
        //print_r($vote_count); exit;	
        if(!empty($list['poll_one'])){
            $partner_array['post_data']['options'][0]['id']  =   1;
            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
            $partner_array['post_data']['options'][0]['percentage']  =   $vote_count['one_per'];
            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['one'];
            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['is_voted_one'];
        }
        if(!empty($list['poll_two'])){
            $partner_array['post_data']['options'][1]['id']  =   2;
            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count['two_per'];
            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['two'];
            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['is_voted_two'];
        }
        if(!empty($list['poll_three'])){
            $partner_array['post_data']['options'][2]['id']  =   3;
            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count['three_per'];
            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['three'];
            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['is_voted_three'];
        }
        if(!empty($list['poll_four'])){
            $partner_array['post_data']['options'][3]['id']  =   4;
            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count['four_per'];
            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['four'];
            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['is_voted_four'];

            
        }
        return $partner_array;
    } 

	public function view_gallery($data){

		$getphotolist =  Photo::where('p_u_id',$data['p_u_id'])->get();	
		
		$PhotoData = array();
		$PhotoArr = array();
		foreach($getphotolist as $list){

			$PhotoData['p_id'] 		=  @$list->p_id ? $list->p_id : '';
			$PhotoData['p_u_id'] 	=  @$list->p_u_id ? $list->p_u_id : '';
			$PhotoData['p_photo'] 	=  @$list->p_photo? URL('/public/images/'.$list->p_photo): '';
			$PhotoData['is_default'] 	=  @$list->is_default ? $list->is_default : '';
			array_push($PhotoArr,$PhotoData);
			
		}



		return $PhotoArr;
	}

	public function delete_gallery($data){

		$getphotolist =  Photo::where('p_id',$data['p_id'])->delete();	
		return 1;
	}

	

	public function get_user_list($data){

		$getpatient = User::where('current_physican_id','=',$data['Id'])
						->where('user_type','=',2)->where('user_status','=',1)->get();

		$patient = array();
		$Patient_list = array();

		foreach($getpatient as $list){


			$patient['id'] 				=  	@$list->id ? $list->id : '';
			$patient['name'] 			=  	@$list->name ? $list->name : '';
			/*$patient['email'] 			=  	@$list->email ? $list->email : '';
			$patient['country_code'] 	= 	@$list->country_code ? $list->country_code : '';
			$patient['phone'] 			= 	@$list->phone ? $list->phone : '';
			$patient['photo'] 			=  	@$list->photo ? $list->photo : '';
			$patient['address'] 		=  	@$list->address ? $list->address : '';
			$patient['zip'] 			=  	@$list->zip ? $list->zip : '';
			$patient['gender'] 			=  	@$list->gender ? $list->gender : '';
			$patient['phone'] 			=  	@$list->phone ? $list->phone : '';*/
			
			array_push($Patient_list,$patient);
			
		}

		return $Patient_list;
	}

	public function update_forgot_code($userId,$code){
		
		$user = User::find($userId);
		$user->reset_key = $code;
		$user->save();
		return $user;
	}

	public function update_activation($userId){
		
		$user = User::find($userId);
		$user->activation_code = "";
		$user->user_status = 1;
		$user->is_email_verified = 1;

		$user->save();
		$sender_name = $user['first_name'];
		$message =  $sender_name." your email account has been activated.";
		$data['userid'] = $userId;
		$data['name'] = $user['first_name'];
		$data['message'] = $message;
		$data['n_type'] = 1;
		$notify = array ();
		$notify['receiver_id'] = $userId;
		$notify['relData'] = $data;
		$notify['message'] = $message;
		//print_r($notify); exit;
		$test =  $this->sendPushNotification($notify); 
		return $user;
	}

	public function update_password($data){
		//print_r($data); exit;	
		//$user = User::where('reset_key', $data['code'])->where('email', $data['email'])->first();
		$user = User::where('id', $data['id'])->first();
		if($user){
			$forgot_password = 0;
			if($user->password != ''){
				$forgot_password = 1;
			}
			//if($user->reset_key == $data['code']){

				$user->password = hash::make($data['password']);
				$user->user_status = 1;
				$user->activation_code  = '';
				$user->is_phone_verified = 1;

				$user->save();

				$user->is_forgot = $forgot_password; 
			//}
		}
		
		return $user;
	}

	public function category_list($data){

		$category = Categories::where('c_status',1)->paginate(100,['*'],'page_no');
	
		$category_array = array();
		$category_list = array();

		foreach($category as $list){
			$category_array['c_id'] 			=  	@$list->c_id ? $list->c_id : '';
			$category_array['c_name'] 	=  	@$list->c_name ? $list->c_name : '';
			$category_array['c_status'] 	=  	@$list->c_status ? $list->c_status : '';
			$category_array['c_image'] 	=  	@$list->c_image ? URL('/public/images/'.$list->c_image) : '';
			
			array_push($category_list,$category_array);
		}

		//echo '<pre>'; print_r($chip); exit;
		
		return $category;
	}
	

	

	public function report_list($data){

		$report = ReportList::paginate(100,['*'],'page_no');

		$report_array = array();
		$report_list = array();

		foreach($report as $list){
			$report_array['id'] 			=  	@$list->id ? $list->id : '';
			$report_array['gender'] 	=  	@$list->gender ? $list->report : '';
			
			array_push($report_list,$report_array);
		}
		
		//echo '<pre>'; print_r($chip); exit;
		
		return $report;
	}

	

	


	public function subcategory_list($data){
		$subcategory = SubCategories::where('sc_c_id',$data)->paginate(100,['*'],'page_no');
		$subcategory_array = array();
		$subcategory_list = array();

		foreach($subcategory as $list){
			$subcategory_array['sc_id'] 	=  	@$list->sc_id ? $list->sc_id : '';
			$subcategory_array['sc_name'] 	=  	@$list->sc_name ? $list->sc_name : '';
			
			array_push($subcategory_list,$subcategory_array);
		}
		//echo '<pre>'; print_r($chip); exit;
		
		return $subcategory;
	}

	

	


	public function like($arg,$userId){
		$checklike = Like::where('l_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$like = new Like();
			$like->l_user_id = $userId;
			$like->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  Like::where('l_id',$checklike['l_id'])->delete();	
			$result = 0;
		}		
		
		/*$like_count= Like::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->like_count 	= 	$like_count ? $like_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		
		$partner_array = $this->post_response($arg['post_id'],$result);
		// send notification to post owner
		if($result == 1){
			
			if(!empty($partner_array['userid'])){
				if($userId != $partner_array['userid']){
					$sender = $userId;
		        	$message ="Liked your post.";
		        	$n_type = 6;
		        	$ref_id = $arg['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $partner_array['userid'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		

		}

		return $partner_array;
	}

	public function follow($arg,$userId){
		$checkfollow = Follow::where('follow_by', $userId)->where('user_id', $arg['user_id'])->first();
		if(empty($checkfollow)){
			$follow = new Follow();
			$follow->follow_by = $userId;
			$follow->user_id = $arg['user_id'];
			//echo '<pre>'; print_r($like); exit;
			$follow->save();
			$result= 1;

			// send notification to comment user
			if(!empty($arg['user_id'])){// 
				if($userId != $arg['user_id']){
					$sender = $userId;
					$message ="has started following you.";
		        	$n_type = 11;
		        	$ref_id = $userId;//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $arg['user_id'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}

		}else{
			$deletefollow =  Follow::where('id',$checkfollow['id'])->delete();	
			$result = 0;
		}		
		$getuser =array();
		$id = $arg['user_id'];

		$getuser  =   $this->getuserById($id);
		$getuser['result'] = $result;
		//$partner_array = $this->post_response($arg['post_id'],$result);
		return $getuser;
	}


	public function comment_like($arg,$userId){
		$checklike = CommentLike::where('l_user_id', $userId)->where('c_id', $arg['c_id'])->first();
		if(empty($checklike)){
			$like = new CommentLike();
			$like->l_user_id = $userId;
			$like->c_id = $arg['c_id'];
			//echo '<pre>'; print_r($like); exit;
			$like->save();
			$result= 1;
		}else{
			$deletelike =  CommentLike::where('c_id',$checklike['c_id'])->delete();	
			$result = 0;
		}		
		
		
		$postData 	=	Comment::where('c_id', $arg['c_id'])->first();
		$partner_array = $this->post_detail($postData['post_id']);
		$partner_array['result'] = $result;

		if($result == 1){
			
			// send notification to comment user
			if(!empty($postData['u_id'])){// 
				if($userId != $postData['u_id']){
					$sender = $userId;
					if($postData['parent_id'] == ''){  //Like on comment
		        		$message ="Liked your comment.";
		        		$n_type = 7;
		        	}else{ // like in Reply
		        		$message ="Liked your reply.";
		        		$n_type = 8;
					}
		        	$ref_id = $postData['post_id'];//post_id
		        	$push_type = 1; //1 for normal 2 for seclient 
	        	    
                	$userArr = $postData['u_id'];
					$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
					
				}
			}
		
		}

		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}

	public function favourite($arg,$userId){
		$checklike = favourite::where('f_user_id', $userId)->where('post_id', $arg['post_id'])->first();
		if(empty($checklike)){
			$favourite = new favourite();
			$favourite->f_user_id = $userId;
			$favourite->post_id = $arg['post_id'];
			//echo '<pre>'; print_r($like); exit;
			$favourite->save();
			$result = 1;
		}else{
			$deletelike =  favourite::where('f_id',$checklike['f_id'])->delete();	
			$result = 0;
		}		
		
		/*$favourite_count= favourite::where('post_id', $arg['post_id'])->count();
		
		$postData 	=	Post::where('id', $arg['post_id'])->first();
		$postData->favourite_count 	= 	$favourite_count ? $favourite_count : 0;
		//print_r($postData); exit;
		$postData->save();*/
		$partner_array = $this->post_response($arg['post_id'],$result);
		

		return $partner_array;
	}

	public function vote($arg,$userId){
		$checklike = Vote::where('v_user_id', $userId)->where('v_post_id', $arg['v_post_id'])->first();
		if(empty($checklike)){
			$vote = new Vote();
			$vote->v_user_id = $userId;
			$vote->v_post_id = $arg['v_post_id'];
			$vote->v_option = $arg['v_option'];
			//echo '<pre>'; print_r($like); exit;
			$vote->save();
			$result= 1;
		}else{
			if($arg['v_option'] == $checklike['v_option']){
				$deletelike =  Vote::where('v_user_id', $userId)->where('v_post_id', $arg['v_post_id'])->delete();	
				$result = 0;
			}else{
				Vote::where('v_id', $checklike['v_id'])
	       		->update([
	           	'v_option' => $arg['v_option']
        		]);	
        		$result = 1;
			}
			
		}		
		
		$partner_array = $this->post_response($arg['v_post_id'],$result);
		

		//echo '<pre>'; print_r($partner_array); exit;
		return $partner_array;
	}


	public function post_list($data){
		$model 		= "App\Models\Post";	
		$post_type = @$data['post_type'];
		$query = $model::query();
			

			if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','users.pollitical_orientation','posts.*')
					->where('status',1)
					->leftjoin('users','posts.u_id','users.id')
					->orderBy('posts.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}

	public function activity_list($data){
		$model 		= "App\Models\Post";	
		$post_type = @$data['post_type'];
		$userid = @$data['userid'];
		$query = $model::query();
			

			if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
					->where('status',1)
					->where('post_type','!=',2)
					->where('u_id',$userid)
					->leftjoin('users','posts.u_id','users.id')
					->orderBy('posts.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}


	

	public function post_detail($data){
		$checkPost = Post::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
			->where('posts.id', $data)
			->leftjoin('users','posts.u_id','users.id')
			->first();
		//echo '<pre>';print_r($checkPost); exit;
		 if($checkPost['repost_id'] != ''){
			$data = $checkPost['repost_id'];
		 	$is_repost = true;
		 	$repost_id = 1;
		 }else{
		 	$data = $data;
		 	$is_repost = false;
		 	$repost_id = 0;
		 }		
		$list = Post::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
			->where('posts.id', $data)
			->leftjoin('users','posts.u_id','users.id')
			->first();
		$partner_array['id']   =   @$list['id'] ? $list['id'] : '';
		
		$postid =  $data;
            
        $like_count  = $this->like_count($postid);
        $favourite_count  = $this->favourite_count($postid);
        $comment_count  = $this->comment_count($postid);
        $repost_count  = $this->repost_count($postid);  
        $is_my_like = $this->my_like_count($postid,Auth::user()->id);      
        $is_my_favourite = $this->is_my_favourite($postid,Auth::user()->id);      

		

        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
        $partner_array['post_data']['is_liked']  =  $is_my_like;
        
        $partner_array['post_data']['is_reposted']  =  $is_repost;
        if($repost_id == 1){
        	$partner_array['userid']        =   @$checkPost['userid'] ? $checkPost['userid'] : '';
	        $partner_array['picUrl']  =   @$checkPost['picUrl'] ? $checkPost['picUrl'] : '';
	        $partner_array['user_name']  =   @$checkPost['username'] ? $checkPost['username'] : '';
	        $partner_array['first_name']  =   @$checkPost['first_name'] ? $checkPost['first_name'] : '';
	        $partner_array['last_name']  =   @$checkPost['last_name'] ? $checkPost['last_name'] : '';
	        $partner_array['is_verified']  =   @$checkPost['is_verified'] ? $checkPost['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$checkPost['user_type'] ? $checkPost['user_type'] : '';

        }else{
        	$partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
	        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
	        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
	        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
	        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
	        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
	       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
	        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
        
       

        }
	    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
        $partner_array['category']            =   @$list['category'] ? $list['category'] : '';
        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
        $partner_array['post_data']['like_count']  =   @$like_count;
        $partner_array['post_data']['favourite_count']  =   @$favourite_count;
        $partner_array['post_data']['comment_count']  =   @$comment_count;
        $partner_array['post_data']['retweet_count']  =   @$repost_count;

        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;
        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;
        $partner_array['post_data']['time_left']  =   @$list['time_left'] ? $list['time_left'] : '';
        $partner_array['post_data']['term']  =   @$list['term'] ? $list['term'] : '';
        $partner_array['post_data']['result']  =   @$list['result'] ? $list['result'] : '';
        $partner_array['post_data']['trend']   =  @$list['trend'] ? $list['trend'] : 0;
        $partner_array['post_data']['recommendation']   =  @$list['recommendation'] ? $list['recommendation'] : 0;
        $photoData = DB::table('photos')
        ->where('post_id','=',$list['id'])
        ->get();
        $photo_array = array();
        $Photo_list = array();
        foreach ($photoData as $photoDatakey => $photoDatavalue) {
            $photo_array['photo_id']  =  @$photoDatavalue->p_id ? $photoDatavalue->p_id : '';
            $photo_array['imgUrl']  =  @$photoDatavalue->p_photo ? $photoDatavalue->p_photo : '';
            array_push($Photo_list,$photo_array);
           
        }
        $partner_array['post_data']['photos']  =   $Photo_list;
        //$partner_array['post_data']['total_votes']  =   @$list['total_votes'] ? $list['total_votes'] : 0;
        $vote_count = $this->vote_count($postid);
        $partner_array['post_data']['total_votes']  =  $vote_count['total_vote_count'];
        if(!empty($list['poll_one'])){
            $partner_array['post_data']['options'][0]['id']  =   1;
            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
            $partner_array['post_data']['options'][0]['percentage']  =   $vote_count['one_per'];
            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['one'];
            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count['is_voted_one'];
        }
        if(!empty($list['poll_two'])){
            $partner_array['post_data']['options'][1]['id']  =   2;
            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count['two_per'];
            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['two'];
            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count['is_voted_two'];
        }
        if(!empty($list['poll_three'])){
            $partner_array['post_data']['options'][2]['id']  =   3;
            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count['three_per'];
            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['three'];
            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count['is_voted_three'];

        }
        if(!empty($list['poll_four'])){
            $partner_array['post_data']['options'][3]['id']  =   4;
            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count['four_per'];
            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['four'];
            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count['is_voted_four'];
            
        }
        $comment = Comment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','comments.*')
			->where('comments.post_id', $data)
			->WhereNull('comments.parent_id')
			->leftjoin('users','comments.u_id','users.id')
			->get();
		if(!empty($comment)){
			$partner_array['post_data']['comments'] =array();
		
			foreach ($comment as $commentkey => $commentvalue) {
				//print_r($commentvalue['c_id']);
				$partner_array['post_data']['comments'][$commentkey]['id']= $commentvalue['c_id']?$commentvalue['c_id']:0;

				$comment_like_count  = $this->comment_like_count($commentvalue['c_id']);
      
				$partner_array['post_data']['comments'][$commentkey]['userid']=   @$commentvalue['userid'] ? $commentvalue['userid'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['picUrl']  =   @$commentvalue['picUrl'] ? $commentvalue['picUrl'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['user_name']  =   @$commentvalue['username'] ? $commentvalue['username'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['first_name']  =   @$commentvalue['first_name'] ? $commentvalue['first_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['last_name']  =   @$commentvalue['last_name'] ? $commentvalue['last_name'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['description']  =   @$commentvalue['description'] ? $commentvalue['description'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['posted_time']  =   @$commentvalue['created_at'] ? $commentvalue['created_at'] : '';
		        $partner_array['post_data']['comments'][$commentkey]['like_count']  =   $comment_like_count;
		       
		        $myowncommenton = $this->my_comment_like_count($commentvalue['c_id'],Auth::user()->id);
		        $partner_array['post_data']['comments'][$commentkey]['is_liked']  =  $myowncommenton;

		        $reply = Comment::select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','comments.*')
				->where('comments.parent_id', $commentvalue['c_id'])
				->leftjoin('users','comments.u_id','users.id')
				->get();
				if(!empty($reply)){
					$partner_array['post_data']['comments'][$commentkey]['sub_comments']  =array();
					foreach ($reply as $replykey => $replyvalue) {
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['id']= $replyvalue['c_id']?$replyvalue['c_id']:0;
						$reply_like_count  = $this->comment_like_count($replyvalue['c_id']);
						$partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['userid']=   @$replyvalue['userid'] ? $replyvalue['userid'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['picUrl']  =   @$replyvalue['picUrl'] ? $replyvalue['picUrl'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['user_name']  =   @$replyvalue['username'] ? $replyvalue['username'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['first_name']  =   @$replyvalue['first_name'] ? $replyvalue['first_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['last_name']  =   @$replyvalue['last_name'] ? $replyvalue['last_name'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['description']  =   @$replyvalue['description'] ? $replyvalue['description'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['posted_time']  =   @$replyvalue['created_at'] ? $replyvalue['created_at'] : '';

				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['like_count']  =   @$reply_like_count;
				      

				        $myownreplyon = $this->my_comment_like_count($replyvalue['c_id'],Auth::user()->id);
				        
				        $partner_array['post_data']['comments'][$commentkey]['sub_comments'][$replykey]['is_liked']  =  $myownreplyon;

					}
				}
			}
		}
		//print_r($comment); exit;
		return $partner_array;
	}


	public function photo_list($data){
    	//echo '<pre>'; print_r($data); exit;
		$model 		= "App\Models\Photo";	
		$query = $model::query();
		
		if(!empty(@$data['userid'])){
				//echo $selected_date ; exit;
				$query = $query->where('p_u_id','=',@$data['userid']);
		}

		$query = $query->where('p_status',1)
        ->where('p_type','=',1)
		//->orderBy('follows.id', 'DESC')
		->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('p_status',1)
			->count();
		
		$photo = $query;

			
		return $photo;
	}


    public function reel_list($data){
    	//echo '<pre>'; print_r($data); exit;
		$model 		= "App\Models\Photo";	
		$query = $model::query();
		
		if(!empty(@$data['userid'])){	
				//echo $selected_date ; exit;
				$query = $query->where('p_u_id','=',@$data['userid']);
		}

		$query = $query->where('p_status',1)
        ->where('p_type','=',2)
		//->orderBy('follows.id', 'DESC')
		->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('p_status',1)
			->count();
			
		
		$photo = $query;

    	//echo '<pre>'; print_r($photo); exit;
			
		return $photo;
	}


	public function reel_detail($data){

		$id = $data['reel_id'];

		$reelsData = Photo::leftJoin('users', function($join) {
		      $join->on('p_u_id', '=', 'id');
		    })
		    ->where('p_id',$id)
		    ->where('p_type','=',2)
		    ->where('p_status',1)
		    ->first();

		return $reelsData;

	}

	public function user_status($data){


		$id = $data['user_id'];

		$postData = Post::leftjoin('users','posts.p_u_id' ,'users.id')
					->leftjoin('photos', 'photos.post_id', '=', 'posts.p_id')
					->where('posts.p_u_id',$id)
		    		->where('post_type','=',3)
		    		->where('status',1)
		    		->groupBy('posts.p_id')
		    		->get();

		//		dd($postData);    		
		return $postData;

	}

	public function status_view($data){


		$senderId 	= $data['sender_id'];
		$receiverId = $data['receiver_id'];
		$postId 	= $data['post_id'];

		$statusView = Post::leftjoin('users','posts.p_u_id' ,'users.id')
					->where('posts.p_u_id',$senderId)
					->where('posts.p_u_id',$receiverId)
					->where('posts.p_id',$postId)
		  		    ->where('status',1)
		    		->groupBy('posts.p_id')
		    		->get();
	
		return $statusView;

	}

	public function status_list($data){
    	//echo '<pre>'; print_(r$data); exit;
		$model 		= "App\Models\Photo";	
		$query = $model::query();
		
		if(!empty(@$data['userid'])){
				//echo $selected_date ; exit;
				$query = $query->where('p_u_id','=',@$data['userid']);
		}

		$query = $query->where('p_status',1)
        ->where('p_type','=',3)
		//->orderBy('follows.id', 'DESC')
		->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('p_status',1)
			->count();
			
		
		$photo = $query;

			
		return $photo;
	}

	public function watch_list($data){
		$model 		= "App\Models\Favourite";	
		$post_type = @$data['post_type'];
		$query = $model::query();
			

			
		if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}

		
		/*$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','favourities.*')
			->where('users.user_status',1)
			->leftjoin('users','favourities.f_user_id','users.id')
			->orderBy('favourities.f_id', 'DESC')
			->paginate(10,['*'],'page_no');*/
		$userId= Auth::user()->id;
		$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*', 'favourities.f_id as fav_id','favourities.post_id as fav_post_id','favourities.f_user_id as fav_user_id')
				->where('favourities.f_user_id',$userId)
				->leftjoin('posts','posts.id','favourities.post_id')
				->leftjoin('users','users.id','posts.u_id')
				->orderBy('posts.id', 'DESC')
				->paginate(10,['*'],'page_no');


					

		$query->total_count = $model::where('f_user_id',$userId)
				->count();
		$partner = $query;
				
		
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}


	public function notificationList($data){
		$model 		= "App\Models\Notification";	
		$post_type = @$data['post_type'];
		$query = $model::query();
		if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','notifications.*')
				->where('notifications.n_status','!=',2)
				->leftjoin('users','notifications.n_sender_id','users.id')
				->orderBy('notifications.n_id', 'DESC')
				->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('notifications.n_status','!=',2)
				->count();
		$notification = $query;
		return $notification;
	}

	public function followUser($data){
		$model 		= "App\Models\Follow";	
		$post_type = @$data['post_type'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','follows.*')
				->where('follows.follow_by',$userId)
				->leftjoin('users','follows.user_id','users.id')
				->orderBy('users.first_name', 'ASC')
				->paginate(100,['*'],'page_no');

		$query->total_count = $model::where('follows.follow_by',$userId)
				->count();
		$users = $query;
		return $users;
	}


	

	public function check_username($data,$userId){
		$checkEmail = User::where('username', $data['username'])->first();
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail); exit;
		$userData =array();
		$userData['is_username_available'] = 0;	
		if(!isset($checkEmail['id'])){
			$userData['is_username_available'] = 0;
		}else{
			
	   		$userData['is_username_available'] = 1;
	   	}

		return $userData;
	}


	public function update_device($data,$userId){
		$checkEmail = User::where('id', $userId)
	       		->update([
	           'device_token' => @$data['device_token'] ,'device_type' => @$data['device_type'],
	           'device_id' => @$data['device_id']
        ]);	
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
		$userData =array();
		$userData['code'] = 200;
		$userData['device_token'] = $data['device_token'];
		
		return $userData;
	}

	public function chat_user_sid_update($sid,$userId){
		$checkEmail = User::where('id', $userId)
	       		->update([
	           'sid' => @$sid 
        ]);	
		////////////
		//print_r($userId); exit;
		//print_r($checkEmail['id']); exit;
		$userData =array();
		$userData['code'] = 200;
		$userData['sid'] = $sid;
		
		return $userData;
	}


	public function sendPushNotification($notify) {
		$data                       = $notify['relData'];
		$receiver_id                = trim($notify['receiver_id']); 
		$message                    = trim($notify['message']);
	    // $badge                      = trim(@$_POST['badge']);
		if (strlen($message) > 189) {
			$message = substr($message, 0, 185);
			$message = $message . '...';
		}else{
			$message = $message;
		}
		//echo $receiver_id; exit;
		$check_user 	=	User::find($receiver_id);
		$badge = 1;
		/*$notificationTable = TableRegistry::get('Notifications');
		$badge = $notificationTable
					->find()
					->where(['n_u_id'=> $receiver_id])
					->where(['n_type != 5'])
					->where(['n_status' => 0])
					->count();
		//print_r($badge);
		if($badge == 0){
		}else{
			$badge = $badge+1;
		}*/
		//prd($data);
		//echo '<pre>'; print_r($check_user); exit;

		if (empty($receiver_id)) {
			exit;
		}
		if (@$check_user['device_type'] == 0) { //ios
			$check_user['device_id'] = trim(@$check_user['device_id']);
			if($check_user['device_token'] != ''){
				if(!empty($message)){
					//echo $check_user['device_token'];  exit;
					//$this->iphone_push($check_user['device_token'], $message,  $data, $badge);
					//echo 'yesy';
					//print_r($data); exit;
					//$this->sendApns_P8($check_user['device_token'], $message,  $data, 0);
					$this->ios_fcm_push($check_user['device_token'], $message,  $data, $badge);
				}
			}
			//$this->android_push($check_user['device_id'], $message,  $data, $badge=0);
		}else{ //android
			//dd($check_user);
			if(@$check_user['device_token'] != ''){
				if(!empty($message)){
					//echo '<br>'.$check_user['device_id'].'<br>';
					$this->android_fcm_push($check_user['device_token'], $message,  $data, $badge);
				}
			}
		}
	   
		//return;
	}

	//  FCM
	public function android_push($id, $message, $relData, $badge){
		header('Content-type: text/html; charset=utf-8');
		// API access key from Google API's Console
		//CGT Key
		//prd($id);
		//Client Account
		$API_ACCESS_KEY  = 'er';
	   	
	   //	$id = 'courlEezNQ0:APA91bEPfxQbaJUUD_WakvYMZLyxDpKu6ydF1vXIu6j3QwGcPQFVWTS2H3oAayHRXsIGt39D_XcJ5qVtSJSKfjZpnZJ9zGLtvE9pk5xq_n4s2dIv_yv0XcnMVDvI6XlWq8p-1WXJRcy7';
		$registrationIds = array($id);
		//echo 'come'; exit;
		$msg['data']= array(
		'message' => $message,
		'badge' => (int)$badge,
		'relData' => $relData,
		//'vibrate' => 1,
		
		//'data'=>$data
		);
	   
		$fields = array(
					   'registration_ids' => $registrationIds,
					   'data' => $msg,
					   'title' => 'Eureka',
					   'priority'=>'high',
					   'sound' => 'default',
					   //'relData' => $relData
						);
		//prd($fields);
		$headers         = array(
		'Authorization: key=' . $API_ACCESS_KEY,
		'Content-Type: application/json'
		);
		$ch        = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		//curl_close($ch);
		$res = json_decode($result,true);
		//print_r($res); exit;
		if($res['success']){
		echo 'complete'; exit;
		curl_close($ch);
		return 1;
		}else{
		  echo 'not'; exit;
		curl_close($ch);
		return 0;
		}
	}

	// iphone FCM 
	public function android_fcm_push($id, $message, $relData, $badge){
		$url = "https://fcm.googleapis.com/fcm/send";
		$token =  $id; 
		//Client key
		//prd($relData['notification_title']);
		$serverKey = '1';
		$title = $relData['notification_title'];
 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}
		
		$body = $message;
		$msg['data']= array(
		'message' => $message,
		'relData' => $relData,
		'badge' => (int)$badge,
		);
		$notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => $badge);
		//$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','data'=>$msg);
		$arrayToSend = array('to' => $token, 'priority'=>'high','data'=>$msg );
		$json = json_encode($arrayToSend);
		//	print_r($json);exit;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: key='. $serverKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

		"POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		//Send the request
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		//print_r($response); exit;
		//Close request
		if ($response === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);	
	}
	
	// iphone FCM 
	public function ios_fcm_push($id, $message, $relData, $badge){
		
		$url = "https://fcm.googleapis.com/fcm/send";
		$token =  $id; 
		//Client key
		//echo '<pre>'; print_r($relData['notification_title']); exit;
		$serverKey = '1';
		$title = $relData['notification_title'];

 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}
		
		$body = $message;
		$msg['data']= array(
		'message' => $message,
		'relData' => $relData,
		'badge' => (int)$badge,
		);

			
		
		$notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => $badge);
		$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','data'=>$msg['data']['relData']);
		/*$arrayToSend = array('aps'=>array(
		 	'relData' => $relData,
		 	'alert' => $message, 
		 	'badge' => intval(0), 'sound' => 'default' 
		 ),'to' => $token, 'priority'=>'high');*/
		$json = json_encode($arrayToSend);
		//print_r($json);exit;
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: key='. $serverKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,

		"POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		//Send the request
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		//print_r($response); //exit;
		//Close request
		if ($response === FALSE) {
		die('FCM Send Error: ' . curl_error($ch));
		}
		curl_close($ch);	
	}


	// Iphone APNS
	public function iphone_push($id, $message, $relData, $badge) {
		//header('Content-type: text/html; charset=utf-8');
		 echo $deviceToken = $id.'<br>';
		// Put your private key's passphrase here:
		$deviceToken  = $id;
		$deviceToken  = trim($deviceToken);  
		$deviceToken  = '5673719219f37a51aaa253126b892095c9d778feed081629939cd163a7cb5e33';  
		$passphrase  = '';
		// //////////////////////////////////////////////////////////////////////////////
		//$ctx         = stream_context_create();
		/*$ctx = $streamContext = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);*/
        $ctx = stream_context_create();
        //echo app_path(); exit;
		echo $pem_path = app_path().'/nn.pem';
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_path );
		//stream_context_set_option($ctx, 'ssl', 'local_cert', './Meprosh_Development.pem');
		//echo stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].$this->webroot.'ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		// Open a connection to the APNS server
		//$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		//print_r($fp);
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
			echo 'Connected to APNS' . PHP_EOL;
			// Create the payload body
			//$resp = $this->cpSTR_to_utf8STR($message);
			//$this->writeResponseLog($resp);
			//$m = (string) $this->cpSTR_to_utf8STR($message);
		//echo strlen($message);
		$title = "Hopple";
 		
 		if(isset($relData['notification_title'])){
			$title = $relData['notification_title'];
		}

		$body['aps'] = array(
		'alert' => html_entity_decode($message, ENT_NOQUOTES, 'UTF-8'),
		'title' => $title,
		'sound' => 'default',
		'badge' => (int)$badge,
		'relData' => $relData,
		
	
		);
		//print_r($body); 
		//$this->writeResponseLog($body);
		//echo $count;
		// Encode the payload as JSON
		$payload = json_encode($body);
		//echo strlen($payload); exit;
		// Build the binary notification
		$msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$msg     = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result  = fwrite($fp, $msg, strlen($msg));
	    //print_r($result); 
	    //echo '<br>';
	    if (! $result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;
			
		//Close the connection to the server
		@socket_close($fp);
		fclose($fp);
			return;
	}

	public function sendApns_P8($deviceIds,$message,$optionalData,$badge){
        //print_r([$deviceIds,$message,$optionalData]); exit;
        //$pem_path = app_path().'/AuthKey_RR5BW56AWA.p8';
        $keyfile = app_path().'/AuthKey_RR5BW56AWAA.p8';  # <- Your AuthKey file
        $keyid = 'd';                            # <- Your Key ID
        $teamid = 'd';                           # <- Your Team ID (see Developer Portal)
        $bundleid = 'com.d.app';               # <- Your Bundle ID
        $url = 'https://api.push.apple.com'; # <- production url, or use 
        //$url = 'https://api.sandbox.push.apple.com'; # <- development url, or use 

 
        //print_r($optionalData) exit;
        $pload = isset($optionalData) ? $optionalData : [];
        
        $payload = array();
        $n_type = $optionalData['n_type'];
        $payload['aps'] = array('noti_type' => $n_type,'alert' => $message, 'badge' => intval(0), 'sound' => 'default','pload'=>$pload, 'n_type' => $n_type  );
        $payload = json_encode($payload);

 		//print_r($payload); exit;

        $key = openssl_pkey_get_private('file://'.$keyfile);

 

        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

 

        // $header_encoded = base64($header);
        // $claims_encoded = base64($claims);
        $header_encoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $claims_encoded = rtrim(strtr(base64_encode(json_encode($claims)), '+/', '-_'), '=');

 

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

 

        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

 

        if(is_array($deviceIds)){
            foreach ($deviceIds as $k => $v) {
                $http2ch = curl_init();
                curl_setopt_array($http2ch, array(
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    CURLOPT_URL => "$url/3/device/$v",
                    CURLOPT_PORT => 443,
                    CURLOPT_HTTPHEADER => array(
                        "apns-topic: {$bundleid}",
                        "authorization: bearer $jwt"
                    ),
                    CURLOPT_POST => TRUE,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HEADER => 1
                ));

                $result = curl_exec($http2ch);
                //print_r($deviceIds);
                if ($result === FALSE) {
                    echo "Error for given device : ".$v;
                    //$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                    //throw new Exception("Curl failed: ".curl_error($http2ch));
                }
            }
        }else{
            $http2ch = curl_init();
            curl_setopt_array($http2ch, array(
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_URL => "$url/3/device/$deviceIds",
                CURLOPT_PORT => 443,
                CURLOPT_HTTPHEADER => array(
                    "apns-topic: {$bundleid}",
                    "authorization: bearer $jwt"
                ),
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HEADER => 1
            ));

 

            $result = curl_exec($http2ch);
            
            if ($result === FALSE) {
                echo "Error for one device : ".$deviceIds;
                //$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
                //throw new Exception("Curl failed: ".curl_error($http2ch));
            }            
        }        
        return true;            
    }

	//subscriptionsList => It is used for get Subscription plan List
	public function subscriptionsList(){
        $query = Subscription::where('country', 'US')->get();
        if(!empty($query)){
        	//$query =  $query->toArray();
        	$query->code = 200;
        }else{
        	$query->code = 400;
        }
        return $query;
    }

    //pendingSubscriptionPlan =>  It is used for save the purchased plan which is pending
 	public function pendingSubscriptionPlan($arg,$userId)
    { 
    	$data = $arg;
		$u_id =  $userId;
		$itunesReceipt = $data['itunes_receipt'];

        $receiptData = '{"receipt-data":"'.$itunesReceipt.'","password":"51197df0c08744ca903b0dcc0f0a259aa"}';

        $endpoint =  'https://sandbox.itunes.apple.com/verifyReceipt';

		$query = Transaction::where('user_id','=',$u_id )
        ->leftjoin('subscriptions','transactions.subscription_id','subscriptions.id')
        ->where('payment_status','=',1)
        ->where('expired_at', '>', NOW())
        ->orderBy('expired_at','DESC')
        ->first();
       //	print_r($query); exit;
        
        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);

        $errno = curl_errno($ch);

        //print_r($errno); exit;

        if($errno==0){

            $response = curl_exec($ch);

            $receiptInfo = json_decode($response,true);

            if(!empty($receiptInfo)){

                if(isset($receiptInfo['status']) && $receiptInfo['status']==0){

                    $latestReceiptInfo = $receiptInfo['latest_receipt_info'];

                    $latestTransactioninfo = $latestReceiptInfo[count($latestReceiptInfo)-1];

                    //echo'<pre>';print_r($latestTransactioninfo);

                   /* $SubscriptionModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

                    $subscriptionData = $SubscriptionModel

                    ->find()

                    ->select(['id','price'])

                    ->where(['itunes_product_id'=>$latestTransactioninfo['product_id']])

                    ->first();  */ 
                    $find_other_user = Transaction::where('user_id','!=',$u_id )
			        ->where('itune_original_transaction_id','=',$latestTransactioninfo['original_transaction_id'])
			        ->first();

	                //print_r($find_other_user); exit;
                    
                    if(empty($find_other_user)){
	                    $transactionData = new Transaction();
						$transactionData->user_id = $u_id;
						$transactionData->subscription_id = 1;
						$transactionData->total_amount 	=  9.99;
						$transactionData->payment_status 	=  1;
						$transactionData->itune_original_transaction_id = $latestTransactioninfo['original_transaction_id'];
						$transactionData->itunes_receipt = $itunesReceipt;
						$transactionData->orderId = $latestTransactioninfo['transaction_id'];
						$transactionData->packageName = $latestTransactioninfo['product_id'];
						$transactionData->productId = $latestTransactioninfo['product_id'];
						$transactionData->purchaseTime =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));
						$transactionData->purchaseState =  1;
						$transactionData->created_at =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['purchase_date']));
						$transactionData->expired_at =  date('Y-m-d H:i:s',strtotime($latestTransactioninfo['expires_date']));
						$transactionData->device_type = 0;
						$transactionData->purchaseToken = 'Iphone';
						if ($result = $transactionData->save()){
	                        $transaction_last_id = $transactionData->id;
	                      	$user = User::where('id', $u_id)
						       		->update([
						           'itunes_autorenewal' => 1 ,'is_subscribe' => 1,'active_subscription' => 1,
						           'last_transaction_id' => $transaction_last_id
					        ]);	
	                     
	                       	$is_success = 221;
						    //print_r($query); exit;


	                    }else{
	                        $is_success = 423;

	                    }
	                }else{
	                	$is_success = 424;
	                }

                }else{
                	$user = User::where('id', $u_id)
					       		->update([
					           'itunes_autorenewal' => 0 
				        ]);	

                     $is_success = 424;
                }

            }

        }

        return $is_success;

        
    } 

	//subscriptions -> It is used for get Subscription Type
	public function subscriptions()
	{ 

        if ($this->request->is('get')){

            $data = $this->request->query;

            $uid = $this->userid;

            $SubscriptionsModel = TableRegistry::get('Subscriptions'); //use Cake\ORM\TableRegistry;

            $querySubscriptions = $SubscriptionsModel

            ->find();

            $TransactionsModel = TableRegistry::get('Transactions'); //use Cake\ORM\TableRegistry;

            $query = $TransactionsModel

            ->find()

            ->contain(['Subscriptions','Users'])

            ->where(['user_id'=>$uid])

            ->where(['payment_status'=>'1'])

            ->where(['NOW()<`expired_at`'])

            ->order(['expired_at'=>'DESC'])

             ->first();

            $timestamp = strtotime(date('Y-m-d H:i:s'));

            if(!empty($query)){

                $query =  $query->toArray();

                if(!empty($query['user']['id'])){

                    $addded_date = strtotime($query['user']['added_date']);

                }else{

                    $addded_date ='';

                }

                if($query['device_type']== 0){

                     $this->set([

                    'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>$query['subscription']['name'],'plan_id'=>$query['subscription_id'],'added_date'=>$addded_date,'itune_original_transaction_id'=>$query['itune_original_transaction_id'],'itunes_receipt'=>json_decode($query['itunes_receipt'])),

                    'code' => 209,

                    'msg'=> responseMsg(209),

                    '_serialize' => ['code','data','msg']

                 ]);



                }else{     

                    $this->set([

                        'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>$query['subscription']['name'],'plan_id'=>$query['subscription_id'],'added_date'=>$addded_date,'itune_original_transaction_id'=>$query['itune_original_transaction_id'],),

                        'code' => 209,

                        'msg'=> responseMsg(209),

                        '_serialize' => ['code','data','msg']

                     ]);

                }

            }else{

                $querySubscriptions =  $querySubscriptions->toArray();

                  $this->set([

                    'data' => array('Subscriptions'=>$querySubscriptions,'timestamp' =>$timestamp,'plan_name'=>'','plan_id'=>0,'added_date'=>'','itune_original_transaction_id'=>''),

                    'code' => 209,

                    'msg'=> responseMsg(209),

                    '_serialize' => ['code','data','msg']

                 ]);

            }

        }
    }

	//newSubscriptionPlan => It is used for Add new Subscription Plan (not need)
	public function newSubscriptionPlan()
    { 

        if ($this->request->is('post')){



            $data = $this->request->data;

            //pr($data);

            $u_id = $this->userid;

            $this->loadModel('Transactions');

            $Transactions = TableRegistry::get('Transactions'); 

            $transaction = $this->Transactions->newEntity();

            $transaction = $this->Transactions->patchEntity($transaction, $data);

            $transaction ['user_id'] = $this->userid;

            $created_at = $data['created_at']/1000;

            $transaction ['created_at'] =  date('Y-m-d H:i:s', $created_at);

            $expired_at = $data['expired_at']/1000;

            $transaction ['expired_at'] =date('Y-m-d H:i:s', $expired_at);

            

            //prd($transaction);

            if ($this->Transactions->save($transaction)){

                $this->loadModel('Users');

                $UserModel = TableRegistry::get('Users'); //use Cake\ORM\TableRegistry;

                $user = $UserModel->get($u_id);

                $user->itunes_autorenewal = 0;

                $user->active_subscription = $data['subscription_id'];

                $user->last_transaction_id = $transaction_last_id;

                $UserModel->save($user);

                $this->set([

                    'msg'=> responseMsg(210),

                    'code'  => 200,

                    '_serialize' => ['code','msg']

                ]);

                

            }else{

                 $this->set([

                    'msg'=> responseMsg(418),

                    'code'  => 418,

                    '_serialize' => ['code','msg']

                ]);

            }

        }
    }


	//actionCheckTransactionId => This function is used to check original trasaction id of itunes.
	public function actionCheckTransactionId()
    {   

        $Transactions = TableRegistry::get('Transactions'); 

        if ($this->request->is('post')){

            $data = $this->request->data;

            $userId = $this->userid;

            $itune_original_transaction_id = $data['itune_original_transaction_id'];

            $subscription = $Transactions

            ->find()

            ->where(['itune_original_transaction_id'=> $itune_original_transaction_id])

            ->where(['NOW()>`expired_at`'])

            ->first();  

            if(empty($subscription)){

                 $this->set([

                    'msg'=> responseMsg(210),

                    'data' => '',

                    'code'  => 200,

                    '_serialize' => ['code','msg','data']

                 ]);

            }else{

                $this->set([

                    'msg'=> responseMsg(436),

                    'data' => '',

                    'code'  => 436,

                    '_serialize' => ['code','msg','data']

                 ]);

            }

        }
    }  

	

	///androidSubscreption
	public function androidSubscreption($arg,$userId) {

        $request = $this->request;
        $postData = $arg;
		$u_id =  $userId;
        

       
        $requestStatus = 1;

        if( !isset($postData['orderId']) ) { $requestStatus = 0; }

        if( !isset($postData['productId']) ) { $requestStatus = 0; }

        if( !isset($postData['packageName']) ) { $requestStatus = 0; }

        if( !isset($postData['autoRenewing']) ) { $requestStatus = 0; }

        if( !isset($postData['purchaseToken']) ) { $requestStatus = 0; }

        if( !isset($postData['purchaseTime']) ) { $requestStatus = 0; }

        



        if($requestStatus==1) { 



            $user_id = $this->userid;

            /*$subTable = TableRegistry::get('Subscreption'); 

            $subData = $subTable->find()

                        ->where(['user_id'=>$user_id, 'status'=>1])

                        ->first();*/



            /*if(!empty($subData)) {



                $Result['code'] = '217';

                $Result['message'] = $this->ErrorMessages($Result['code']);

                echo json_encode($Result); exit;



            } else {*/



                require_once app_path().'/GoogleClientApi/Google_Client.php';

                require_once app_path().'/GoogleClientApi/auth/Google_AssertionCredentials.php';



            $CLIENT_ID = '100377813809460893738';

                //'110053402852490647256';

            $SERVICE_ACCOUNT_NAME = 'hopple-subscriptions@hopple.iam.gserviceaccount.com';
            $KEY_FILE = app_path().'/GoogleClientApi/hopple-39e53e5c539b.p12';

            $KEY_PW   = 'notasecret';



            $key = file_get_contents($KEY_FILE);

            $client = new \Google_Client();

            $client->setApplicationName("hopple");



                $cred = new \Google_AssertionCredentials(

                            $SERVICE_ACCOUNT_NAME,

                            array('https://www.googleapis.com/auth/androidpublisher'),

                            $key);  



                $client->setAssertionCredentials($cred);

                $client->setClientId($CLIENT_ID);

               

                if ($client->getAuth()->isAccessTokenExpired()) {

                    try {

                        $client->getAuth()->refreshTokenWithAssertion($cred);

                    } catch (Exception $e) {

                    }

                }

                $token = json_decode($client->getAccessToken());
                //print_r($token); exit;
                    

                $expireTime = "";

                $amount = 0;

                if( isset($token->access_token) && !empty($token->access_token) ) {

                    $appid = $postData['packageName'];

                    $productID = $postData['productId'];

                    $purchaseToken = $postData['purchaseToken'];



                    $ch = curl_init();

                    $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v3/applications/";

                    $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                    $res = $token->access_token;
                    //print_r($res); exit;



                    $ch = curl_init();

                    curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                    $result = curl_exec($ch);

                    $result = json_decode($result, true);

                    //print_r($result); exit;

                    

                    if(isset($result["startTimeMillis"])) {

                        $startTime = date('Y-m-d H:i:s', $result["startTimeMillis"]/1000. - date("Z"));

                        //$amount = $result["priceAmountMicros"]/1000000;

                    }

                    if(isset($result["expiryTimeMillis"])) {

                        $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));

                        $amount = $result["priceAmountMicros"]/1000000;

                    }

                }

                if(!empty($result)){
	                $date = new \DateTime();

	                $date->setTimestamp($postData['purchaseTime']/1000);

	                $dateStart = $date->format('Y-m-d H:i:s');

	                $transactionData = new Transaction();
					$transactionData->user_id = $u_id;
					$transactionData->subscription_id = 1;
					$transactionData->total_amount 	= $amount;
					$transactionData->payment_status 	=  1;
					$transactionData->itune_original_transaction_id = $postData['orderId'];
					$transactionData->itunes_receipt = $result["orderId"];
					$transactionData->orderId = $result["orderId"];
					$transactionData->packageName = $postData['packageName'];
					$transactionData->productId = $productID;
					$transactionData->purchaseState =  @$postData['purchaseState'];
					$transactionData->created_at =  $dateStart;
					$transactionData->expired_at =  $expireTime;
					$transactionData->device_type = 2;
					$transactionData->purchaseToken = $postData['purchaseToken'];
					if ($result = $transactionData->save()){
	                    $transaction_last_id = $transactionData->id;
	                  	$user = User::where('id', $u_id)
					       		->update([
					           'itunes_autorenewal' => 1 ,'is_subscribe' => 1,'active_subscription' => 1,
					           'last_transaction_id' => $transaction_last_id
				        ]);	
	                 
	                   	$is_success = 221;
					    //print_r($query); exit;


	                }else{
	                    $is_success = 423;

	                }
	            }else{
	            	$is_success = 429;
	            }

        } else {

             $is_success = 424;

        }
        return $is_success;
        
    }


	//cronJobForSubscreption 
	public function cronJobForSubscreption() { //use for  cron
   

        $Result['code'] = '200';

        $request = $this->request;

        $requestStatus = 1;

        if($requestStatus==1) { 

             $currentDate = date('Y-m-d H:i:s');

            //$transactionsTable = TableRegistry::get('Transactions');

           /* $subData = $transactionsTable->find()

                        ->where(['expired_at < '=>$currentDate])

                        ->ToArray();*/
            $subData = Transaction::where('expired_at', '<', $currentDate)
	        ->get();
	        echo $currentDate;
	        //echo '<pre>'; print_r($subData); 
            if(!empty($subData) && count($subData)) {

                //---- get auth token ---------------

                require_once app_path().'/GoogleClientApi/Google_Client.php';

                require_once app_path().'/GoogleClientApi/auth/Google_AssertionCredentials.php';

                $CLIENT_ID = '100377813809460893738';

                    //'110053402852490647256';

                $SERVICE_ACCOUNT_NAME = 'hopple-subscriptions@hopple.iam.gserviceaccount.com';
                $KEY_FILE = app_path().'/GoogleClientApi/hopple-39e53e5c539b.p12';

                $KEY_PW   = 'notasecret';



                $key = file_get_contents($KEY_FILE);

                $client = new \Google_Client();

                $client->setApplicationName("hopple");


                $cred = new \Google_AssertionCredentials(

                            $SERVICE_ACCOUNT_NAME,

                            array('https://www.googleapis.com/auth/androidpublisher'),

                            $key);  



                $client->setAssertionCredentials($cred);

                $client->setClientId($CLIENT_ID);

                

                if ($client->getAuth()->isAccessTokenExpired()) {

                    try {

                        $client->getAuth()->refreshTokenWithAssertion($cred);

                    } catch (Exception $e) {

                    }

                }

                $token = json_decode($client->getAccessToken());





                //---- cron job work  ---------------------



                foreach ($subData as $key => $val) {

                    if( $val->device_type==2 ) {  // android
	                	

                        $expireTime = "";

                        $amount = 0;

                        if( isset($token->access_token) && !empty($token->access_token) ) {

                            $appid = $val->packageName;

                            $productID = $val->productId;

                            $purchaseToken = $val->purchaseToken;



                            $VALIDATE_URL = "https://www.googleapis.com/androidpublisher/v3/applications/";

                            $VALIDATE_URL .= $appid."/purchases/subscriptions/".$productID."/tokens/".$purchaseToken;

                            $res = $token->access_token;



                            $ch = curl_init();

                            curl_setopt($ch,CURLOPT_URL,$VALIDATE_URL."?access_token=".$res);

                            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                            $result = curl_exec($ch);

                            $result = json_decode($result, true);

	                        if(isset($result["expiryTimeMillis"])) {
	                        	echo '<pre>'; print_r($result);

                                $expireTime = date('Y-m-d H:i:s', $result["expiryTimeMillis"]/1000. - date("Z"));
                                echo  $expireTime;
                                $amount = $result["priceAmountMicros"]/1000000;

                            	echo 'SUNIL'.$val->user_id; 

                                if($expireTime > date('Y-m-d H:i:s')) {
                                	echo 'Renew Test Sunil';
                                   /* Transaction::where('id',  $val->user_id)
							       		->update([
							           'expired_at' => $expireTime,
							           'payment_status' => 1
						        	]);	*/

                                    User::where('id',  $val->user_id)
                                    	->where('is_subscribe',0)
							       		->update([
							           'is_subscribe' => 1
						        	]);	

                                 

                                } else {

                                    echo 'Expire Test Sunil Aadroid';
                                    /*Transaction::where('id',  $val->user_id)
							       		->update([
							           'payment_status' => 2
						        	]);	*/
        

                                            
							       	User::where('id',  $val->user_id)
                                    	->where('is_subscribe',1)
							       		->update([
							           'is_subscribe' => 0
						        	]);	


                                    
                                } 



                            }

                        }

                    } else if( $val->device_type==1 ) {   // iphone

                        $itunesReceipt = $val->purchase_token;  

                        //$password = "58c72878cd56401a9c71927679fd9ee5";        

                        $password = "51197df0c08744ca903b0dcc0f0a259a";        

                        $receiptData = '{"receipt-data":"'.$itunesReceipt.'","password":"'. $password .'"}';

                        $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';

                        // $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';    



                        $ch = curl_init($endpoint);

                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        curl_setopt($ch, CURLOPT_POST, true);

                        curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                        $response = curl_exec($ch);

                        $errno = curl_errno($ch);



                        if($errno==0) {



                            $receiptInfo = json_decode($response,true);

                            

                            if( isset($receiptInfo['latest_receipt_info']) && !empty($receiptInfo['latest_receipt_info']) ) {



                                $lastData = end($receiptInfo['latest_receipt_info']);

                                

                                $expireTime = date('Y-m-d H:i:s',strtotime($lastData['expires_date']));



                                if($expireTime > date('Y-m-d H:i:s')) {
                                	echo '<pre>'; print_r($receiptInfo);
                                    echo 'SUNIL'.$val->user_id;

                                    $query = $transactionsTable->query();

                                    $result = $query->update()

                                            ->set(['expired_at' => $expireTime , 'status' => 1])

                                            ->where(['id' => $val->user_id])

                                            ->execute();

                                       User::where('id',  $val->user_id)
                                    	->where('is_subscribe',0)
							       		->update([
							           'is_subscribe' => 1,
							           'active_subscription' => 1
						        	]);	     

                                   /* $salonQuery = $userTable->query();

                                    $salonQuery->update()

                                                    ->set(['active_subscription' => 1])

                                                    ->where(['id' => $val->user_id, 'active_subscription' => 0])

                                                    ->execute();*/

                                } else {

                                    $query = $transactionsTable->query();

                                    /*$result = $query->update()

                                            ->set(['payment_status' => 2])

                                            ->where(['id' => $val->id])

                                            ->execute();*/

                                    User::where('id',  $val->user_id)
                                    	->where('is_subscribe',1)
							       		->update([
							           'is_subscribe' => 0,
							           'active_subscription' => 0
						        	]);	

                                      echo 'Expire Test Sunil IOS';
                                    $salonQuery = $userTable->query();


                                } 
                            }
                        }       
                    }
                }

            } 
        }   

        exit;   
    }

	
	
	public function logout($data){

		$rescod = "";
		//print_r($data); exit;
		if ($data) {
        
			$user =  User::findorfail($data);
			$user->device_id = "";
			$user->device_type = 2;
			$user->save();

			$user = Auth::user()->token();
        	//$user->revoke();
        	$rescod = 642;

    	}else{

        	$rescod = 461;

    	}
		return $rescod;
	}

	public function deleteAccount($data){
		


		$deletepost =  Post::where('u_id',$data['userid'])
		->delete();	


		$deleterepost =  Post::where('repost_u_id',$data['userid'])
		->delete();	


		$deletefav =  Favourite::where('f_user_id',$data['userid'])
		->delete();	


		$deletefollow =  Follow::where('user_id',$data['userid'])
		->delete();


		$deletefollowby =  Follow::where('follow_by',$data['userid'])
		->delete();	
		

		$deletelike =  Like::where('l_user_id',$data['userid'])
		->delete();	

		$deletephoto =  Photo::where('p_u_id',$data['userid'])
		->delete();	


		$deletereport =  Report::where('user_id',$data['userid'])
		->delete();	

		$deletereported =  Report::where('reported_user',$data['userid'])
		->delete();


		$deletevote =  Vote::where('v_user_id',$data['userid'])
		->delete();	



		$deleteuser =  User::where('id',$data['userid'])
		->delete();
	
		return 1;
	}

	public function notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type){
		$user = User::find($sender); //notification sender
		if($userArr != Auth::user()->id){
			$receiver_detail = User::find($userArr);// Notification Recceiver
			$receiver_name = @$receiver_detail['first_name'];
			$device_token = @$receiver_detail['device_token'];
			// Notification Payload
			$data['userid'] = $sender;
			$data['name'] = $user['first_name'];
			$data['message'] = $message;
			$data['n_type'] = $n_type;
			if($n_type == 29){
				$data['ref_id'] = $ref_id['g_id'];
				$data['room_id'] = $ref_id['room_id'];
				$data['notification_title'] = $ref_id['notification_title'];
				$message;
			}else{
				$data['notification_title'] = 'Social Trade';
				$message = $user['first_name'].' '.$message;
				$data['ref_id'] = $ref_id;
			}

			$notify = array ();
			$notify['receiver_id'] = $userArr;
			$notify['relData'] = $data;
			$notify['message'] = $message;
			//print_r($notify); exit;
			$test =  $this->sendPushNotification($notify); 

			if($n_type != 29){
				$this->notification_save($userArr,$notify,$message,$user['first_name'],$n_type,$receiver_name,$device_token);
			}
		}
	}


	public function tranding_list($data){
		$model 		= "App\Models\Post";	
		$post_type = @$data['post_type'];
		$query = $model::query();
			

			if(isset($partner_type)){
				//echo $selected_date ; exit;
				$query =$query->where('post_type','=',@$post_type);
			}

				
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
					->where('status',1)
					->leftjoin('users','posts.u_id','users.id')
					->orderBy('posts.id', 'DESC')
					->paginate(10,['*'],'page_no');

			$query->total_count = $model::where('status',1)
					->count();
			$partner = $query;
			//print_r($partner); exit;
		/*$partner = Partner::where('status','=',1)->paginate(10,['*'],'page_no');
		$partner_array = array();
		$Partner_list = array();*/

		/*foreach($partner as $list){
			$partner_array['id'] 			=  	@$list->id ? $list->id : '';
			$partner_array['name'] 	=  	@$list->name ? $list->name : '';
			$partner_array['desc'] 	=  	@$list->desc ? $list->desc : '';
			$partner_array['photo'] 		=  	@$list->photo ? $list->photo : '';
			$partner_array['status'] 		=  	@$list->status ? $list->status : '';
			
			array_push($Partner_list,$partner_array);
		}*/
		//echo '<pre>'; print_r($partner); exit;
		
		return $partner;
	}

	public function userList($data){
		$model 		= "App\User";	
		$name = @$data['name'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		if(isset($name)){
			//echo $selected_date ; exit;
				$query =$query->where('first_name','LIKE','%'.$name.'%');
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','follows.*')
				->where('users.id','!=',$userId)
				->where('users.id','!=',1)
				//->leftjoin('users','follows.user_id','users.id')
				->leftjoin('follows','users.id','follows.user_id')
				->orderBy('users.first_name', 'ASC')
				->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('users.id','!=',$userId)->where('users.id','!=',1)
				->count();
		$users = $query;
		return $users;
	}

	public function alluserList($data){
		$model 		= "App\User";	
		$name = @$data['name'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		if(isset($name)){
			//echo $selected_date ; exit;
				$query =$query->where('first_name','LIKE','%'.$name.'%');
		}

		$query = $query->select('users.id as userid','users.first_name as first_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type')
				->where('users.id','!=',$userId)
				->where('users.id','!=',1)
				//->leftjoin('users','follows.user_id','users.id')
				//->leftjoin('follows','users.id','follows.user_id')
				->orderBy('users.first_name', 'ASC')
				->paginate(10,['*'],'page_no');

		$query->total_count = $model::where('users.id','!=',$userId)->where('users.id','!=',1)
				->count();
		$users = $query;
		return $users;
	}

	public function createDebet($data){
		//print_r($data); exit;
		$userId = Auth::user()->id;
		$send_notification = 0;
		if($data['topic'] !=  ''){
			if(@$data['id']){
				$is_new = 0;
				$debet = Debet::where('id','=',@$data['id'])
					->first();
				$debet->id = @$data['id'] ? $data['id']: 0;	
			}else{
				$is_new = 1;
				$send_notification = 1;
				$debet = new Debet();
			}
			$debet->topic = @$data['topic'] ? $data['topic']: '';
			$debet->date = @$data['date'] ? $data['date']: '';
			$debet->time_slot = @$data['time_slot'] ? $data['time_slot']: '';
			$debet->u_id = $userId;
			$debet->photo = @$data['photo'] ? $data['photo']: '';
			$debet->opponant_id = @$data['opponant_id'] ? $data['opponant_id']: '';
			$debet->debet_desc = @$data['debet_desc'] ? $data['debet_desc']: '';
			$debet->created_at =  date ( 'Y-m-d H:i:s' );
			
			//echo '<pre>'; print_r($room); exit;
			$debet->save();

			$lastid = $debet->id;
			if($is_new == 1){// if new group then add first user in this Group as admin
				
				//print_r($all_invited_user); exit;
				$user = User::find($userId);
				$sender_name = $user['first_name'];
				$receiver_detail = User::find($debet->opponant_id);
					$receiver_name = @$receiver_detail['first_name'];
					$fcm_token = @$receiver_detail['fcm_token'];
					
					if($send_notification  == 1){
						$sender = $userId;
						$message ="has Challenge to Debet on topic: ".$data['topic'];
						$n_type = 12;
						$ref_id = $lastid;//group id
						$push_type = 1; //1 for normal 2 for seclient 
						// get follower list and send notification
						   
					    $userArr = $debet->opponant_id;
						$this->notification_master($sender,$userArr,$message,$n_type,$ref_id,$push_type);
							
						
					}
			}
			
			$partner_array['code'] = 200;
			$partner_array['data'] = $lastid;

		
		}else{

			$partner_array['code'] = 633;

		}
		//echo '<pre>'; print_r($partner_array); exit;

		return $partner_array;
	}

	public function debetrequestList($data){
		$model 		= "App\Models\Debet";	
		$list_type = @$data['list_type'];
		$userId= Auth::user()->id;
        $Is_method  = 0; 
		$query = $model::query();
		/*if(isset($post_type)){
			//echo $selected_date ; exit;
			$query =$query->where('post_type','=',@$post_type);
		}*/
		if($list_type == 'receive'){ //Receve debet
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','debets.*')
					->where('debets.status',0)
					->where('debets.opponant_id',$userId)
					//->where('group_members.gm_u_id',$userId)
					->leftjoin('users','debets.u_id','users.id')
					->orderBy('users.first_name', 'ASC')
					->paginate(100,['*'],'page_no');

			$query->total_count = $model::where('debets.status',0)
					->count();
			$users = $query;
			return $users;
		}else{ //sended by me
			$query = $query->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','debets.*')
					->where('debets.status',0)
					->where('debets.u_id',$userId)
					//->where('group_members.gm_u_id',$userId)
					->leftjoin('users','debets.opponant_id','users.id')
					->orderBy('users.first_name', 'ASC')
					->paginate(100,['*'],'page_no');

			$query->total_count = $model::where('debets.status',0)
					->count();
			$users = $query;
			return $users;
		}
	}

	public function debetacceptDecline($arg,$userId){
		$checkfollow = Debet::where('id', $arg['id'])
		->where('status', 0)
		->first();
		if(!empty($checkfollow)){
			if($arg['status'] == 1){
				Debet::where('id', $arg['id'])
		       		->update([
		           'status' => 1
	        	]);
				$result= 1;
		    }else{ //declne
		    	Debet::where('id', $arg['id'])
		       		->update([
		           'status' => 2
	        	]);
				//$deletefollow =  Debet::where('id',$arg['gid'])->delete();	
				$result= 3;
		    }
		}else{
			$result = 2;
		}		
		return $result;
	}
} 

