<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Repository\UserRepository;
use App\User;
use App\Http\Controllers\Utility\DataService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use DB;
Class ApiService{

	public  function checkemail_phone($arg)
	{

		$data = new DataService();
		
		if(isset($arg['phone'])){
			$requestStatus = 1;
		}else{
			$requestStatus = 0;
		}

		if($requestStatus == 1){

			$UserRepostitory   = new UserRepository();
    	 	$User   =  $UserRepostitory->check_user($arg);
    	 	//print_r($User); exit;
			/*if(@$User['user_status'] != 0){

				if($User['phone'] == @$arg['phone']){
					$data->error_code = 203; 

				}else if($User['email'] == @$arg['email']){

					$data->error_code = 500;
				}else{

					$data->error_code = 499;
				}

				if(@$arg['password']){

                    if($User['password'] == @$arg['password']){
                        
                        $data->error_code =	203;
                    }
				}
			
			}else{*/

				$unactive_user   =  $UserRepostitory->check_unactive_user($arg);
    	 	
				if($unactive_user){
					$arg['id'] = $unactive_user['id']; 

					$UserRepostitory   = new UserRepository();
    		 		$User   =  $UserRepostitory->register($arg);
    				
				}else{
	
					$UserRepostitory   = new UserRepository();
    	 			$User   =  $UserRepostitory->register($arg);
    	 		}
    	 		
    	 		if($User){
    	 			$data->error_code =	203;
    	 		}
			//}
		}else{
			$data->error_code = 403; 

		}
		return $data;
	}


	public function verifyUser($arg){

		$data  = new DataService();
		
		/*	if($arg['email'] != ""){

			$Checkuser = Auth::attempt( ['email' => $arg['email'], 'password' => $arg['password']] );

		}else if($arg['phone'] != ""){

			$Checkuser = Auth::attempt( ['phone' => $arg['phone'], 'password' => $arg['password']] );		
		}*/

		/*if($Checkuser){*/
			
			if( isset($arg['code']) && isset($arg['phone'])){
				
				$UserRepostitory = new UserRepository();
				$getuser = $UserRepostitory->getuser($arg);
				
				if($getuser){
					//print_r($getuser); exit;
					//$user = Auth::user(); 
					//$getuser['token'] = $user->createToken('hopple')->accessToken;
					//$getuser['id'] = $user->id;

					if($getuser['code'] == 205){

						unset($getuser['code']);
						$data->error_code = 205;
						$data->data = $getuser; 

					}else if($getuser['code'] == 422){
							
						$data->error_code = $getuser['code'];
					
					}
				}else{
					$data->error_code = $getuser['code'];
				}
				
	    	 	
			}else{

				$data->error_code =	403;

			}

		/*}else{

			$data->error_code =	422; 


		}*/
		
		return $data;
		
		

	}

	public function login($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		$arg['password'] = $arg['code'];
		if(Auth::attempt(['phone'=> @$arg['phone'],'password'=> $arg['password']] )){ 
			$user = Auth::user(); 
			DB::table('oauth_access_tokens')->where('user_id',$user['id'])->delete();
			if(Auth::user()->is_phone_verified == 0){
				$data->error_code = 209; 
			}else{
				//$user = Auth::user()->token();
        		//$user->revoke();
				$arg['token'] = $user->createToken('hopple')->accessToken;
				if(isset($arg['phone']) && isset($arg['password'])){
					$requestStatus = 1;		
				}else{

					$requestStatus = 0;
				}
		        if( !isset($arg['password']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_id']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_type']) ) { $requestStatus = 0; }
		       
		        $validpassword = 0;

		        
		 		if( $requestStatus==1 ) {
					$forgot_pass = 0;
					$user = $UserRepostitory->login($arg);
					//print_r($user['id']); exit;
					//$code  = $UserRepostitory->logout($user['id']);
					//print_r($user); exit;
					if(!empty($user)){

						$pass = password_verify($arg['password'], $user['password']);

						$refreshToken = DB::table('oauth_access_tokens')
										->where('user_id',$user['id'])
										->whereNull('name')
										->update(['revoked' => 1]);
										

						if($pass == 1){

							$validpassword =0;	
						}else{

							$validpassword =1;
						}

						if($validpassword == 0){
							
							$clear_token = $UserRepostitory->clear_user_token($user['device_id']);

							if($user['user_status'] == 0){
		                    	$is_reset = 1;   
							}

							//Check user reactivated by admin 
							if($user['user_status']  ==  2){
								
								$data->error_code = 498;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else if($user['isdelete'] ==  1){
								
								$data->error_code = 643;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else{ //Login users

								$arg['id'] = $user['id'];
								$userDetail  = $UserRepostitory->get_user_detail($arg);
								$data->error_code = 200;
								$data->data = $userDetail; 
							}

						}else{

							$data->error_code = 411;
						}


					}else{

						$data->error_code = 401;

					}
				}else{


		 			$data->error_code = 402; 
		 		}
		 	}	
	 	}else if(Auth::attempt(['email'=> @$arg['email'],'password'=> $arg['password']] )){ 
			$user = Auth::user(); 
			if(Auth::user()->is_email_verified == 0){
				$data->error_code = 209; 
			}else{
				$arg['token'] = $user->createToken('hopple')->accessToken;
				if(isset($arg['email']) && isset($arg['password'])){
					$requestStatus = 1;		
				}else{

					$requestStatus = 0;
				}
		        if( !isset($arg['password']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_id']) ) { $requestStatus = 0; }
		        if( !isset($arg['device_type']) ) { $requestStatus = 0; }
		       
		        $validpassword = 0;

		        
		 		if( $requestStatus==1 ) {
					$forgot_pass = 0;
					$user = $UserRepostitory->login($arg);
					if(!empty($user)){
						$pass = password_verify($arg['password'], $user['password']);
						DB::table('oauth_access_tokens')->where('user_id',$user['id'])->delete();	
						$refreshToken = DB::table('oauth_access_tokens')
										->where('user_id',$user['id'])
										->whereNull('name')
										->update(['revoked' => 1]);

						if($pass == 1){

							$validpassword =0;	
						}else{

							$validpassword =1;
						}

						if($validpassword == 0){
							
							$clear_token = $UserRepostitory->clear_user_token($user['device_id']);

							if($user['user_status'] == 0){
		                    	$is_reset = 1;   
							}

							//Check user reactivated by admin 
							if($user['user_status']  ==  2){
								
								$data->error_code = 498;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else if($user['isdelete'] ==  1){
								
								$data->error_code = 643;
								$data->email = $user['email'];
			                    $data->phone = $user['phone'];
							
							}else{ //Login users

								$arg['id'] = $user['id'];
								$userDetail  = $UserRepostitory->get_user_detail($arg);
								$data->error_code = 200;
								$data->data = $userDetail; 
							}

						}else{

							$data->error_code = 411;
						}


					}else{

						$data->error_code = 401;

					}
				}else{


		 			$data->error_code = 402; 
		 		}
		 	}	
	 	}else{

	 		$data->error_code = 401;//UnAuthorizated users
	 	}	

 		return $data;
	}


	public  function socialLogin($arg)
	{

		$data = new DataService();
		
		if(isset($arg['facebook_id'])){
			$requestStatus = 1;
		}elseif(isset($arg['google_id'])){
			$requestStatus = 1;
		}elseif(isset($arg['apple_id'])){
			$requestStatus = 1;
		}else{
			$requestStatus = 0;
		}

		if($requestStatus == 1){
			//echo rand(); exit;
			$UserRepostitory   = new UserRepository();
    	 	$User   =  $UserRepostitory->check_user($arg);
			if(@$User['id'] != ""){
				DB::table('oauth_access_tokens')->where('user_id',$User['id'])->delete();
				$arg['token'] = $User->createToken('hopple')->accessToken;
				if(Auth::loginUsingId($User['id'])){
					$user = Auth::user();
					$refreshToken = DB::table('oauth_access_tokens')
					->where('user_id',$User['id'])
					->whereNull('name')
					->update(['revoked' => 1]);
					$clear_token = $UserRepostitory->clear_user_token($User['device_id']);

	 				$arg['id'] = $user['id'];
	 				//print_r($arg); exit;
					$userDetail  = $UserRepostitory->get_user_detail($arg);
					//$data->error_code = 200;
					$data->data = $userDetail; 
    	 			$data->error_code =	200;

				}
			}else{
				//echo rand(); exit;
				$UserRepostitory   = new UserRepository();
	 			$User   =  $UserRepostitory->social_register($arg);
	 			$User   =  $UserRepostitory->check_user($arg);
				//echo '<pre>'; print_r($User); exit;
	 			$arg['token'] = $User->createToken('hopple')->accessToken;
    	 		$arg['id'] = $User;
				if($User){
	    	 		if(Auth::loginUsingId($User['id'])){
						$user = Auth::user();
						$refreshToken = DB::table('oauth_access_tokens')
						->where('user_id',$User['id'])
						->whereNull('name')
						->update(['revoked' => 1]);
						$clear_token = $UserRepostitory->clear_user_token($User['device_id']);

		 				$arg['id'] = $user['id'];
		 				//print_r($arg); exit;
						$userDetail  = $UserRepostitory->get_user_detail($arg);
						//$data->error_code = 200;
						$data->data = $userDetail; 
	    	 			$data->error_code =	200;

					}
    	 		}
			}
		}else{
			$data->error_code = 403; 

		}
		return $data;
	}


	public function createPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->post_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->create_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}

	public function groupcreatePost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->grouppost_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->groupcreatePost($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}

	public function grouprepost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->group_post_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->grouprepost($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->groupdelete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}
	
	public function groupvote($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['v_option'])){
			$checkunique = $UserRepostitory->groupvote($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function groupdeletePost($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] =  Auth::user()->id;
		$gallery = $UserRepostitory->groupdelete_post($arg);
		$data->error_code = 302; 

		return $data;
	}

	public function group_post_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->group_post_detail($arg);
			if($getuser){
				$data->error_code = 213;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	public function groupfavourite($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id']) ){
			$checkunique = $UserRepostitory->groupfavourite($arg,$userid);
			//print_r($checkunique); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 220;
			}else{
				$data->error_code = 432;
			} 

		}else{

			$data->error_code = 403;
		}	
		
		$data->data = @$checkunique; 
		
		return $data;
	}
	public function grouppost_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->grouppost_list($request);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function grouplike($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id'])){
			$checkunique = $UserRepostitory->grouplike($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function groupcommentPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->groupcomment_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->groupcomment_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->groupdelete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}
	public function groupcomment_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['c_id'])){
			$checkunique = $UserRepostitory->groupcomment_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function deletePost($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] =  Auth::user()->id;
		$gallery = $UserRepostitory->delete_post($arg);
		$data->error_code = 302; 


		return $data;
	}



	public function commentPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->comment_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->comment_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}


    public function repost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->post_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->repost($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}
	
	public function forgotPassword($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['email']) || isset($arg['phone'])){
			$getuser = $UserRepostitory->login($arg);	
			if(isset($getuser->id) && $getuser->id > 0 ){
			
				$update_password = $UserRepostitory->forgot_password($arg,$getuser);	

				if($update_password){

					$data->error_code = 601;
				
				}else{

					$data->error_code = 470;
				}

			}else{

				$data->error_code = 430;

			}

		}
		//print_r($data); die;
		return $data;
	}


	public function photo_list($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] 	   = @$arg['userid'];
		$photo_list  = $UserRepostitory->photo_list($arg);

		if($photo_list){
			$data->error_code = 641;
			$data->data = $photo_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function reel_list($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] 	   = @$arg['userid'] ;
		$photo_list  = $UserRepostitory->reel_list($arg);

		if($photo_list){
			$data->error_code = 641;
			$data->data = $photo_list;
		
		}else{

			$data->error_code = 634;

		}
	
		return  $data;
	}

	public function reel_detail($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['reel_id']	= @$arg['reel_id'] ;
		$reelList  = $UserRepostitory->reel_detail($arg);

		if($reelList){
			$data->error_code = 641;
			$data->data = $reelList;
		
		}else{
			$data->error_code = 650;
		}

		return  $data;
	}


	public function user_status($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['user_id'] = $arg['user_id'];
		$reelList  = $UserRepostitory->user_status($arg);

		if($reelList){
			$data->error_code = 641;
			$data->data = $reelList;
		
		}else{
			$data->error_code = 650;
		}

		return  $data;
	}


	public function status_view($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['sender_id'] 	= $arg['sender_id'];
		$arg['receiver_id'] = $arg['receiver_id'];
		$arg['post_id'] 	= $arg['post_id'];
		
		$reelList  = $UserRepostitory->status_view($arg);

		if($reelList){
			$data->error_code = 641;
			$data->data = $reelList;
		
		}else{
			$data->error_code = 650;
		}

		return  $data;
	}

	public function status_list($arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] 	   = @$arg['userid'] ;
		$photo_list  = $UserRepostitory->status_list($arg);

		if($photo_list){
			$data->error_code = 641;
			$data->data = $photo_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function report_text_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$category_list  = $UserRepostitory->report_text_list(1);

		if($category_list){
			$data->error_code = 641;
			$data->data = $category_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

 	public function report($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		//print_r($arg); exit;
		if(isset($arg['post_id']) && isset($arg['desc']) ){
			$checkunique = $UserRepostitory->report($arg,$userid);
			if($checkunique == 1){
				$data->error_code = 222;
			}else{
				$data->error_code = 436;
			} 

		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

	public function category_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$category_list  = $UserRepostitory->category_list(1);

		if($category_list){
			$data->error_code = 641;
			$data->data = $category_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}



	//////////////////////////////////
	public function mygroup_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$group_list  = $UserRepostitory->mygroup_list($request);

		if($group_list){
			$data->error_code = 437;
			$data->data = $group_list;
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
	public function createGroup($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->post_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->createGroup($arg);
				//echo '<pre>'; print_r($gallery); exit;				
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 223;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
								
		//echo '<pre>'; print_r($data); exit;
		return $data;
	}



	public function joinGroup($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		 
		if(Auth::user()->id){
			
			$arg['userid'] =  Auth::user()->id;
			$gallery = $UserRepostitory->joinGroup($arg);
			//echo '<pre>'; print_r($gallery); exit;				
			if($gallery['code'] == 200){

				unset($gallery['code']);
				$data->error_code = 228;
				$data->data = $gallery;


			}else{
				unset($gallery['code']);
				$data->error_code = 649;
			}  
	
		}else{

			$data->error_code =  414;  //UnAuthorize user
		}

		
								
		//echo '<pre>'; print_r($data); exit;
		return $data;
	}


	public function acceptDecline($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['gm_id'])){
			$checkunique = $UserRepostitory->acceptDecline($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique== 1){// accept
				$data->error_code = 308;
			}elseif($checkunique == 3){ // decline
				$data->error_code = 309;
			}else{ //not found
				$data->error_code = 310;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function groupCancleRequest($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['g_id'])){
			$checkunique = $UserRepostitory->groupCancleRequest($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique== 1){// accept
				$data->error_code = 308;
			}elseif($checkunique == 3){ // decline
				$data->error_code = 309;
			}else{ //not found
				$data->error_code = 310;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function group_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->group_detail($arg);
			if($getuser){
				$data->error_code = 224;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	public function forum_topic_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$category_list  = $UserRepostitory->forum_topic_list(1);

		if($category_list){
			$data->error_code = 641;
			$data->data = $category_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}


	public function createForum($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->forum_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->createForum($arg);
				//echo '<pre>'; print_r($gallery); exit;				
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 223;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->deleteForum($arg);
			$data->error_code = 214; 
		}
								
		//echo '<pre>'; print_r($data); exit;
		return $data;
	}




	public function forum_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$group_list  = $UserRepostitory->forum_list($request);

		if($group_list){
			$data->error_code = 437;
			$data->data = $group_list;
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function forum_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->forum_detail($arg);
			if($getuser){
				$data->error_code = 213;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}



	public function forum_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id'])){
			$checkunique = $UserRepostitory->forum_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}


	public function forum_commentPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->forum_comment_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->forum_comment_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}



	public function forum_comment_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['c_id'])){
			$checkunique = $UserRepostitory->forum_comment_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}





	public function create_vote_them($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->vote_them_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->create_vote_them($arg);
				//echo '<pre>'; print_r($gallery); exit;				
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 223;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->deleteVoteThem($arg);
			$data->error_code = 214; 
		}
								
		//echo '<pre>'; print_r($data); exit;
		return $data;
	}




	public function vote_them_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$group_list  = $UserRepostitory->vote_them_list($request);

		if($group_list){
			$data->error_code = 437;
			$data->data = $group_list;
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function vote_them_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->vote_them_detail($arg);
			if($getuser){
				$data->error_code = 213;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}



	public function vote_them_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id'])){
			$checkunique = $UserRepostitory->vote_them_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}


	public function vote_them_commentPost($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->vote_them_comment_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->vote_them_comment_post($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_vote_them($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}



	public function vote_them_comment_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['c_id'])){
			$checkunique = $UserRepostitory->vote_them_comment_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}







	public function groupChat($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->groupchat_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->groupChat($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 311;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_post($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}

	public function groupChatMessageList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->groupChatMessageList($request);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}













	public function gender_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$gender_list  = $UserRepostitory->gender_list(1);

		if($gender_list){
			$data->error_code = 641;
			$data->data = $gender_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	

	public function report_list(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$report_list  = $UserRepostitory->report_list(1);

		if($report_list){
			$data->error_code = 641;
			$data->data = $report_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
	

	

	

	public function profile($method,$arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			$getuser  =   $UserRepostitory->getuserById($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}else if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->getupdateprofile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}

	public function user_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->getotheruserById($arg);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	

	public function subcategory_list($arg){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$subcategory_list  = $UserRepostitory->subcategory_list($arg['c_id']);

		if($subcategory_list){
			$data->error_code = 641;
			$data->data = $subcategory_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function pendingSubscriptionPlan($arg,$userId){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->pendingSubscriptionPlan($arg,$userId);
		//print_r($spendingSubscription_list); exit;
		if($spendingSubscription_list){
			$data->error_code = $spendingSubscription_list;
			//$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = $spendingSubscription_list;

		}
		
		
		return  $data;
	}

	public function androidSubscreption($arg,$userId){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->androidSubscreption($arg,$userId);
		//print_r($spendingSubscription_list); exit;
		if($spendingSubscription_list){	
			$data->error_code = $spendingSubscription_list;
			//$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = $spendingSubscription_list;

		}
		
		
		return  $data;
	}

	public function cronJobForSubscreption(){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$spendingSubscription_list  = $UserRepostitory->cronJobForSubscreption();

		if($spendingSubscription_list){
			$data->error_code = 221;
			$data->data = $spendingSubscription_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
	
	public function requestVerification($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->requestVerification($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}

	public function visibilty_profile($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 0){  // FOR POST METHOD
		
	
			$arg['Id'] = Auth::user()->id;

			$update = $UserRepostitory->visibilty_profile($arg);
			

				//print_r($update); exit;	
			if($update['code'] == 200){

				unset($update['code']);
				$data->error_code = 217;
				$data->data = $update; 

			}else if($update['code'] == 410){
					
				$data->error_code = $update['code'];
			
			}else{

				$data->error_code = 632;
			}  
		}

	
		return $data;
	}




	public function makeAdmin($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['g_id']) || isset($arg['userid'])){
			$getphoto = $UserRepostitory->makeAdmin($arg);	
			//echo '<pre>'; print_r($getphoto['code']); exit;
			//if(isset($getphoto->g_id) && $getphoto->g_id > 0 ){
			if($getphoto['code'] == 200){

				$data->error_code = 304;
				

			}else{

				$data->error_code = 438;

			}

		}
		//print_r($data); die;
		return $data;
	}


	public function removeAdmin($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['g_id']) || isset($arg['userid'])){
			$getphoto = $UserRepostitory->removeAdmin($arg);	
			//echo '<pre>'; print_r($getphoto['code']); exit;
			//if(isset($getphoto->g_id) && $getphoto->g_id > 0 ){
			if($getphoto['code'] == 200){

				$data->error_code = 305;
				

			}else{

				$data->error_code = 438;

			}

		}
		//print_r($data); die;
		return $data;
	}

	public function removeGroupUser($arg){
		//echo '<pre>'; print_r($arg); exit;
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['userid'] =  Auth::user()->id;
		$gallery = $UserRepostitory->removeGroupUser($arg);
		$data->error_code = 306; 


		return $data;
	}

	public function deleteGroup($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['g_id']) || isset($arg['userid'])){
			$getphoto = $UserRepostitory->deleteGroup($arg);	
			//echo '<pre>'; print_r($getphoto['code']); exit;
			//if(isset($getphoto->g_id) && $getphoto->g_id > 0 ){
			if($getphoto['code'] == 200){

				$data->error_code = 307;
				

			}else{

				$data->error_code = 438;

			}

		}
		//print_r($data); die;
		return $data;
	}

	

	

	public function gallery($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->view_gallery($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->gallery($arg);
		
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 218;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->delete_gallery($arg);
			$data->error_code = 214; 
		}
		
		return $data;
	}
	
	


	public function resetPassword($arg){


		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		if(isset($arg['password']) && isset($arg['id'])){

			$getuser =  $UserRepostitory->update_password($arg);

			if($getuser){
				if($getuser->is_forgot == 0){
					$data->error_code = 638;
				}else{
					$data->error_code = 645;
				}

			}else{

				$data->error_code = 639;
			}


		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

	

	


	public function check_username($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			//echo $userId ; exit;
			$getuser  =   $UserRepostitory->check_username($arg,$userId);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}


	public function update_device($method,$arg,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR POST METHOD

			$getuser  =   $UserRepostitory->update_device($arg,$userId);
			if($getuser){
				$data->error_code = 207;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
		return $data;
	}

	public function chat_user_sid_update($sid,$userId){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		
		$getuser  =   $UserRepostitory->chat_user_sid_update($sid,$userId);
		if($getuser){
			$data->error_code = 207;
			$data->data = $getuser;
		
		}else{

			$data->error_code = 631;
		}
		return $data;
	}


	public function chip($arg){

		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		if( isset($arg['chip_name']) && isset($arg['unique_id']) ){
			$arg['id'] = Auth::user()->id;
			
			$checkunique = $UserRepostitory->checkchip($arg);
			if($checkunique == 0){
				$getchip =  $UserRepostitory->chip($arg);
				//print_r($getchip);
				if($getchip){
					$data->error_code = 210;
					$data->data =$getchip;

				}else{
					$data->error_code = 640;
				}
			}else{// this user already register this chip 
				$data->error_code = 644;
			}


		}else{

			$data->error_code = 403;
		}	
		

		return $data;
	}

	public function like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id'])){
			$checkunique = $UserRepostitory->like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}

	public function follow($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['user_id'])){
			$checkunique = $UserRepostitory->follow($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}




	public function comment_like($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['c_id'])){
			$checkunique = $UserRepostitory->comment_like($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}
	

	public function vote($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['v_option'])){
			$checkunique = $UserRepostitory->vote($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 219;
			}else{
				$data->error_code = 433;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}
	

	public function favourite($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['post_id']) ){
			$checkunique = $UserRepostitory->favourite($arg,$userid);
			//print_r($checkunique); exit;
			if($checkunique['result'] == 1){
				$data->error_code = 220;
			}else{
				$data->error_code = 432;
			} 

		}else{

			$data->error_code = 403;
		}	
		
		$data->data = @$checkunique; 
		
		return $data;
	}


	public function recommend_list($request){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$partner_list  = $UserRepostitory->recommend_list($request);

		if($partner_list){
			$data->error_code = 647;
			$data->data = $partner_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function post_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->post_list($request);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function activity_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->activity_list($request);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}


	



	public function watch_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$post_list  = $UserRepostitory->watch_list($request);

		if($post_list){
			$data->error_code = 647;
			$data->data = $post_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}


	public function notificationList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$notification_list  = $UserRepostitory->notificationList($request);
		if($notification_list){
			$data->error_code = 277;
			$data->data = $notification_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function followUser($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->followUser($request);
		if($user_list){
			$data->error_code = 280;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function groupUser($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->groupUser($request);
		if($user_list){
			$data->error_code = 281;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function requestList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->requestList($request);
		if($user_list){
			$data->error_code = 280;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}


	public function post_detail($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD

			$getuser  =   $UserRepostitory->post_detail($arg);
			if($getuser){
				$data->error_code = 213;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}


	public function subscriptionsList(){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		
		$scription = $UserRepostitory->subscriptionsList();
		if($scription->code == 200){
			$scription->error_code = 220;
		}else{
			$scription->error_code = 433;
		} 
		
		return $scription;
	} 

	
	


	
	

	public function notification_match_detail($method,$arg,$user_id){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ //FOR GET METHOD
			$getuser  =   $UserRepostitory->notification_match_detail($arg,$user_id);
			if($getuser){
				$data->error_code = 303;
				$data->data = $getuser;
			
			}else{

				$data->error_code = 631;
			}
		}
	
		return $data;
	}

	public function logout(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg 	   = Auth::user()->id;
		$code  = $UserRepostitory->logout($arg);

		if($code == 642){
			$data->error_code = 642;
		
		}else{

			$data->error_code = 461;

		}

		return  $data;
	}


	public function deleteAccount(){

		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['userid'] =  Auth::user()->id;
		$gallery = $UserRepostitory->deleteAccount($arg);
		$data->error_code = 447; 


		return $data;
	}
	
	public function tranding_list($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$tranding_list  = $UserRepostitory->tranding_list($request);

		if($tranding_list){
			$data->error_code = 647;
			$data->data = $tranding_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}

	public function userList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->userList($request);
		if($user_list){
			$data->error_code = 280;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}	

	public function alluserList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->alluserList($request);
		if($user_list){
			$data->error_code = 280;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}
	public function createDebet($method,$arg){

		$data = new DataService();
		$UserRepostitory = new UserRepository();

		if($method == 1){ // FOR GET METHOD

			$gallery = $UserRepostitory->debet_detail($arg);
		
			$data->error_code = 218;
			$data->data = $gallery; 
		
		}else if($method == 2){ // FOR POST METHOD

			if(Auth::user()->id){
				
				$arg['userid'] =  Auth::user()->id;
				$gallery = $UserRepostitory->createDebet($arg);
				//echo '<pre>'; print_r($gallery); exit;				
				if($gallery['code'] == 200){

					unset($gallery['code']);
					$data->error_code = 223;
					$data->data = $gallery;


				}else{
					unset($gallery['code']);
					$data->error_code = 633;
				}  
		
			}else{

				$data->error_code =  414;  //UnAuthorize user
			}

		}else{ // FOR DELETE METHOD


			$gallery = $UserRepostitory->deleteDebet($arg);
			$data->error_code = 214; 
		}
								
		//echo '<pre>'; print_r($data); exit;
		return $data;
	}


	public function debetrequestList($request){
		$data = new DataService();
		$UserRepostitory = new UserRepository();
		//$arg['id'] 	   = Auth::user()->id;
		$user_list  = $UserRepostitory->debetrequestList($request);
		if($user_list){
			$data->error_code = 280;
			$data->data = $user_list;
		
		}else{

			$data->error_code = 634;

		}
		
		
		return  $data;
	}


	public function debetacceptDecline($arg){
		$userid =  Auth::user()->id;
		$data = 	new DataService();
		$UserRepostitory = new UserRepository();
		if(isset($arg['id'])){
			$checkunique = $UserRepostitory->debetacceptDecline($arg,$userid);
			//print_r($checkunique['result']); exit;
			if($checkunique== 1){// accept
				$data->error_code = 308;
			}elseif($checkunique == 3){ // decline
				$data->error_code = 309;
			}else{ //not found
				$data->error_code = 310;
			} 
		


		}else{

			$data->error_code = 403;
		}	
		$data->data = @$checkunique;
			//print_r($data); exit;

		return $data;
	}
}



