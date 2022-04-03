<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Service\ApiService;
use App\Http\Controllers\Service\SpecialitiesService;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Msg;
use App\Http\Controllers\Repository\UserRepository;
use App\Http\Controllers\Repository\CrudRepository;
use App\User;
use App\Models\Photo;
use App\Models\Debet;
use App\Models\Categories;
use App\Models\Page;
use App\Models\SubCategories;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use App\Http\Controllers\Utility\SendEmails;
use DateTime;
use DB;
use Validator;
use Route;


//use Illuminate\Routing\Controller as BaseController;

class ApiController extends Controller
{
    
    public function register(Request $request){
            
        $data = $request->all();
        
           
        if($request->method() == 'POST'){

            
            if(isset($data['phone'])){// Register With Phone Number 
                $rules = array(  'phone'=>'required|digits:10');
                
                //$rest = new Rest('tgnGLgYDUx381oPn76SXt1OePgt6AxPH');

              //  print_r($rest->forex->realTimeCurrencyConversion->get('USD', 'EUR', 10)); exit;

                $validate = Validator::make($data,$rules);

                if($validate->fails() ){
                    
                    $validate_error = $validate->errors()->all();

                    $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

                }else{
                    $ApiService = new ApiService();
                    $Check = $ApiService->checkemail_phone($data);  
                    $error_msg = new Msg();
                    $msg =  $error_msg->responseMsg($Check->error_code);
                

                    if($Check->error_code == 203 ){
                        $response = [
                            'code' => 200,
                            'msg'=>  $msg
                        ];
                    }else{
                        $response = [
                            'code' => $Check->error_code,
                            'msg'=>  $msg
                        ];
                    }

                }
            }
           
            
            return $response;
        }   
    }
    
    /*****************************************************************************
    * API                   => verify Phone and email                            *
    * Description           => It is used  verify                                *
    * Required Parameters   => code,password,confirm_password                    *
    * Created by            => Sunil                                             *
    *****************************************************************************/
    public function verifyUser(Request $request){

        $data = $request->all();

        if($request->method() == 'POST'){

            $ApiService = new ApiService();
            $Check = $ApiService->verifyUser($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 205 ){
                $ApiService = new ApiService();
                $Check = $ApiService->login($data);
                //print_r($Check); exit;
                $response = [
                    'code' => 200,
                    'msg'=>  $msg,
                    'data' => $Check->data
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   

    }


    /*****************************************************************************
    * API                   => Social Login                                      *
    * Description           => It is used  verify                                *
    * Required Parameters   => facebook_id,google_id,apple_id                    *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function socialLogin(Request $request){
            
        $data = $request->all();
           
        if($request->method() == 'POST'){

            
            if(isset($data['facebook_id'])){// Register With facebook
                $rules = array(  'facebook_id'=>'required');
            }
            if(isset($data['google_id'])){// Register With google 
                $rules = array(  'google_id'=>'required');
            }
            if(isset($data['apple_id'])){// Register With Apple 
                $rules = array(  'apple_id'=>'required');
            }

                $validate = Validator::make($data,$rules);

                if($validate->fails() ){
                    
                    $validate_error = $validate->errors()->all();

                    $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

                }else{
                    $ApiService = new ApiService();
                    $Check = $ApiService->socialLogin($data); 
                    //print_r($Check); exit; 
                    $error_msg = new Msg();
                    $msg =  $error_msg->responseMsg($Check->error_code);
                

                    if($Check->error_code == 200 ){
                        $response = [
                            'code' => 200,
                            'msg'=>  $msg,
                            'data' => $Check->data
                        ];
                    }else{
                        $response = [
                            'code' => $Check->error_code,
                            'msg'=>  $msg
                        ];
                    }

                }
            
           
            
            return $response;
        }   
    }
    
    /*****************************************************************************
      API                   => set Password                                      *
    * Description           => It is to set the ssword                           *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    ******************************************************************************/
    public function resetPassword(Request $request){
       
        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'id'         =>  'required',
                    'password'      =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){

                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->resetPassword($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 638 || $Check->error_code == 645){

                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            
            return $response;
        }   
    }


   

    public function changePassword(Request $request){
        
        $userId= Auth::user()->id; 
        if($request->method() == 'POST'){

            $data = $request->all();
            $rules = array(
                'old_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password',
            );
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
            } else {
                try {
                    if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {

                        $arr = array("code" => 400, "msg" => "Check your old password.", "data" => array());
                    } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                        $arr = array("code" => 400, "msg" => "Your new password cannot be the same as your current password.", "data" => array());
                    } else {
                        User::where('id', $userId)->update(['password' => Hash::make($data['new_password'])]);
                        $arr = array("code" => 200, "msg" => "Password updated successfully.", "data" => array());
                    }
                } catch (\Exception $ex) {
                    if (isset($ex->errorInfo[2])) {
                        $msg = $ex->errorInfo[2];
                    } else {
                        $msg = $ex->getMessage();
                    }
                    $arr = array("code" => 404, "msg" => $msg, "data" => array());
                }
            }
            return \Response::json($arr);
        }
    }



    /************************************************************************************
    * API                   => Login                                                    *
    * Description           => It is used to login new user                             *
    * Required Parameters   => email,password,device_id,device_type                     *
    * Created by            => Sunil                                                    *
    *************************************************************************************/

    public function login(Request $request){
        $data = $request->all();

        if($request->method() == 'POST'){

            $rules = array(
                    'password'      =>  'required | min:8',
                    'device_id'     =>  'required',
                    'device_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->login($data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 200){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    public function registeremail(Request $request){
            
    	$data = $request->all();
    	   
       
    	if($request->method() == 'POST'){

            $rules = array('email' =>'required|email|max:255|unique:users','password'=>'required | min:8');
            

            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                
                $validate_error = $validate->errors()->all();

                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{
                
                $ApiService = new ApiService();
                $Check = $ApiService->checkemail_phone($data);  
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            

                if($Check->error_code == 203 ){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }

            }
    		
    		return $response;
    	}	
    }


    /***********************************************************************************
    * API                   => ''                                                      *
    * Description           => It is used  verify  the email..                         *
    * Required Parameters   => ''                                                      *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function activation(Request $request){
            //print_r($request->all());die;
            $id = $request->id;
            $code = $request->code;   

            $UserRepostitory   = new UserRepository();
            $getuser = $UserRepostitory->getuserById($id);
            //echo '<pre>'; print_r($getuser); exit;
            if($getuser['id'] == 1){
                $getCode = $getuser['forgot_password_code'];
            }else{
                $getCode = $getuser['activation_code'];
            }
            $endTime = strtotime("+5 minutes",strtotime($getCode));
            $newTime = date('H:i:s',$endTime);
            if($getCode == $request->code){
                $user = $UserRepostitory->update_activation($id);
                if($getuser['id'] == 1){
                    return view('admin/users/reset');
                }else{
                    return view('activations');

                } 
            }else{
                
                return view('activationsfail');
            }   
        }


    /******************************************************************************
    * API                   => ''                                                 *
    * Description           => It is used  verify  the email..                    *
    * Required Parameters   => ''                                                 *
    * Created by            => Sunil                                              *
    *******************************************************************************/

    public function terms(Request $request){
           $result = DB::table('pages')->where('p_status','=',1)->where('id','=',1)->first();
           print_r($result->p_description);
    }   

    public function privacypolicy(Request $request){
           $result = DB::table('pages')->where('p_status','=',1)->where('id','=',2)->first();
           print_r($result->p_description);

    }   

    
    public function aboutus(Request $request){
        echo 'About Us';
    }    
      
  

    /*************************************************************************************
    * API                   => Forgot Password                                           *
    * Description           => It is used send forgot password mail..                    *
    * Required Parameters   => email                                                     *
    * Created by            => Sunil                                                     *
    *************************************************************************************/

    public function forgotPassword(Request $request){
        $data = $request->all();
        if($request->method() == 'POST'){
        
            $ApiService = new ApiService();
            $Check = $ApiService->forgotPassword($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 601){
                $response = [
                    'code' => 200,
                    'msg'=>  $msg
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /********************************************************************************
    * API                   => Category list                                        *
    * Description           => It is to get Chip list                               *
    * Required Parameters   => Access Token                                         *
    * Created by            => Sunil                                                *
    *********************************************************************************/

    public function category_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->category_list();
            

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
             
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                   
                ];
                $category_array = array();
                $category_list = array();
                foreach($responseOld['data']['data'] as $list){
                    //  print_r($list);
                    $category_array['c_id']  =  @$list['c_id'] ? $list['c_id'] : '';
                    $category_array['c_name'] = @$list['c_name'] ? $list['c_name'] : '';
                    $category_array['c_status'] =  @$list['c_status'] ? $list['c_status'] : '';
                    $category_array['c_image'] =  @$list['c_image'] ? URL('/public/images/'.$list['c_image']) : '';
                    
                    array_push($category_list,$category_array);
                }
                //echo '<pre>'; print_r($responseOld['gender']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $category_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

     /********************************************************************************
      API                   => Category list                                        *
    * Description           => It is to get Chip list                               *
    * Required Parameters   => Access Token                                         *
    * Created by            => Sunil                                                *
    *********************************************************************************/

    public function group_category_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->category_list();
            

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
             
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                   
                ];
                $category_array = array();
                $category_list = array();
                 $category_list['category_list'] = array();
                foreach($responseOld['data']['data'] as $list){
                    //  print_r($list);
                    $category_array['c_id']  =  @$list['c_id'] ? $list['c_id'] : '';
                    $category_array['c_name'] = @$list['c_name'] ? $list['c_name'] : '';
                    $category_array['c_status'] =  @$list['c_status'] ? $list['c_status'] : '';
                    $category_array['c_image'] =  @$list['c_image'] ? URL('/public/images/'.$list['c_image']) : '';
                    
                    array_push($category_list['category_list'],$category_array);
                }
                //echo '<pre>'; print_r($responseOld['gender']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $category_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /**********************************************************************************
      API                   => Get and update Profile                                 *
    * Description           => It is user for Profile                                 *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function profile(Request $request){
        if(@$request['userid'] != ''){
            $userId = $request['userid'];
        }else{
            $userId= Auth::user()->id;
        }
        //
        $Is_method  = 0; 
        
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $userId;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        if($request->method() == 'POST'){

            $data = $request->all();
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }
        }      
        return $response;
    }


     /***********************************************************************************
    * API                   => Home Page Post list                                     *
    * Description           => It is to get Post list                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function other_profile(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->post_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                $userId= $request->userid;

                $myprofile = $ApiService->profile(1,$userId);
                //echo '<pre>'; print_r($myprofile->data['designation']); exit;
                $follower_count  = $UserRepostitory->follower_count($userId);
                $following_count  = $UserRepostitory->following_count($userId);
                $check_is_follow  = $UserRepostitory->check_is_follow($userId);
                $post_count  = $UserRepostitory->post_count($userId);



                //print_r($myprofile); exit;
                $Partner_list['id'] = $myprofile->data['id'];
                $Partner_list['username'] = $myprofile->data['username'];
                $Partner_list['first_name']  = $myprofile->data['first_name']?$myprofile->data['first_name']:'';
                $Partner_list['last_name']   = $myprofile->data['last_name']?$myprofile->data['last_name']:'';
                $Partner_list['is_verified'] = 0;// $myprofile->data['is_verified'];
                $Partner_list['photo'] = $myprofile->data['photo'];
                $Partner_list['bio'] = $myprofile->data['bio'];
                $Partner_list['pollitical_orientation'] = $myprofile->data['pollitical_orientation'];
                $Partner_list['followers_count'] = @$follower_count;
                $Partner_list['followings_count'] = @$following_count;
                $Partner_list['is_follow'] = $check_is_follow;
                $Partner_list['post_count'] = @$post_count;
                $Partner_list['post'] = array();
                $Partner_list['followers'] = array();
                $Partner_list['photoList'] = array();
                //$Partner_list['reelsList'] =array();
                foreach($responseOld['data']['data'] as $list){
                    //echo '<pre>'; print_r($list['id']); exit;
                    $partner_array = array();
                    $repost  = array();
                    $postid =  $list['id'];
                    $like_count  = 0;// $UserRepostitory->like_count($postid);
                    $favourite_count  = 0; // $UserRepostitory->favourite_count($postid);
                    $comment_count  = 0 ;//$UserRepostitory->comment_count($postid);
                    $repost_count  = 0; // $UserRepostitory->repost_count($postid);  
                    $is_my_like = 0; //$UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = 0; //$UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                        
                    $like_count  = $UserRepostitory->like_count($postid);
                    $favourite_count  =  $UserRepostitory->favourite_count($postid);
                   
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    //  $repost_count  = $this->repost_count($data);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    
                    $partner_array['id']    =   @$list['p_id'] ? $list['p_id'] : '';
                    $partner_array['userid']   =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['photo']  =   @$list['photo'] ? $list['photo'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                    $partner_array['post_data'] = array();

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

                    $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : '';
                    $partner_array['post_data']['like_count']  =   $like_count;

                    $partner_array['post_data']['my_like_on'] = $is_my_like;
                    
                    $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                    
                     
                    $partner_array['post_data']['favourite_count'] = $favourite_count;
                    $partner_array['post_data']['comment_count']  =   @$comment_count;

                
                    $partner_array['post_data']['posted_time']  =   @$list['created_at'] ? $list['created_at'] : '';

                    
                    array_push($Partner_list['post'],$partner_array);
                }
                
               /* $Checkfollower = $ApiService->follower_list($request);
                if($Checkfollower->error_code == 641){
                    $data = $Checkfollower->data;     
                    $FollowerresponseOld = [
                        'data'  => $data->toArray(),
                    ];
                    //echo  '<pre>'; print_r($FollowerresponseOld); exit;
                    $follower_list = array();
                    foreach($FollowerresponseOld['data']['data']  as $list){
                        //$FollowerresponseOld = array();
                        //echo '<pre>';print_r($list); exit;
                        $follower_array['userid'] =  @$list['userid'] ? $list['userid'] : 0;
                        $follower_array['first_name'] =  @$list['first_name'] ? $list['first_name'] : '';
                        $follower_array['picUrl'] =  @$list['picUrl'] ? $list['picUrl'] : '';
                        $follower_array['is_verified'] =  @$list['is_verified'] ? $list['is_verified'] : 0;
                        $follower_array['user_type'] =  @$list['user_type'] ? $list['user_type'] : 0;
                        $follower_array['designation'] =  @$list['designation'] ? $list['designation'] : '';
                        $follower_array['id'] =  @$list['id'] ? $list['id'] : 0;
                        $follower_array['user_id'] =  @$list['user_id'] ? $list['user_id'] : 0;
                        $check_is_follow  =  $UserRepostitory->check_is_follow($list['userid']);
                        $follower_array['is_follow'] = $check_is_follow;
                        $follower_array['follow_by'] =  @$list['follow_by'] ? $list['follow_by'] : 0;
                      
                        
                        array_push($follower_list,$follower_array);
                        $Partner_list['followers'] = $follower_list;
                    }
                }*/

                //Status
                $getphotolist = Photo::where('p_type','1')
                ->where('p_u_id','=',$userId)
                ->groupBy('p_u_id')
                //->leftjoin('photos','users.id','photos.p_u_id')
                ->get(); 
               /*  $getphotolist = Photo::select('users.id as userid','users.first_name as first_name','p.p_id as photo_id','p.p_photo as photo_name')
                ->where('p.p_type','1')
                ->orwhere('p.p_type','3')
                ->leftjoin('photos as p','users.id','p.p_u_id')
                ->get();*/

                $PhotoData = array();
                $PhotoArr = array();
                $userData['photoList'] = array();   
                foreach($getphotolist as $key => $list){
                    //print_r($list); exit;
                    if(!empty($list)){
                         $statussuser = User::where('id',$list->p_u_id)
                        ->first(); 
                       
                        $PhotoData['name']       =  $statussuser['first_name'];
                        //$PhotoData['name']      =  'xyz';//@$list->p_id ? $list->p_id  : '';
                        $PhotoData['photo_id']  =  @$list->p_id ? $list->p_id  : '';
                        $PhotoData['photo']     =  @$list->p_photo ? $list->p_photo  : '';
                    }
                    array_push($PhotoArr,$PhotoData);
                    $Partner_list['photoList'] = $PhotoArr;
                } 

                $getreellist = Photo::where('p_type','2')
                ->where('p_u_id','=',$userId)
                ->get(); 
                $ReelData = array();
                $ReelArr = array();
                $userData['reelsList'] = array(); 
                $UserRepostitory = new UserRepository();  
                foreach($getreellist as $rkey => $rlist){
                    //  print_r($rlist); exit;
                    if(!empty($rlist)){
                        $reelsuser = User::where('id',$rlist->p_u_id)
                        ->first(); 
                       
                        $ReelData['name']       =  $reelsuser['first_name'];
                        $checkunique = $UserRepostitory->get_view_count($rlist->p_id);
                        $ReelData['view_count']  = $checkunique;
                        $ReelData['photo_id']   =  @$rlist->p_id ? $rlist->p_id  : '';
                        $ReelData['photo']      =  @$rlist->p_photo ? $rlist->p_photo  : '';
                    }
                    array_push($ReelArr,$ReelData);
                    //$Partner_list['reelsList'] = $ReelArr;
                } 


                $ForumData = array();
                $ForumArr = array();
                $userData['forumsList'] = array();   
                $Partner_list['forumsList'] = $ForumArr;
                $Partner_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***********************************************************************************
    * API                   => Home Page acivity_list                                  *
    * Description           => It is to get acivity_list                               *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function acivity_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->acivity_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['post'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $repost  = array();
                    $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                    
                    $like_count  = $UserRepostitory->like_count($postid);
                    $favourite_count  = $UserRepostitory->favourite_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 
                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }
                    if($list['repost_id'] != ''){
                        $repost = DB::table('posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
                        ->where('posts.id','=',$list['repost_id'])
                        ->leftjoin('users','posts.u_id','users.id')
                        ->first();
                        $list['repost_id'] = '';

                        //echo $repost->id; exit;
                        //print_r($repost); exit;
                        $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                        $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                        
                        $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                        $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                        $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                        $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                        $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                        $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                        $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        
                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                        
                        $partner_array['post_data']['is_reposted']  =  true;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        
                        
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                        $partner_array['post_data']['retweet_count']  =   $repost_count ;
                        $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                        $partner_array['post_data']['stock_name']  =   @$repost->stock_name ? $repost->stock_name : '';
                        $partner_array['post_data']['stock_target_price']  =   @$repost->stock_target_price ? $repost->stock_target_price : '';
                        $partner_array['post_data']['time_left']  =   @$repost->time_left ? $repost->time_left : '';
                        $partner_array['post_data']['term']  =   @$repost->term ? $repost->term : '';
                        $partner_array['post_data']['result']  =   @$repost->result ? $repost->result : '';
                        $partner_array['post_data']['trend']   =  @$repost->trend ? $repost->trend : 0;
                        $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($repost->poll_one)){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                        }
                        if(!empty($repost->poll_two)){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                        }
                        if(!empty($repost->poll_three)){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                        }
                        if(!empty($repost->poll_four)){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            
                        }
                        

                    }else{
                        $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                        $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;

                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                        $partner_array['post_data']['is_reposted']  =  false;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

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

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($list['poll_one'])){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                        }
                        if(!empty($list['poll_two'])){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                        }
                        if(!empty($list['poll_three'])){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                        }
                        if(!empty($list['poll_four'])){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            
                        }
                    }
                    array_push($Partner_list['post'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }
    /************************************************************************************
    * API                   => Update Device                                            *
    * Description           => It is user for email                                     *
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function update_device(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'POST'){
            $data = $request;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->update_device($Is_method,$data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        return $response;
    }


    /*****************************************************************************
    * API                   => create Post                                       *
    * Description           => It is Use to  create Post                         *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function createPost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createPost(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

    public function deletePost(Request $request)
    {
        if($request->method() == 'DELETE'){
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->deletePost($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 302){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            }    
        }   
    }
   

    /*****************************************************************************
    * API                   => Commpent Post                                     *
    * Description           => It is Use to  Comment Post                        *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function commentPost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->commentPost(2, $data);
                
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                
                if($Check->error_code == 218){
                    // $Check = $ApiService->post_detail(1,$data['post_id']);
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }



    /********************************************************************************
      API                   => Category list                                        *
    * Description           => It is to get Chip list                               *
    * Required Parameters   => Access Token                                         *
    * Created by            => Sunil                                                *
    *********************************************************************************/

    public function report_text_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->report_text_list();
            

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
             
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                   
                ];
                $category_array = array();
                $category_list = array();
                foreach($responseOld['data']['data'] as $list){
                    //  print_r($list);
                    $category_array['id']  =  @$list['id'] ? $list['id'] : '';
                    $category_array['report'] = @$list['report'] ? $list['report'] : '';
                    
                    array_push($category_list,$category_array);
                }
                //echo '<pre>'; print_r($responseOld['gender']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Report List',
                    'data'  =>  $category_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /*****************************************************************************
    * API                   => RE Post                                           *
    * Description           => It is Use to  Re Post                             *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function repost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->repost(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    public function contact(Request $request){
        if($request->method() == 'POST'){
            $data = $request->all();
            //print_r($data); exit;
            $email = @$data['email'];
            //$phone = @$data['phone'];
            $subject = 'User Query';    //@$data['subject'];
            $msg = @$data['messsage'];
            $name = @$data['name'];
            $to = 'socialtrade@mailinator.com';
            $SendEmail = new SendEmails();
           // $SendEmail->sendContact($to,$email,$subject,$name,$msg);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg(648);
            $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
                return $response;
        }
    }


    /***********************************************************************************
    * API                   => Create Report                                           *
    * Description           => It is used for creating the report                      * 
    * Required Parameters   =>                                                         *
    * Created by            => Sunil                                                   *
    ************************************************************************************/
    
    public function report(Request $request){
        if($request->method() == 'POST'){
            $data = $request->all();
            $rules = array('post_id' => 'required', 'desc' => 'required');
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->report($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($msg); exit;
                if($Check->error_code == 222){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        //'data'  =>  $Check->data  
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    

            return $response;
        }   
    }



    /*****************************************************************************
      API                   => photo_list                                        *
    * Description           => It is to get photo_list                           *
    * Required Parameters   => Access Token                                      *
    * Created by            => Sunil                                             *
    *****************************************************************************/

    public function photo_list(Request $request){
       
        if($request->method() == 'GET'){
            $data = $request->all();
            $UserRepostitory = new UserRepository();
            
            $ApiService = new ApiService();
            $Check = $ApiService->photo_list($data);
           
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                ];
                //echo '<pre>'; print_r($responseOld); exit;


                $Photo_list['photo'] = array();
                foreach ($responseOld['data']['data']  as $photoDatakey => $photoDatavalue) {
                    //echo '<pre>'; print_r($photoDatavalue); exit;
                    $photo_array = array();
                    $photo_array['post_id']  =  @$photoDatavalue['post_id'] ? $photoDatavalue['post_id']:0;
                    $photo_array['photo_id']  =  @$photoDatavalue['p_id'] ? $photoDatavalue['p_id'] : 0;
                    $photo_array['imgUrl']  =  @$photoDatavalue['p_photo'] ? $photoDatavalue['p_photo'] : '';
                    array_push($Photo_list['photo'],$photo_array);
                }
                    $Photo_list['paging']['current_page'] = $responseOld['data']['current_page'];
                    $Photo_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                    $Photo_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                    $Photo_list['paging']['last_page'] = $responseOld['data']['last_page'];
                    $Photo_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                    $Photo_list['paging']['per_page'] = $responseOld['data']['per_page'];
                    $Photo_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                    $Photo_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
               
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Photo List',
                    'data'  =>  $Photo_list
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /*****************************************************************************
      API                   => reel_list                                         *
    * Description           => It is to get reel_list                            *
    * Required Parameters   => Access Token                                      *
    * Created by            => Sunil                                             *
    *****************************************************************************/

    public function reel_list(Request $request){
       
        if($request->method() == 'GET'){
            $data = $request->all();
            $UserRepostitory = new UserRepository();
            
            $ApiService = new ApiService();
            $Check = $ApiService->reel_list($data);
           
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                ];
                //echo '<pre>'; print_r($responseOld); exit;


                $Reel_list['reel'] = array();
                foreach ($responseOld['data']['data']  as $photoDatakey => $photoDatavalue) {
                    //echo '<pre>'; print_r($photoDatavalue); exit;
                    $reel_array = array();
                    $statussuser = User::where('id',$photoDatavalue['p_u_id'])
                        ->first(); 
                    $reel_array['user']  =  $statussuser['first_name'];
                    $reel_array['photo']  =  $statussuser['photo'];
                    $reel_array['post_id']  =  @$photoDatavalue['post_id'] ? $photoDatavalue['post_id']:0;
                    $checkunique = $UserRepostitory->get_view_count($photoDatavalue['p_id']);
                    $reel_array['view_count']  = $checkunique;
                    $reel_array['photo_id']  =  @$photoDatavalue['p_id'] ? $photoDatavalue['p_id'] : 0;
                    $reel_array['imgUrl']  =  @$photoDatavalue['p_photo'] ? $photoDatavalue['p_photo'] : '';
                    array_push($Reel_list['reel'],$reel_array);
                }
                    $Reel_list['paging']['current_page'] = $responseOld['data']['current_page'];
                    $Reel_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                    $Reel_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                    $Reel_list['paging']['last_page'] = $responseOld['data']['last_page'];
                    $Reel_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                    $Reel_list['paging']['per_page'] = $responseOld['data']['per_page'];
                    $Photo_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                    $Reel_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
               
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Reel List',
                    'data'  =>  $Reel_list
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }




    /*****************************************************************************
      API                   => reel_detail                                         *
    * Description           => It is to get reel_detail                            *
    * Required Parameters   => Access Token                                      *
    * Created by            => Sunil                                             *
    *****************************************************************************/

    public function reel_detail(Request $request){
       
        if($request->method() == 'POST'){

            $data = $request->all();
            $rules = array('reel_id'=>'required');
            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                    
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{

                $UserRepostitory = new UserRepository();      
                $ApiService = new ApiService();
                    
                $Check = $ApiService->reel_detail($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                
                
                if($Check->error_code == 641){

                    $data = $Check->data;   


                    $like_count  = $UserRepostitory->like_count($data['post_id']); 
                    $comment_count  = $UserRepostitory->comment_count($data['post_id']);

                    $result = array();
                    $result['photo_id']       = $data['p_id'];
                    $result['user_id']        = $data['p_u_id'];
                    $result['photo']          = $data['p_photo'];
                    $result['photo_type']     = $data['p_type'];
                    $result['user_photo']     = $data['photo'];
                    $result['user_description'] = $data['about'];
                    $result['comment_count']  = $comment_count;
                    $result['like_count']     = $like_count;

                    $reelData = $this->filiterArray($result);  

                    
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  'Reel  Details',
                        'data'  =>  $reelData,
                    ];       
                   
              
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }




    /*****************************************************************************
      API                   => user_status                                         *
    * Description           => It is to get user_status                            *
    * Required Parameters   => Access Token                                      *
    * Created by            => Sunil                                             *
    *****************************************************************************/

    public function user_status(Request $request){
       
        if($request->method() == 'POST'){

            $data = $request->all();
            $rules = array('user_id' => 'required');
            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                    
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{

                $UserRepostitory = new UserRepository();      
                $ApiService = new ApiService();
                    
                $Check = $ApiService->user_status($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                

                if($Check->error_code == 641){

                    $data = $Check->data;   

                    $result = array();
                    $resultArray = array();

                    foreach($data as $value){

                        $viewCount = $UserRepostitory->get_view_count($value['post_id']);
                      
                        $result['post_id']       = $value['p_id'];
                        $result['user_id']        = $value['p_u_id'];
                        $result['post_photo']     = $value['p_photo'];
                        $result['photo_type']     = $value['p_type'];
                        $result['user_photo']     = $value['photo'];
                        $result['user_name']      = $value['first_name'] .$value['last_name'] ;
                        $result['view_count']     = $viewCount;
                        
                        $statusData = $this->filiterArray($result);  

                        array_push($resultArray,$statusData);
                    }

                   
                    
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  'User Status',
                        'data'  =>  $resultArray,
                    ];       
                   
              
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

     /****************************************************************************
      API                   => status_list                                       *
    * Description           => It is to get status_list                          *
    * Required Parameters   => Access Token                                      *
    * Created by            => Sunil                                             *
    *****************************************************************************/

    public function status_list(Request $request){
       
        if($request->method() == 'GET'){
            $data = $request->all();
            $UserRepostitory = new UserRepository();
            
            $ApiService = new ApiService();
            $Check = $ApiService->status_list($data);
           
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                ];
                //echo '<pre>'; print_r($responseOld); exit;

                $Status_list['status'] = array();
                foreach ($responseOld['data']['data']  as $photoDatakey => $photoDatavalue) {
                    //echo '<pre>'; print_r($photoDatavalue); exit;
                    $status_array = array();
                    $statussuser = User::where('id',$photoDatavalue['p_u_id'])
                        ->first(); 
                    $status_array['user']  =  $statussuser['first_name'];
                    $status_array['photo']  =  $statussuser['photo'];
                    $status_array['post_id']  =  @$photoDatavalue['post_id'] ? $photoDatavalue['post_id']:0;
                    $checkunique = $UserRepostitory->get_view_count($photoDatavalue['p_id']);
                    $status_array['view_count']  = $checkunique;
                    $status_array['photo_id']  =  @$photoDatavalue['p_id'] ? $photoDatavalue['p_id'] : 0;
                    $status_array['imgUrl']  =  @$photoDatavalue['p_photo'] ? $photoDatavalue['p_photo'] : '';
                    array_push($Status_list['status'],$status_array);
               
                }
                $Status_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $Status_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $Status_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Status_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $Status_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $Status_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $Status_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Status_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'Status List',
                    'data'  =>  $Status_list
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    public function status_view(Request $request){
       
        if($request->method() == 'POST'){

            $data = $request->all();
            $rules = array('sender_id' => 'required','receiver_id' => 'required','post_id' => 'required');
            $validate = Validator::make($data,$rules);

            if($validate->fails() ){
                    
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=> $validate_error[0] ]; 

            }else{

                $UserRepostitory = new UserRepository();      
                $ApiService = new ApiService();
                    
                $Check = $ApiService->status_view($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                

                if($Check->error_code == 641){

                    $data = $Check->data;   

                    $result = array();
                    $resultArray = array();

                    foreach($data as $value){

                        $result['post_id']       = $value['p_id'];
                        $result['user_id']        = $value['p_u_id'];
                        $result['user_photo']     = $value['photo'];
                        $result['user_name']      = $value['first_name'] .$value['last_name'] ;
                        
                        $statusData = $this->filiterArray($result);  

                        array_push($resultArray,$statusData);
                    }

                   
                    
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  'User View Status',
                        'data'  =>  $resultArray,
                    ];       
                   
              
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

   
    /***************************************************************************************
      API                   => Get and update Profile                                     *
    * Description           => It is user for Profile                                     *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function user_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
            //$data = $request->id;
            $data = $request['userid'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->user_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

     /***********************************************************************************
    * API                   => Home Page popular_list list                                    *
    * Description           => It is to get Group list                                 *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function popular_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->mygroup_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 437){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['group'] = array();
                //echo '<pre>'; print_r($responseOld['data']['data']); exit;
                foreach($responseOld['data']['data'] as $list){
                    //$partner_array = array();
                    
                    $member_count  = $UserRepostitory->member_count($list['g_id']);
                  /*  $like_count  = $UserRepostitory->like_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 

                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }*/
                    
                    $partner_array['g_id']            =   @$list['g_id'] ? $list['g_id'] : '';
                    $partner_array['g_type']   =   @$list['g_type'] ? $list['g_type'] :''; 
                    $partner_array['g_tags']   =   @$list['g_tags'] ? $list['g_tags'] :''; 
                    $partner_array['userid']        =   @$list['gm_u_id'] ? $list['gm_u_id'] : '';
                    $partner_array['g_photo']  =   @$list['g_photo'] ? $list['g_photo'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['g_title']  =   @$list['g_title'] ? $list['g_title'] : '';
                    $partner_array['g_desc']  =   @$list['g_desc'] ? $list['g_desc'] : '';
                    $partner_array['is_free']  =   @$list['is_free'] ? $list['is_free'] : '';
                    $partner_array['member_count']  =   $member_count;
                    $partner_array['post_count']  =   0;
                    array_push($Partner_list['group'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***********************************************************************************
    * API                   => Home Page Group list                                    *
    * Description           => It is to get Group list                                 *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function mygroup_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->mygroup_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 437){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['group'] = array();
                //echo '<pre>'; print_r($responseOld['data']['data']); exit;
                foreach($responseOld['data']['data'] as $list){
                    //$partner_array = array();
                    
                    $member_count  = $UserRepostitory->member_count($list['g_id']);
                  /*  $like_count  = $UserRepostitory->like_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 

                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }*/
                    
                    $partner_array['g_id']            =   @$list['g_id'] ? $list['g_id'] : '';
                    $partner_array['g_type']   =   @$list['g_type'] ? $list['g_type'] :''; 
                    $partner_array['g_tags']   =   @$list['g_tags'] ? $list['g_tags'] :''; 
                    $partner_array['userid']        =   @$list['gm_u_id'] ? $list['gm_u_id'] : '';
                    $partner_array['g_photo']  =   @$list['g_photo'] ? $list['g_photo'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['g_title']  =   @$list['g_title'] ? $list['g_title'] : '';
                    $partner_array['g_desc']  =   @$list['g_desc'] ? $list['g_desc'] : '';
                    $partner_array['is_free']  =   @$list['is_free'] ? $list['is_free'] : '';
                    $partner_array['member_count']  =   $member_count;
                    $partner_array['post_count']  =   0;
                    array_push($Partner_list['group'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /*****************************************************************************
    * API                   => create Group                                      *
    * Description           => It is Use to  create Group                        *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function createGroup(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

                    //'g_title'   => 'required|unique:groups,g_title',
            $rules = array(
                    'g_title'   => 'required',
                    'g_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createGroup(2, $data);
                
                    //print_r($Check->data['data']); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 223){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data['data']
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /*****************************************************************************
    * API                   => addMember                                         *
    * Description           => It is Use to  addMember                           *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function addMember(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'g_title'   =>  'required|unique:groups,g_title',
                    'g_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createGroup(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


     /*****************************************************************************
    * API                   => joinGroup                                         *
    * Description           => It is Use to  joinGroup                           *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function joinGroup(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'g_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->joinGroup(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 228){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        //'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /**********************************************************************************
      API                   => Get  Group detail                                      *
    * Description           => It is user for Group detail                            *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function group_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['g_id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->group_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 224){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }


    // Group Post section ------/////////////////////////
     /*****************************************************************************
    * API                   => Group create Post                                 *
    * Description           => It is Use to  create Post                         *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function groupcreatePost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_type'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupcreatePost(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /********************************************************************************
    * API                   => Forum Topic list                                     *
    * Description           => It is to get Forum Topic list                        *
    * Required Parameters   => Access Token                                         *
    * Created by            => Sunil                                                *
    *********************************************************************************/

    public function forum_topic_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->forum_topic_list();
            

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
             
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray(),
                   
                ];
                $category_array = array();
                $category_list['category'] = array();
                foreach($responseOld['data']['data'] as $list){
                    //  print_r($list);
                    $category_array['t_id']  =  @$list['t_id'] ? $list['t_id'] : '';
                    $category_array['t_name'] = @$list['t_name'] ? $list['t_name'] : '';
                    
                    array_push($category_list['category'],$category_array);
                }
                $category_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $category_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $category_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $category_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $category_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $category_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $category_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $category_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['gender']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  'ForumTopic List',
                    'data'  =>  $category_list,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }
    
    /*****************************************************************************
    * API                   => create Forum                                      *
    * Description           => It is Use to  create Forum                        *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function createForum(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            //'g_title'   => 'required|unique:groups,g_title',
            $rules = array(
                    'title'   => 'required|unique:forums,title',
                    'photo'   => 'required',
                    'topic_id'   => 'required',

                );

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createForum(2, $data);
                
                    //print_r($Check->data['data']); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 223){
                    $response = [
                        'code' => 200,
                        'msg'=>  'Forum Created'
                        //'data' => $Check->data['data']
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }




    /***********************************************************************************
    * API                   => Home Page Post list                                     *
    * Description           => It is to get Post list                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function forum_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->forum_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 437){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['forum'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $postid = $list['id'];
                    $like_count  = $UserRepostitory->forum_like_count($postid);
                    $user_plus_like_count  = $UserRepostitory->forum_user_plus_like_count($postid);
                    //echo '<pre>'; print_r($user_plus_like_count); 
                    $is_my_like = $UserRepostitory->forum_my_like_count($postid,Auth::user()->id);      
                    $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                    $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : 0;
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                    $partner_array['topic_id']            =   @$list['topic_id'] ? $list['topic_id'] : '';
                  
                    $partner_array['post_data'] = array();
                    $partner_array['post_data']['photo']  =   @$list['photo'] ? $list['photo'] : '';
                    $partner_array['post_data']['title']  =   @$list['title'] ? $list['title'] :'';
                    $partner_array['post_data']['detail']  =   @$list['detail'] ? $list['detail'] : '';
                    $partner_array['post_data']['topic_id']  =   @$list['topic_id'] ? $list['topic_id'] : 0;
                    $partner_array['post_data']['created_at']  =   @$list['created_at'] ? $list['created_at'] : '';
                    $partner_array['post_data']['like_count']  =   $like_count;
                    $partner_array['post_data']['view_count']  =  0;
                    $partner_array['post_data']['user_plus_like_count']  =  $user_plus_like_count;
                    

                    $partner_array['post_data']['is_liked'] = $is_my_like;
                    
           

                    array_push($Partner_list['forum'],$partner_array);
                }
                $Partner_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  "Forum List",
                    'data'  =>  $Partner_list,
                   
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => Create Like forum                                        *
    * Description           => It is used for liked the fourm                           * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function forum_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->forum_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }



    /**********************************************************************************
      API                   => Get  Forum detail                                      *
    * Description           => It is user for Forum detail                            *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function forum_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['post_id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->forum_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 213    ){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    /*****************************************************************************
    * API                   => Forum Commpent Post                               *
    * Description           => It is Use to  Forum Comment Post                  *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function forum_commentPost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->forum_commentPost(2, $data);
                
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                
                if($Check->error_code == 218){
                    // $Check = $ApiService->post_detail(1,$data['post_id']);
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

    /************************************************************************************
    * API                   => Create Like comment                                      *
    * Description           => It is used for liked the comment                         * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function forum_comment_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('c_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->forum_comment_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }






    ///////////////////////Vote Them Out///////////////////////////////
    /*****************************************************************************
    * API                   => create_vote_them                                  *
    * Description           => It is Use to  create_vote_them                    *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function create_vote_them(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            //'g_title'   => 'required|unique:groups,g_title',
            $rules = array(
                    'name'   => 'required',
                    'party_affiliation'   => 'required',
                    

                );

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->create_vote_them(2, $data);
                
                    //print_r($Check->data['data']); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 223){
                    $response = [
                        'code' => 200,
                        'msg'=>  'Vote Them out Created'
                        //'data' => $Check->data['data']
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /***********************************************************************************
    * API                   => Home Page Post list                                     *
    * Description           => It is to get Post list                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function vote_them_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->vote_them_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 437){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['vote_them_out'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $postid = $list['id'];
                    $like_count  = $UserRepostitory->vote_them_like_count($postid);
                    $user_plus_like_count  = $UserRepostitory->vote_them_user_plus_like_count($postid);
                    //echo '<pre>'; print_r($user_plus_like_count); 
                    $is_my_like = $UserRepostitory->vote_them_my_like_count($postid,Auth::user()->id);      
                    $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                    $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : 0;
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                    $partner_array['topic_id']            =   @$list['topic_id'] ? $list['topic_id'] : '';
                  
                    $partner_array['post_data'] = array();
                    $partner_array['post_data']['photo']  =   @$list['photo'] ? $list['photo'] : '';

                    $partner_array['post_data']['id']   =   @$list['id'] ? $list['id'] : '';
                    $partner_array['post_data']['name']   =   @$list['name'] ? $list['name'] : '';
                    $partner_array['post_data']['party_affiliation']   =   @$list['party_affiliation'] ? $list['party_affiliation'] : '';
                    $partner_array['post_data']['state']   =   @$list['state'] ? $list['state'] : '';
                    $partner_array['post_data']['district']   =   @$list['district'] ? $list['district'] : '';
                    $partner_array['post_data']['senate_or_house']   =   @$list['senate_or_house'] ? $list['senate_or_house'] : '';
                    $partner_array['post_data']['day_of_vote']   =   @$list['day_of_vote'] ? $list['day_of_vote'] : '';
                    $partner_array['post_data']['vote_description']   =   @$list['vote_description'] ? $list['vote_description'] : '';


                    $partner_array['post_data']['title']  =   @$list['title'] ? $list['title'] : 0;
                    $partner_array['post_data']['like_count']  =   $like_count;
                    $partner_array['post_data']['view_count']  =  0;
                    $partner_array['post_data']['last_activity']  =  '';
                    $partner_array['post_data']['user_plus_like_count']  =  $user_plus_like_count;
                    

                    $partner_array['post_data']['is_liked'] = $is_my_like;
                    
           

                    array_push($Partner_list['vote_them_out'],$partner_array);
                }
                $Partner_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  "Forum List",
                    'data'  =>  $Partner_list,
                   
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => Create Like forum                                        *
    * Description           => It is used for liked the fourm                           * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function vote_them_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->vote_them_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }



    /**********************************************************************************
      API                   => Get  vote_them detail                                  *
    * Description           => It is user for vote_them detail                        *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function vote_them_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['post_id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->vote_them_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 213    ){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    /*****************************************************************************
    * API                   => vote_them_ Commpent Post                          *
    * Description           => It is Use to  vote_them_ Comment Post             *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function vote_them_commentPost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->vote_them_commentPost(2, $data);
                
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                
                if($Check->error_code == 218){
                    // $Check = $ApiService->post_detail(1,$data['post_id']);
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

    /************************************************************************************
    * API                   => Create Like comment                                      *
    * Description           => It is used for liked the comment                         * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function vote_them_comment_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('c_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->vote_them_comment_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }








    /***********************************************************************************
    * API                   => Home Page Group list                                    *
    * Description           => It is to get Group list                                 *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function roomList(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->roomList($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 437){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                $Partner_list['room'] = array();
                //echo '<pre>'; print_r($responseOld['data']['data']); exit;
                foreach($responseOld['data']['data'] as $list){
                    //$partner_array = array();
                    
                    
                    $partner_array['r_id']            =   @$list['r_id'] ? $list['r_id'] : '';
                    $partner_array['userid']        =   @$list['rm_u_id'] ? $list['rm_u_id'] : '';
                    $partner_array['g_photo']  =   @$list['r_photo'] ? $list['r_photo'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['r_title']  =   @$list['r_title'] ? $list['r_title'] : '';
                    $partner_array['r_private']  =   @$list['r_private'] ? $list['r_private'] : '';
                    $partner_array['r_desc']  =   @$list['r_desc'] ? $list['r_desc'] : '';
                    $partner_array['member_count']  =   0;
                    $lastmsg = DB::table('room_msgs')->select('room_msgs.*')
                        ->where('room_msgs.rm_id','=',$list['r_id'])
                        ->orderBy('room_msgs.rm_id','DESC')
                        ->first();
                    //echo '<pre>'; print_r($lastmsg).'<br>';   
                    if(!empty($lastmsg->rm_id)){    
                        //if($lastmsg->message_type == 1){
                            $partner_array['last_activity']  =   $lastmsg->text;
                        //}/*else if($lastmsg->message_type == 2){
                          //  $partner_array['last_activity']  =  "📷 IMAGE";
                       // }else{
                           // $partner_array['last_activity']  =  "📷 GIF";

                        //}*/
                    }


                    array_push($Partner_list['room'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /*****************************************************************************
    * API                   => CgroupChat                                        *
    * Description           => It is Use to  groupChat                           *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function groupChat(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'g_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupChat(2, $data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //echo '<pre>'; print_r($Check); exit;
                if($Check->error_code == 311){
                    $data = $Check;   
                    $responseOld = [
                        'data'  => $data->data['msg']    
                    ];
                    $partner_array['id']            =   @$responseOld['data']['rm_id'] ? $responseOld['data']['rm_id'] : '';
                    $partner_array['userid']        =   @$responseOld['data']['userid'] ? $responseOld['data']['userid'] : '';
                    $partner_array['picUrl']  =   $responseOld['data']['picUrl'] ? $responseOld['data']['picUrl'] : '';
                    $partner_array['user_name']  =   @$responseOld['data']['username'] ? $responseOld['data']['username'] : '';
                    $partner_array['first_name']  =   @$responseOld['data']['first_name'] ? $responseOld['data']['first_name'] : '';
                    $partner_array['last_name']  =   @$responseOld['data']['last_name'] ? $responseOld['data']['last_name'] : '';
                    $partner_array['is_verified']  =   @$responseOld['data']['is_verified'] ? $responseOld['data']['is_verified'] : '';
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$responseOld['data']['user_type'] ? $responseOld['data']['user_type'] : '';
                    $partner_array['text']  =   @$responseOld['data']['text'] ? $responseOld['data']['text'] : '';
                    $partner_array['media_url']  =   @$responseOld['data']['media_url'] ? $responseOld['data']['media_url'] : '';
                    $partner_array['message_type']  =   @$responseOld['data']['message_type'] ? $responseOld['data']['message_type'] : 0;
                    $partner_array['added_date_timestamp']  =   @$list['added_date'] ? strtotime($responseOld['data']['added_date']) :'';
                    $partner_array['reply_id']  =   @$responseOld['data']['reply_id'] ? @$responseOld['data']['reply_id'] : '';
                    /// echo '<pre>'; print_r($partner_array); exit;
                    if($partner_array['reply_id'] != ''){//
                         
                        $quotmsg = DB::table('room_msgs')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','room_msgs.*')
                            ->where('rm_id','=',@$partner_array['reply_id'])
                            ->leftjoin('users','room_msgs.sender_id','users.id')
                            ->first();

                        if(!empty($quotmsg)){
                            //echo '<pre>'; print_r($quotmsg); exit;
                            $partner_array['reply']['id']            =   @$quotmsg->rm_id ? $quotmsg->rm_id : '';
                            $partner_array['reply']['userid']        =   @$quotmsg->userid ? $quotmsg->userid : '';
                            $partner_array['reply']['picUrl']  =   @$quotmsg->picUrl ? $quotmsg->picUrl : '';
                            $partner_array['reply']['user_name']  =   @$quotmsg->username ? $quotmsg->username : '';
                            $partner_array['reply']['first_name']  =   @$quotmsg->first_name ? $quotmsg->first_name : '';
                            $partner_array['reply']['last_name']  =   @$quotmsg->last_name ? $quotmsg->last_name : '';
                            $partner_array['reply']['is_verified']  =   @$quotmsg->is_verified ? $quotmsg->is_verified : '';
                             // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                            $partner_array['reply']['user_type']  =   @$quotmsg->user_type ? $quotmsg->user_type : '';
                            $partner_array['reply']['text']  =   @$quotmsg->text ? $quotmsg->text : '';
                             $partner_array['reply']['media_url']  =   @$quotmsg->media_url ? $quotmsg->media_url : '';
                            $partner_array['reply']['message_type']  =   @$quotmsg->message_type ? $quotmsg->message_type : 0;
                            $partner_array['reply']['added_date_timestamp']  =   @$quotmsg->added_date ? strtotime($quotmsg->added_date) :'';
                            $partner_array['reply']['added_date']  =   @$quotmsg->added_date ? \Carbon\Carbon::createFromTimeStamp(strtotime($quotmsg->added_date))->diffForHumans() :'';
                        }    
                
                    }
                    $partner_array['added_date']  =  @$responseOld['data']['added_date'] ?  \Carbon\Carbon::createFromTimeStamp(strtotime($responseOld['data']['added_date']))->diffForHumans() : '';

                    //print_r($responseOld); exit; 
                    // $Check = $ApiService->post_detail(1,$data['post_id']);
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                       'data'=>$partner_array
                        
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /***********************************************************************************
    * API                   => groupChatMessageList                                    *
    * Description           => It is to groupChatMessageList                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function groupChatMessageList(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->groupChatMessageList($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
               
                $Partner_list['chat'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                        
                    $partner_array['id']            =   @$list['rm_id'] ? $list['rm_id'] : '';
                    $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                    $partner_array['text']  =   @$list['text'] ? $list['text'] : '';
                    $partner_array['reply_id']  =   @$list['reply_id'] ? $list['reply_id'] : '';
                    if($partner_array['reply_id'] != ''){//
                         
                        $quotmsg = DB::table('room_msgs')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','room_msgs.*')
                            ->where('rm_id','=',@$partner_array['reply_id'])
                            ->leftjoin('users','room_msgs.sender_id','users.id')
                            ->first();

                        if(!empty($quotmsg)){
                            //echo '<pre>'; print_r($quotmsg); exit;
                            $partner_array['reply']['id']            =   @$quotmsg->rm_id ? $quotmsg->rm_id : '';
                            $partner_array['reply']['userid']        =   @$quotmsg->userid ? $quotmsg->userid : '';
                            $partner_array['reply']['picUrl']  =   @$quotmsg->picUrl ? $quotmsg->picUrl : '';
                            $partner_array['reply']['user_name']  =   @$quotmsg->username ? $quotmsg->username : '';
                            $partner_array['reply']['first_name']  =   @$quotmsg->first_name ? $quotmsg->first_name : '';
                            $partner_array['reply']['last_name']  =   @$quotmsg->last_name ? $quotmsg->last_name : '';
                            $partner_array['reply']['is_verified']  =   @$quotmsg->is_verified ? $quotmsg->is_verified : '';
                             // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                            $partner_array['reply']['user_type']  =   @$quotmsg->user_type ? $quotmsg->user_type : '';
                            $partner_array['reply']['text']  =   @$quotmsg->text ? $quotmsg->text : '';
                             $partner_array['reply']['media_url']  =   @$quotmsg->media_url ? $quotmsg->media_url : '';
                            $partner_array['reply']['message_type']  =   @$quotmsg->message_type ? $quotmsg->message_type : 0;
                            $partner_array['reply']['added_date_timestamp']  =   @$quotmsg->added_date ? strtotime($quotmsg->added_date) :'';
                            $partner_array['reply']['added_date']  =   @$quotmsg->added_date ? \Carbon\Carbon::createFromTimeStamp(strtotime($quotmsg->added_date))->diffForHumans() :'';
                        }    
                
                    }
                    $partner_array['media_url']  =   @$list['media_url'] ? $list['media_url'] : '';
                    $partner_array['message_type']  =   @$list['message_type'] ? $list['message_type'] : 0;
                    $partner_array['added_date_timestamp']  =   @$list['added_date'] ? strtotime($list['added_date']) :'';
                    $partner_array['added_date']  =   @$list['added_date'] ? \Carbon\Carbon::createFromTimeStamp(strtotime($list['added_date']))->diffForHumans() :'';

                    array_push($Partner_list['chat'],$partner_array);
                }
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    
    /*****************************************************************************
    * API                   => RE Post                                           *
    * Description           => It is Use to  Re Post                             *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function grouprepost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->grouprepost(2, $data);
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }


    /************************************************************************************
    * API                   => vote on post                                             *
    * Description           => It is used for vote on post                              * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function groupvote(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('v_option' => 'required', 'v_post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupvote($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }
    /**********************************************************************************
      API                   => Get  partner detail                                    *
    * Description           => It is user for partner detail                          *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function group_post_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['post_id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->group_post_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 213    ){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    public function groupdelete_post(Request $request)
    {
        if($request->method() == 'DELETE'){
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->groupdeletePost($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 302){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            }    
        }   
    }

    /************************************************************************************
    * API                   => Create favourite post                                    *
    * Description           => It is used for favourite post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function groupfavourite(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupfavourite($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }    
    }


    /***********************************************************************************
    * API                   => Home Page Post list                                     *
    * Description           => It is to get Post list                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function grouppost_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->grouppost_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['post'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $repost  = array();
                    $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                    
                    $like_count  = $UserRepostitory->group_like_count($postid);
                    $favourite_count  = $UserRepostitory->group_favourite_count($postid);
                    $comment_count  = $UserRepostitory->group_comment_count($postid);
                    $repost_count  = $UserRepostitory->group_repost_count($postid);  
                    $is_my_like = $UserRepostitory->group_my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->group_is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->group_total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->group_vote_count($postid) ; 

                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }
                    if($list['repost_id'] != ''){
                        $repost = DB::table('group_posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','group_posts.*')
                        ->where('group_posts.id','=',$list['repost_id'])
                        ->leftjoin('users','group_posts.u_id','users.id')
                        ->first();
                        $list['repost_id'] = '';

                        //echo $repost->id; exit;
                        //print_r($repost); exit;
                        $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                        $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                        
                        $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                        $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                        $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                        $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                        $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                        $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                        $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        
                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                        
                        $partner_array['post_data']['is_reposted']  =  true;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        
                        
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                        $partner_array['post_data']['retweet_count']  =   $repost_count ;
                        $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                        $partner_array['post_data']['stock_name']  =   @$repost->stock_name ? $repost->stock_name : '';
                        $partner_array['post_data']['stock_target_price']  =   @$repost->stock_target_price ? $repost->stock_target_price : '';
                        $partner_array['post_data']['time_left']  =   @$repost->time_left ? $repost->time_left : '';
                        $partner_array['post_data']['term']  =   @$repost->term ? $repost->term : '';
                        $partner_array['post_data']['result']  =   @$repost->result ? $repost->result : '';
                        $partner_array['post_data']['trend']   =  @$repost->trend ? $repost->trend : 0;
                        $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($repost->poll_one)){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($repost->poll_two)){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                        }
                        if(!empty($repost->poll_three)){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($repost->poll_four)){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                        

                    }else{
                        $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                        $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;

                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                        $partner_array['post_data']['is_reposted']  =  false;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

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

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($list['poll_one'])){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($list['poll_two'])){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                        }
                        if(!empty($list['poll_three'])){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($list['poll_four'])){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                    }
                    array_push($Partner_list['post'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /************************************************************************************
    * API                   => Create Like post                                         *
    * Description           => It is used for liked the post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function grouplike(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->grouplike($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }
    /*****************************************************************************
    * API                   => GroupCommpent Post                                *
    * Description           => It is Use to  Comment Post                        *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function groupcommentPost(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            $rules = array(
                    'description'   =>  'required',
                    'post_id'   =>  'required');

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupcommentPost(2, $data);
                
                
                    //print_r($Check); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                
                if($Check->error_code == 218){
                    // $Check = $ApiService->post_detail(1,$data['post_id']);
                    $response = [
                        'code' => 200,
                        'msg'=>  $msg,
                        'data' => $Check->data
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }
   
    /************************************************************************************
    * API                   => Create Like comment                                      *
    * Description           => It is used for liked the comment                         * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function groupcomment_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('c_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupcomment_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }

    /*****************************************************************************
    * API                   => Remove as Admin                                   *
    * Description           => It is used for remove admin                       *        
    * Required Parameters                                                        *
    * Created by            => Sunil                                             *
    ******************************************************************************/
    
    public function removeAdmin(Request $request){
        if($request->method() == 'POST'){
            $rules = array('g_id' => 'required','userid' =>'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);
            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->removeAdmin($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 305){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        //'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }   

        return $response;        
        
    }

    /*****************************************************************************
    * API                   => Remove group user                                 *
    * Description           => It is used for remove user                       *        
    * Required Parameters                                                        *
    * Created by            => Sunil                                             *
    ******************************************************************************/

    public function removeGroupUser(Request $request)
    {
        if($request->method() == 'DELETE'){
            $rules = array('g_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->removeGroupUser($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 306){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            }    
        }   
    }

    /*****************************************************************************
    * API                   => Delete Group                                      *
    * Description           => It is used for Delete admin                       *        
    * Required Parameters                                                        *
    * Created by            => Sunil                                             *
    ******************************************************************************/
    
    public function deleteGroup(Request $request){
        if($request->method() == 'POST'){
            $rules = array('g_id' => 'required','userid' =>'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);
            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->deleteGroup($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 307){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        //'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }   

        return $response;        
        
    }
    /***************************************************************************************
      API                   => Sub Category list                                           *
    * Description           => It is to get Chip list                                      *
    * Required Parameters   => Access Token                                                *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function subcategory_list(Request $request){
       
        if($request->method() == 'GET'){
            $data = $request->all();
            $ApiService = new ApiService();
            $Check = $ApiService->subcategory_list($data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 641){
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $responseOld['data']['data'],
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from'],
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to'],
                    'total' => $responseOld['data']['total']
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /***************************************************************************************
      API                   => pendingSubscriptionPlan IOS                                 *
    * Required Parameters   => Access Token                                                *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function pendingSubscriptionPlan(Request $request){
         if($request->method() == 'POST'){
            $data = $request->all();
            $userId = Auth::user()->id;
            $ApiService = new ApiService();
            $Check = $ApiService->pendingSubscriptionPlan($data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //$data = $Check->data;   
            if($Check->error_code == 221){
                /*$responseOld = [
                    'data'  => $data->toArray()    
                ];*/
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

    /***************************************************************************************
      API                   => cronJobForSubscreption                                     *
    * Required Parameters   => Access Token                                                *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function cronJobForSubscreption(Request $request){
         if($request->method() == 'GET'){
            $data = $request->all();
            //$userId = Auth::user()->id;
            $ApiService = new ApiService();
            $Check = $ApiService->cronJobForSubscreption();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            $data = $Check->data;   
            if($Check->error_code == 221){
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

     /***************************************************************************************
      API                   => pendingSubscriptionPlan IOS                                 *
    * Required Parameters   => Access Token                                                *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/

    public function androidSubscreption(Request $request){
         if($request->method() == 'POST'){
            $data = $request->all();
            $userId = Auth::user()->id;
            $ApiService = new ApiService();
            $Check = $ApiService->androidSubscreption($data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            //$data = $Check->data;   
            if($Check->error_code == 221){
                /*$responseOld = [
                    'data'  => $data->toArray()    
                ];*/
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }  
    }

     /**********************************************************************************
      API                   => requestVerification                                    *
    * Description           => It is user for requestVerification                     *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function requestVerification(Request $request){
        
        $Is_method  = 0; 
        if($request->method() == 'POST'){

            $data = $request->all();
            $userId= Auth::user()->id;
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->requestVerification($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }      



        
        return $response;
    }



    /***************************************************************************************
      API                   => Set Match Preferences                                      *
    * Description           => It is user for Profile                                     *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
        public function visibility(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        
        if($request->method() == 'POST'){

            $data = $request->all();
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->visibilty_profile($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    //'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }      



        
        return $response;
    }



    /***********************************************************************************
    * API                   => Home Page Post list                                     *
    * Description           => It is to get Post list                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function post_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->post_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['post'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $repost  = array();
                    $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                    
                    $like_count  = $UserRepostitory->like_count($postid);
                    $user_plus_like_count  = $UserRepostitory->user_plus_like_count($postid);
                    //echo '<pre>'; print_r($user_plus_like_count); 
                    $favourite_count  = $UserRepostitory->favourite_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 

                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }
                    if($list['repost_id'] != ''){
                        $repost = DB::table('posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','pollitical_orientation','posts.*')
                        ->where('posts.id','=',$list['repost_id'])
                        ->leftjoin('users','posts.u_id','users.id')
                        ->first();
                        $list['repost_id'] = '';

                        //echo $repost->id; exit;
                        //print_r($repost); exit;
                        $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                        $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                        
                        $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                        $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                        
                        $partner_array['pollitical_orientation']  =   @$repost->pollitical_orientation ? $repost->pollitical_orientation : '';

                        $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                        $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                        $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                        $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                        $partner_array['category']  =    @$repost->category ? $repost->category : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                        $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        $partner_array['post_data']['user_plus_like_count']  =   $user_plus_like_count;
                        
                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                        
                        $partner_array['post_data']['is_reposted']  =  true;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        
                        
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                        $partner_array['post_data']['retweet_count']  =   $repost_count ;
                        $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                        $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;
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
                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($repost->poll_one)){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($repost->poll_two)){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                        }
                        if(!empty($repost->poll_three)){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($repost->poll_four)){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                        

                    }else{
                        $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                        $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                        $partner_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : 0;
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                        $partner_array['category']            =   @$list['category'] ? $list['category'] : '';
                      
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        $partner_array['post_data']['user_plus_like_count']  =  $user_plus_like_count;
                        

                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                        $partner_array['post_data']['is_reposted']  =  false;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$list['share_count'] ? $list['share_count'] : 0;

                        $partner_array['post_data']['retweet_count']  =  $repost_count;

                        $partner_array['post_data']['posted_time']  =   @$list['posted_time'] ? $list['posted_time'] : 0;

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

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($list['poll_one'])){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($list['poll_two'])){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                        }
                        if(!empty($list['poll_three'])){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($list['poll_four'])){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                    }

                    array_push($Partner_list['post'],$partner_array);
                }
                $Partner_list['paging']['current_page'] = $responseOld['data']['current_page'];
                $Partner_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                $Partner_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                $Partner_list['paging']['last_page'] = $responseOld['data']['last_page'];
                $Partner_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                $Partner_list['paging']['per_page'] = $responseOld['data']['per_page'];
                $Partner_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                $Partner_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                   
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /***********************************************************************************
    * API                   => Home Page activity_list                                 *
    * Description           => It is to get activity_list                              *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function activity_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->activity_list($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['post'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $repost  = array();
                    $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                    
                    $like_count  = $UserRepostitory->like_count($postid);
                    $favourite_count  = $UserRepostitory->favourite_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 
                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }
                    if($list['repost_id'] != ''){
                        $repost = DB::table('posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
                        ->where('posts.id','=',$list['repost_id'])
                        ->leftjoin('users','posts.u_id','users.id')
                        ->first();
                        $list['repost_id'] = '';

                        //echo $repost->id; exit;
                        //print_r($repost); exit;
                        $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                        $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                        
                        $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                        $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                        $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                        $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                        $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                        $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                        $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        
                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                        
                        $partner_array['post_data']['is_reposted']  =  true;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        
                        
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                        $partner_array['post_data']['retweet_count']  =   $repost_count ;
                        $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                        $partner_array['post_data']['stock_name']  =   @$repost->stock_name ? $repost->stock_name : '';
                        $partner_array['post_data']['stock_target_price']  =   @$repost->stock_target_price ? $repost->stock_target_price : '';
                        $partner_array['post_data']['time_left']  =   @$repost->time_left ? $repost->time_left : '';
                        $partner_array['post_data']['term']  =   @$repost->term ? $repost->term : '';
                        $partner_array['post_data']['result']  =   @$repost->result ? $repost->result : '';
                        $partner_array['post_data']['trend']   =  @$repost->trend ? $repost->trend : 0;
                        $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($repost->poll_one)){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($repost->poll_two)){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =  $vote_count_per['is_voted_two'];
                        }
                        if(!empty($repost->poll_three)){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($repost->poll_four)){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                        }
                        

                    }else{
                        $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                        $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;

                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                        $partner_array['post_data']['is_reposted']  =  false;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

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

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($list['poll_one'])){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($list['poll_two'])){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =  $vote_count_per['is_voted_two'];
                        }
                        if(!empty($list['poll_three'])){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($list['poll_four'])){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                        }
                    }
                    array_push($Partner_list['post'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



   
    



    


    /**********************************************************************************
      API                   => Get  partner detail                                    *
    * Description           => It is user for partner detail                          *
    * Required Parameters   =>                                                        *
    * Created by            => Sunil                                                  *
    ***********************************************************************************/
    public function post_detail(Request $request){
        
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $request['post_id'];
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->post_detail($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 213    ){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }

    /***************************************************************************************
      API                   => Get and update Profile                                     *
    * Description           => It is user for Profile                                     *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function profile1(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
           

            //$data = $request->id;
            $data = $userId;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        if($request->method() == 'POST'){

            $data = $request->all();
            $Is_method = 0;
            $ApiService = new ApiService();
            $Check = $ApiService->profile($Is_method,$data);
            
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 217){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }      

        return $response;
    }






    /***************************************************************************************
      API                   => Upload Gallery                                              *
    * Description           => It is user for for CRED gallery api                                      *
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    
    public function gallery(Request $request){
        $Is_method = 0;
        if($request->method() == 'GET'){
        
            $Is_method = 1;

            $rules = array('p_u_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }
        }    


        if($request->method() == 'POST'){


            $Is_method = 2;
            $rules = array('p_photo' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 218){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }  


        
        if($request->method() == 'DELETE'){


            $Is_method = 3;
            $rules = array('p_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{

                $ApiService = new ApiService();
                $Check = $ApiService->gallery($Is_method,$data);
                
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 214){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }  

        return $response;
    
    }


    /*****************************************************************************
    * API                   => Make Admin                                        *
    * Description           => It is used for make admin                         *        
    * Required Parameters                                                        *
    * Created by            => Sunil                                             *
    ******************************************************************************/
    
    public function makeAdmin(Request $request){
         if($request->method() == 'POST'){
            $rules = array('g_id' => 'required','userid' =>'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);
            if($validate->fails()){      

                $validate_error = $validate->errors()->all();
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->makeAdmin($data);
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //echo '<pre>'; print_r($Check); exit;
                if($Check->error_code == 304){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        //'data'  =>  $Check->data 
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
        }   

        return $response;        
        
    }


   


    /************************************************************************************
    * API                   => Create Like post                                         *
    * Description           => It is used for liked the post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => Create favourite post                                    *
    * Description           => It is used for favourite post                            * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function favourite(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->favourite($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }    
    }



    /************************************************************************************
    * API                   => Create follow/unfollow                                   *
    * Description           => It is used for follow/unfollow  the post                 * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function follow(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('user_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->follow($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->error_code); exit;
                if($Check->error_code == 219){
                    $Check->data['is_follow'] = 1;
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $Check->data['is_follow'] = 0;
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }

 
    /************************************************************************************
    * API                   => Create Like comment                                      *
    * Description           => It is used for liked the comment                         * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function comment_like(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('c_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->comment_like($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => vote on post                                             *
    * Description           => It is used for vote on post                              * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function vote(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('v_option' => 'required', 'v_post_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->vote($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->data); exit;
                if($Check->error_code == 219){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }







    /***********************************************************************************
    * API                   => WatchList                                               *
    * Description           => It is to get Watchlist                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function watch_list(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->watch_list($request);
            $error_msg = new Msg();
            $userId= Auth::user()->id;
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 647){
                //print_r($Check); exit;
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                //$Partner_list['tranding'] = array();
                // $trandingList = $this->tranding();
                //$Partner_list['tranding'] = $trandingList;

                $Partner_list['post'] = array();

                foreach($responseOld['data']['data'] as $list){
                    $partner_array = array();
                    $repost  = array();
                    $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                    
                    $like_count  = $UserRepostitory->like_count($postid);
                    $favourite_count  = $UserRepostitory->favourite_count($postid);
                    $comment_count  = $UserRepostitory->comment_count($postid);
                    $repost_count  = $UserRepostitory->repost_count($postid);  
                    $is_my_like = $UserRepostitory->my_like_count($postid,$userId);      
                    $is_my_favourite = $UserRepostitory->is_my_favourite($postid,$userId);      
                    if($list['post_type'] == 3){
                        $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                        $vote_count_per = $UserRepostitory->vote_count($postid) ; 
                       // print_r($vote_count_per); exit;
                    }else{
                        $total_vote_count = 0; 
                        $vote_count_per = 0 ; 

                    }
                    if($list['repost_id'] != ''){
                        $repost = DB::table('posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','posts.*')
                        ->where('posts.id','=',$list['repost_id'])
                        ->leftjoin('users','posts.u_id','users.id')
                        ->first();
                        $list['repost_id'] = '';

                        //echo $repost->id; exit;
                        //print_r($repost); exit;
                        $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                        $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                        
                        $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                        $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                        $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                        $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                        $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                        $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                        $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;
                        
                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                        
                        $partner_array['post_data']['is_reposted']  =  true;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        
                        
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

                        $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                        $partner_array['post_data']['retweet_count']  =   $repost_count ;
                        $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                        $partner_array['post_data']['stock_name']  =   @$repost->stock_name ? $repost->stock_name : '';
                        $partner_array['post_data']['stock_target_price']  =   @$repost->stock_target_price ? $repost->stock_target_price : '';
                        $partner_array['post_data']['time_left']  =   @$repost->time_left ? $repost->time_left : '';
                        $partner_array['post_data']['term']  =   @$repost->term ? $repost->term : '';
                        $partner_array['post_data']['result']  =   @$repost->result ? $repost->result : '';
                        $partner_array['post_data']['trend']   =  @$repost->trend ? $repost->trend : 0;
                        $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($repost->poll_one)){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($repost->poll_two)){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =  $vote_count_per['is_voted_two'];
                        }
                        if(!empty($repost->poll_three)){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($repost->poll_four)){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][4]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                        

                    }else{
                        $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                        $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                        $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                        $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                        $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                        $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                        $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                        $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                       // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                        $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                        $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                        $partner_array['post_data'] = array();
                        $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                        $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                        $partner_array['post_data']['like_count']  =   $like_count;

                        $partner_array['post_data']['is_liked'] = $is_my_like;
                        
                        $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                        $partner_array['post_data']['is_reposted']  =  false;
                        
                        $partner_array['post_data']['favourite_count'] = $favourite_count;
                        $partner_array['post_data']['comment_count']  =   @$comment_count;

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

                        $partner_array['post_data']['total_votes']  =   $total_vote_count;
                        unset($partner_array['post_data']['options']);
                        if(!empty($list['poll_one'])){
                            $partner_array['post_data']['options'][0]['id']  =   1;
                            $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                            $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                            //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                            $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                        }
                        if(!empty($list['poll_two'])){
                            $partner_array['post_data']['options'][1]['id']  =   2;
                            $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                            $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                            //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                            $partner_array['post_data']['options'][1]['is_voted']  =  $vote_count_per['is_voted_two'];
                        }
                        if(!empty($list['poll_three'])){
                            $partner_array['post_data']['options'][2]['id']  =   3;
                            $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                            $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                            //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                            $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                        }
                        if(!empty($list['poll_four'])){
                            $partner_array['post_data']['options'][3]['id']  =   4;
                            $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                            $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                            //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                            $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                            
                        }
                    }
                    array_push($Partner_list['post'],$partner_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Partner_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }



    /***********************************************************************************
    * API                   => notificationList                                        *
    * Description           => It is to get notificationList                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function notificationList(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->notificationList($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 277){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                    //print_r($Check->data); exit;           
                //print_r($Check); exit;
                $notification_list['notification'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $notification_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $notification_array['id'] =  @$list['n_id'] ? $list['n_id'] : '';
                    $notification_array['sender_id']            =   @$list['n_sender_id'] ? $list['n_sender_id'] : '';
                    $notification_array['userid']        =   @$list['n_u_id'] ? $list['n_u_id'] : '';
                    $notification_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $notification_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $notification_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $notification_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $notification_array['n_type']  =   @$list['n_type'] ? $list['n_type'] : '';
                    $notification_array['message']  =   @$list['n_message'] ? $list['n_message'] : '';
                    $notification_array['status']  =   @$list['n_status'] ? $list['n_status'] : '';
                    $notification_array['added_date']  =   @$list['n_added_date'] ? $list['n_added_date'] : '';
                    //$notification_list[] =$notification_array;
                    array_push($notification_list['notification'],$notification_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $notification_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /***********************************************************************************
    * API                   => notificationList                                        *
    * Description           => It is to get notificationList                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function followUser(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->followUser($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 280){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //print_r($Check); exit;
                $user_list['users'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['id'] =  @$list['id'] ? $list['id'] : '';
                    $user_array['userid'] =  @$list['userid'] ? $list['userid'] : '';
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $user_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $user_array['username']  =   @$list['username'] ? $list['username'] : '';
                    array_push($user_list['users'],$user_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $user_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***********************************************************************************
    * API                   => Group pending request                                   *
    * Description           => It is to get pending  request                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/


    public function groupUser(Request $request){
       
        if($request->method() == 'GET'){
            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->groupUser($request);
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 281){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //print_r($Check); exit;
                $user_list['users'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['id'] =  @$list['userid'] ? $list['userid'] : '';
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $user_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $user_array['gm_user_type']  =   @$list['gm_user_type'] ? $list['gm_user_type'] : '';
                    $user_array['user_type']  =   @$list['gm_user_type'] ? $list['gm_user_type'] : '';
                    $user_array['status']  =   @$list['gm_status'] ? $list['gm_status'] : 0;
                    array_push($user_list['users'],$user_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $user_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }




    /***********************************************************************************
    * API                   => notificationList                                        *
    * Description           => It is to get notificationList                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function requestList(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->requestList($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 280){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //print_r($Check); exit;
                $user_list['users'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['gm_id'] =  @$list['gm_id'] ? $list['gm_id'] : 0;
                    $user_array['g_id'] =  @$list['gm_g_id'] ? $list['gm_g_id'] : 0;
                    $user_array['status'] =  @$list['gm_status'] ? $list['gm_status'] : 0;
                    $user_array['userid'] =  @$list['userid'] ? $list['userid'] : 0;
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $user_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    array_push($user_list['users'],$user_array);
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $user_list,
                    'current_page' => $responseOld['data']['current_page'],
                    'first_page_url' => $responseOld['data']['first_page_url'],
                    'from' => $responseOld['data']['from']?$responseOld['data']['from']:0,
                    'last_page' => $responseOld['data']['last_page'],
                    'last_page_url' => $responseOld['data']['last_page_url'],
                    'per_page' => $responseOld['data']['per_page'],
                    'to' => $responseOld['data']['to']?$responseOld['data']['to']:0,
                    'total' => $responseOld['data']['total']?$responseOld['data']['total']:0
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => acceptDecline                                            *
    * Description           => It is used for acceptDecline  the group                  * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function acceptDecline(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('gm_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->acceptDecline($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->error_code); exit;
                if($Check->error_code == 309 || $Check->error_code == 308){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }


    /************************************************************************************
    * API                   => groupCancleRequest                                       *
    * Description           => It is used for groupCancleRequest  the group             * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function groupCancleRequest(Request $request){
       
        if($request->method() == 'DELETE'){
            $data = $request;
            
            $rules = array('g_id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->groupCancleRequest($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->error_code); exit;
                if($Check->error_code == 309 || $Check->error_code == 308){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }


   


    
    /***************************************************************************************
    * API                   => subscriptionsList                                           *
    * Description           => It is used for subscriptionsList                            * 
    * Required Parameters   =>                                                             *
    * Created by            => Sunil                                                       *
    ***************************************************************************************/
    
    public function subscriptionsList(Request $request){
        //send push notification
        $sender_name = 'sunil';
        $message =  $sender_name." find as match.";
        $datass['userid'] = 66;
        $datass['name'] = 'sunil';
        $datass['n_type'] = 2;
        $datass['noti_type'] = "2";
        $datass['message'] = $message;
        $notify = array ();
        $notify['receiver_id'] = 83;
        $notify['relData'] = $datass;
        $notify['message'] = $message;

        $UserRepostitory = new UserRepository();
        $test =  $UserRepostitory->sendPushNotification($notify); exit;
        if($request->method() == 'GET'){
            //$data = $request;
            $ApiService = new ApiService();
            $Check = $ApiService->subscriptionsList();
            //print_r($Check); exit;
            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 220){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }






    /*********************************************************************
      API                   => check_username                            *
    * Description           => It is user for username                   *
    * Required Parameters   =>                                           *
    * Created by            => Sunil                                     *
    **********************************************************************/
    public function check_username(Request $request){
        
        $userId= Auth::user()->id;
        $Is_method  = 0; 
      
        if($request->method() == 'GET'){
            $data = $request;
            $Is_method = 1;
            $ApiService = new ApiService();
            $Check = $ApiService->check_username($Is_method,$data,$userId);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 207){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }

        return $response;
    }

    

    /***************************************************************************************
      API                   => chat_user for test                                          *
    * Description           => It is user for chat_user                                  *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function chat_user(Request $request){
        $userId = Auth::user()->id;
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);
        //print_r($twilio); exit;
        $user = $twilio->conversations->v1->users
                                          ->create($userId);

        //print_r($user); exit;
        $sid = $user->sid;
        $ApiService = new ApiService();
        $Check = $ApiService->chat_user_sid_update($sid,$userId);
    }


    /***************************************************************************************
      API                   => Chat_token                                                 *
    * Description           => It is user for test_twilio                                 *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function chat_token(Request $request){
        
        // Required for all Twilio access tokens
        // Required for Chat grant
        $data = $request; 
        //print_r($data['device_type']); exit; 
        $twilioAccountSid = getenv("TWILIO_ACCOUNT_SID");
        $twilioApiKey = getenv("TWILIO_APIKEY");
        $twilioApiSecret = getenv("TWILIO_APISECRET");
        $userId = Auth::user()->id;
        // Required for Chat grant
        $serviceSid = getenv("TWILIO_SERVICESID");//Default
        $chat_env = getenv("CHAT_ENV");//Default
        // choose a random username for the connecting user
        $identity = $chat_env.''.$userId ;//$data['sid'];

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $twilioAccountSid,
            $twilioApiKey,
            $twilioApiSecret,
            3600,
            $identity
        );
        //print_r($token); exit;
        // Create Chat grant
        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid($serviceSid);
        if($data['device_type'] == 0){// APNS
            $chatGrant->setPushCredentialSid('CR6d5f79c62f75ff86e03453027a6662dda');
        }else{//FCM
            $chatGrant->setPushCredentialSid('CR159af2c172372ea4bf411d8e465104c5a');
        }
       
        // Add grant to token
        $token->addGrant($chatGrant);

        // render token to string
        $user_token = $token->toJWT();

        
        $response = [
            'code' => 200,
            'msg'=>  'Token created succesfully',
            'token'=> $user_token
        ];

        return $response;
    }


    public function addchatuser(){
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

       /* $message = $twilio->conversations->v1->conversations("CHc1bafe6eab554f01ba755b350fb450e4a")
                                     ->messages
                                     ->create([
                                                  "author" => "Dev3",
                                                  "body" => "Ahoy there!"
                                              ]
                                     );*/
          
        //if($data->EventType == 'onConversationAdded'){
            //fwrite($file,"\n ". print_r('sunil2', true));
            // fwrite($file,"\n ". print_r($data->EventType, true));
            // $receiver_id = getenv("CHAT_ENV").''.$data->Attributes; 
           //echo $receiver_id = getenv("CHAT_ENV").'3'; 
        $participant = $twilio->conversations->v1->conversations("CHc779b7e4ed3b44c29bad092083e68d61a")
                 ->participants
                 ->create([
                            "identity" => "Dev41"
                          ]
                 );
                $datanew =  json_decode ($participant ,true );                          
        print($datanew);
            //print($participant->sid);
        //}

    }

    public function chat_post_event(Request $request){
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $data = $request->all();  
        //if(isset($data)){

            //$datanew =  json_encode ( $data ,true );
            //$fileName = date('Ymd').'chat_post_event.txt';
            // prd($fileName);
            //$file = fopen($fileName,'a');
            $file = fopen('chat_pre_event.txt','a+');
            
            fwrite($file,"\n ". print_r('sunil1', true));
            //fwrite($file,"\n ". print_r($datanew, true));
            fwrite($file,"\n ". print_r($data, true));
            if(!empty($_FILES))
            {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            }

            if($data['EventType'] == 'onConversationAdded'){
                $sid = getenv("TWILIO_ACCOUNT_SID");
                $token = getenv("TWILIO_AUTH_TOKEN");
                $twilio = new Client($sid, $token);
                fwrite($file,"\n ". print_r('sunil2', true));
                $ConversationSid = $data['ConversationSid'];
                $Attributes = $data['Attributes'];
                $receiver_id = getenv("CHAT_ENV").''.$data['Attributes']; 
                $participant = $twilio->conversations->v1->conversations($ConversationSid)
                     ->participants
                     ->create([
                                "identity" => $receiver_id
                              ]
                     );

                //print($participant->sid);
            }

           
            fwrite($file,"\n ". print_r('sunil6', true));
                
            /////////
        //}
    }

    public function chat_pre_event(Request $request){
        $data = $request->all();   
        //if(isset($data)){

            $datanew =  json_encode ( $data ,true );

            if($datanew['EventType'] == 'onConversationAdded'){


            }
            $file = fopen('chat_pre_event.txt','a+');
            
            fwrite($file,"\n ". print_r($datanew, true));
            fwrite($file,"\n ". print_r($datanew->EventType, true));
            fwrite($file,"\n ". print_r($datanew->Attributes, true));
            fwrite($file,"\n ". print_r('sunil', true));
            if(!empty($_FILES))
            {
            
                fwrite($file,"\n ".print_r($_FILES, true));
                fclose($file);
            
            }
            if($datanew->EventType == 'onMessageAdded'){
                 fwrite($file,"\n ". print_r($datanew->EventType, true));
                $sid = getenv("TWILIO_ACCOUNT_SID");
                $token = getenv("TWILIO_AUTH_TOKEN");
                $twilio = new Client($sid, $token);
                $receiver_id = getenv("CHAT_ENV").''.$datanew->Attributes; 
                $participant = $twilio->conversations->v1->conversations($datanew->ConversationSid)
                     ->participants
                     ->create([
                                "identity" => $receiver_id
                              ]
                     );

                //print($participant->sid);
            }
            /////////
        //}
    }

    public function chat_update_uername(Request $request){
        $data = $request->all();   
             //if(isset($data)){
       
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

        $user = $twilio->conversations->v1->users("US6808d12f805c493b8572e02f81f03153")
          ->update([
                       "friendlyName" => "techno new name",
                   ]
          );

        //print($user->friendlyName);

       
                //print($participant->sid);
           
            /////////
        //}
    }


    public function check_pending(){
        $date = new DateTime;
        //echo $test = $date->format('Y-m-d H:i:s').'<br>';
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $result = DB::table('pending_matches')->where('is_pending','=',1)->where('is_notify','=',0)->where('added_date','<',$formatted_date)->get();
        if(!empty($result)){
            foreach ($result as $resultkey => $resultvalue) {
                # code...
                DB::table('pending_matches')->where('id', $resultvalue->id)
                ->update([
                   'is_notify' => 1,
                   ]);
                $message =  "your are not found any match in last fifteen minutes.";
                $data['userid'] = $resultvalue->sender_id;
                $data['message'] = $message;
                $data['n_type'] = 3;
                $notify = array ();
                $notify['receiver_id'] = $resultvalue->sender_id;
                $notify['relData'] = $data;
                $notify['message'] = $message;
                echo print_r($notify);
                $UserRepostitory   = new UserRepository();
                $test =  $UserRepostitory->sendPushNotification($notify); 
                         echo '<pre>'; print_r($resultvalue->sender_id);
            }
        }
    }

    // Cron 30MIn
    public function update_previous(){
        $date = new DateTime;
        //echo $test = $date->format('Y-m-d H:i:s').'<br>';
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $result = DB::table('pending_matches')->where('is_new','=',1)->where('is_pending','=',0)->where('added_date','<',$formatted_date)->get();

        if(!empty($result)){
            foreach ($result as $resultkey => $resultvalue) {
                //print_r($resultvalue->id); exit;
                # code...
                  DB::table('pending_matches')->where('id', $resultvalue->id)
                                ->update([
                               'is_pending' => 0,
                               'is_new' => 0,
                               ]);
                /*$message =  "your are not found any match in last fifteen minutes.";
                $data['userid'] = $resultvalue->sender_id;
                $data['message'] = $message;
                $data['n_type'] = 3;
                $notify = array ();
                $notify['receiver_id'] = $resultvalue->sender_id;
                $notify['relData'] = $data;
                $notify['message'] = $message;
                //print_r($notify); exit;
                $UserRepostitory   = new UserRepository();
                $test =  $UserRepostitory->sendPushNotification($notify); 
                         echo '<pre>'; print_r($resultvalue->sender_id);*/
            }
        }
    }
    




   


    /***************************************************************************************
      API                   => Get and notification_match_detail                          *
    * Description           => It is notification_match_detail                            *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/
    public function notification_match_detail(Request $request){
        
        $Is_method  = 0; 
        if($request->method() == 'GET'){
            $req = $request->id;
            $Is_method = 1;
            $data = Auth::user()->id; 
            $ApiService = new ApiService();
            $Check = $ApiService->notification_match_detail($Is_method,$req,$data);

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 303){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $Check->data  
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

        }
        return $response;
    }
    /***************************************************************************************
      API                   => Logout                                                     *
    * Description           => It is user for Logout                                      *
    * Required Parameters   =>                                                            *
    * Created by            => Sunil                                                      *
    ***************************************************************************************/


    public function logout(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $Check = $ApiService->logout();

            $error_msg = new Msg();
            $msg =  $error_msg->responseMsg($Check->error_code);
        
            if($Check->error_code == 642){
                $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }




    public function deleteAccount(Request $request)
    {
        if($request->method() == 'DELETE'){
               
                $ApiService = new ApiService();
                $Check = $ApiService->deleteAccount();
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 447){
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
                return $response;
            
        }   
    }

    public function all_post_list($request){
        //echo '<pre>'; print_r($request); exit; 
        $ApiService = new ApiService();
        $UserRepostitory = new UserRepository();
        $Check = $ApiService->post_list($request);
        $error_msg = new Msg();
        $msg =  $error_msg->responseMsg($Check->error_code);
        if($Check->error_code == 647){
            //print_r($Check); exit;
            $data = $Check->data;   
            $responseOld = [
                'data'  => $data->toArray()    
            ];
            
            $Partner_list['post'] = array();

            foreach($responseOld['data']['data'] as $list){
                //echo '<pre>'; print_r($list);
                $partner_array = array();
                $repost  = array();
                $postid = @$list['repost_id'] ? $list['repost_id'] : $list['id'];

                
                $like_count  = $UserRepostitory->like_count($postid);
                $favourite_count  = $UserRepostitory->favourite_count($postid);
                $comment_count  = $UserRepostitory->comment_count($postid);
                $repost_count  = $UserRepostitory->repost_count($postid);  
                $is_my_like = $UserRepostitory->my_like_count($postid,Auth::user()->id);      
                $is_my_favourite = $UserRepostitory->is_my_favourite($postid,Auth::user()->id);      
                if($list['post_type'] == 3){
                    $total_vote_count = $UserRepostitory->total_vote_count($postid); 
                    $vote_count_per = $UserRepostitory->vote_count($postid) ; 

                   // print_r($vote_count_per); exit;
                }else{
                    $total_vote_count = 0; 
                    $vote_count_per = 0 ; 

                }
                if($list['repost_id'] != ''){
                    $repost = DB::table('posts')->select('users.id as userid','users.first_name as first_name','users.last_name as last_name','users.username as username','users.photo as picUrl','users.user_status as is_verified','users.user_type as user_type','users.pollitical_orientation as pollitical_orientation','posts.*')
                    ->where('posts.id','=',$list['repost_id'])
                    ->leftjoin('users','posts.u_id','users.id')
                    ->first();
                    $list['repost_id'] = '';

                    //echo $repost->id; exit;
                    //print_r($repost); exit;
                    $partner_array['id']   =   @$repost->id ? $repost->id : $list['repost_id'];
                    $partner_array['original_id']   =   @$list['id'] ? $list['id'] :'';
                    $partner_array['userid']  =  @$repost->u_id ? $repost->u_id : '';
                    
                    $partner_array['picUrl']  =   @$repost->picUrl ? $repost->picUrl : '';
                    $partner_array['user_name']  =   @$repost->username ? $repost->username : '';
                    $partner_array['first_name']  =   @$repost->first_name ? $repost->first_name : '';
                    $partner_array['last_name']  =   @$repost->last_name ? $repost->last_name : '';
                    $partner_array['is_verified']  =   @$repost->is_verified ? $repost->is_verified : '';
                    $partner_array['pollitical_orientation']  =   @$repost->pollitical_orientation ? $repost->pollitical_orientation : '';
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$repost->user_type ? $repost->user_type : '';
                    $partner_array['post_type']  =   @$repost->post_type ? $repost->post_type : '';
                    $partner_array['post_data'] = array();
                    $partner_array['post_data']['imgUrl']  =   @$repost->imgUrl ? $repost->imgUrl : '';
                    $partner_array['post_data']['description']  =   @$repost->description ? $repost->description : 0;
                    $partner_array['post_data']['like_count']  =   $like_count;
                    
                    $partner_array['post_data']['is_liked'] = $is_my_like;
                    $partner_array['post_data']['is_favorited']  =  $is_my_favourite;
                    
                    $partner_array['post_data']['is_reposted']  =  true;
                    
                    $partner_array['post_data']['favourite_count'] = $favourite_count;
                    
                    
                    $partner_array['post_data']['comment_count']  =   @$comment_count;

                    $partner_array['post_data']['share_count']  =   @$repost->share_count ? $repost->share_count : 0;
                    $partner_array['post_data']['retweet_count']  =   $repost_count ;
                    $partner_array['post_data']['posted_time']  =   @$repost->posted_time ? $repost->posted_time : 0;
                    $partner_array['post_data']['stock_name']  =   @$repost->stock_name ? $repost->stock_name : '';
                    $partner_array['post_data']['stock_target_price']  =   @$repost->stock_target_price ? $repost->stock_target_price : '';
                    $partner_array['post_data']['time_left']  =   @$repost->time_left ? $repost->time_left : '';
                    $partner_array['post_data']['term']  =   @$repost->term ? $repost->term : '';
                    $partner_array['post_data']['result']  =   @$repost->result ? $repost->result : '';
                    $partner_array['post_data']['trend']   =  @$repost->trend ? $repost->trend : 0;
                    $partner_array['post_data']['recommendation']   =  @$repost->recommendation ? $repost->recommendation : 0;

                    $partner_array['post_data']['total_votes']  =   $total_vote_count;
                    unset($partner_array['post_data']['options']);
                    if(!empty($repost->poll_one)){
                        $partner_array['post_data']['options'][0]['id']  =   1;
                        $partner_array['post_data']['options'][0]['title']  =   @$repost->poll_one ? $repost->poll_one : '';
                        $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                        //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                        $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                    }
                    if(!empty($repost->poll_two)){
                        $partner_array['post_data']['options'][1]['id']  =   2;
                        $partner_array['post_data']['options'][1]['title']  =   @$repost->poll_two ? $repost->poll_two : '';
                        $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                        //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                        $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                    }
                    if(!empty($repost->poll_three)){
                        $partner_array['post_data']['options'][2]['id']  =   3;
                        $partner_array['post_data']['options'][2]['title']  =   @$repost->poll_three ? $repost->poll_three : '';
                        $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                        //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                        $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                    }
                    if(!empty($repost->poll_four)){
                        $partner_array['post_data']['options'][3]['id']  =   4;
                        $partner_array['post_data']['options'][3]['title']  =   @$repost->poll_four ? $repost->poll_four : '';
                        $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                        //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                        $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                        
                    }
                    

                }else{
                    $partner_array['id']            =   @$list['id'] ? $list['id'] : '';
                    $partner_array['original_id']   =   @$list['id'] ? $list['id'] :''; 
                    $partner_array['userid']        =   @$list['userid'] ? $list['userid'] : '';
                    $partner_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $partner_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    $partner_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $partner_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $partner_array['is_verified']  =   @$list['is_verified'] ? $list['is_verified'] : '';
                    $partner_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                   // $partner_array['tags']  =   @$list['tags'] ? $list['tags'] : '';
                    $partner_array['user_type']  =   @$list['user_type'] ? $list['user_type'] : '';
                    $partner_array['post_type']  =   @$list['post_type'] ? $list['post_type'] : '';
                    $partner_array['post_data'] = array();
                    $partner_array['post_data']['imgUrl']  =   @$list['imgUrl'] ? $list['imgUrl'] : '';
                    $partner_array['post_data']['description']  =   @$list['description'] ? $list['description'] : 0;
                    $partner_array['post_data']['like_count']  =   $like_count;

                    $partner_array['post_data']['is_liked'] = $is_my_like;
                    
                    $partner_array['post_data']['is_favorited']  =  $is_my_favourite;

                    $partner_array['post_data']['is_reposted']  =  false;
                    
                    $partner_array['post_data']['favourite_count'] = $favourite_count;
                    $partner_array['post_data']['comment_count']  =   @$comment_count;

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

                    $partner_array['post_data']['total_votes']  =   $total_vote_count;
                    unset($partner_array['post_data']['options']);
                    if(!empty($list['poll_one'])){
                        $partner_array['post_data']['options'][0]['id']  =   1;
                        $partner_array['post_data']['options'][0]['title']  =   @$list['poll_one'] ? $list['poll_one'] : '';
                        $partner_array['post_data']['options'][0]['percentage']  =  $vote_count_per['one_per'];;
                        //$partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['one'];
                        $partner_array['post_data']['options'][0]['is_voted']  =  $vote_count_per['is_voted_one'];
                    }
                    if(!empty($list['poll_two'])){
                        $partner_array['post_data']['options'][1]['id']  =   2;
                        $partner_array['post_data']['options'][1]['title']  =   @$list['poll_two'] ? $list['poll_two'] : '';
                        $partner_array['post_data']['options'][1]['percentage']  =  $vote_count_per['two_per'];
                        //$partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['two'];
                        $partner_array['post_data']['options'][1]['is_voted']  =   $vote_count_per['is_voted_two'];
                    }
                    if(!empty($list['poll_three'])){
                        $partner_array['post_data']['options'][2]['id']  =   3;
                        $partner_array['post_data']['options'][2]['title']  =   @$list['poll_three'] ? $list['poll_three'] : '';
                        $partner_array['post_data']['options'][2]['percentage']  =  $vote_count_per['three_per'];
                        //$partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['three'];
                        $partner_array['post_data']['options'][2]['is_voted']  =  $vote_count_per['is_voted_three'];
                    }
                    if(!empty($list['poll_four'])){
                        $partner_array['post_data']['options'][3]['id']  =   4;
                        $partner_array['post_data']['options'][3]['title']  =   @$list['poll_four'] ? $list['poll_four'] : '';
                        $partner_array['post_data']['options'][3]['percentage']  =  $vote_count_per['four_per'];
                        //$partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['four'];
                        $partner_array['post_data']['options'][3]['is_voted']  =  $vote_count_per['is_voted_four'];
                        
                    }
                }
                array_push($Partner_list['post'],$partner_array);
            }
            
        }
        //echo '<pre>'; print_r($Partner_list); exit;
        return $Partner_list;
           
    }

    

    /***********************************************************************************
    * API                   => User List                                               *
    * Description           => It is to get User List                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function userList(Request $request){
       
        if($request->method() == 'POST'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->userList($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 280){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //echo '<pre>'; print_r($responseOld['data']['data']); exit;
                $user_list['user_list'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['id'] =  @$list['userid'] ? $list['userid'] : '';
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['designation']  =   @$list['designation'] ? $list['designation'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $user_array['is_verified'] = @$list['is_verified'] ? $list['is_verified'] : '';
                    $user_array['location']  =   @$list['location'] ? $list['location'] : '';
                    $check_is_follow  = $UserRepostitory->check_is_follow($list['userid']);
                    $user_array['is_follow']  =   $check_is_follow;
                    array_push($user_list['user_list'],$user_array);
                }
                    $user_list['paging']['current_page'] = $responseOld['data']['current_page'];
                    $user_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                    $user_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                    $user_list['paging']['last_page'] = $responseOld['data']['last_page'];
                    $user_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                    $user_list['paging']['per_page'] = $responseOld['data']['per_page'];
                    $user_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                    $user_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $user_list,
       
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /***********************************************************************************
    * API                   => User List                                               *
    * Description           => It is to get User List                                  *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function alluserList(Request $request){
       
        if($request->method() == 'POST'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->alluserList($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 280){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //echo '<pre>'; print_r($responseOld['data']['data']); exit;
                $user_list['user_list'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['id'] =  @$list['userid'] ? $list['userid'] : '';
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['designation']  =   @$list['designation'] ? $list['designation'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $user_array['is_verified'] =  @$list['is_verified'] ? $list['is_verified'] : '';
                    $user_array['location']  =   @$list['location'] ? $list['location'] : '';
                    $check_is_follow  = $UserRepostitory->check_is_follow($list['userid']);
                    $user_array['is_follow']  =   $check_is_follow;
                    array_push($user_list['user_list'],$user_array);
                }
                    $user_list['paging']['current_page'] = $responseOld['data']['current_page'];
                    $user_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                    $user_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                    $user_list['paging']['last_page'] = $responseOld['data']['last_page'];
                    $user_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                    $user_list['paging']['per_page'] = $responseOld['data']['per_page'];
                    $user_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                    $user_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  'All User List',
                    'data'  =>  $user_list,
       
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /*****************************************************************************
    * API                   => create Debet                                      *
    * Description           => It is Use to  create Debet                        *
    * Required Parameters   =>                                                   *
    * Created by            => Sunil                                             *
    *****************************************************************************/    
    public function createDebet(Request $request){

        $data = $request->all();
        if($request->method() == 'POST'){

            //'g_title'   => 'required|unique:groups,g_title',
            $rules = array(
                    'topic'   => 'required|unique:debets,topic',
                    

                );

            $validate = Validator::make($data,$rules);

            if($validate->fails()){
                $validate_error = $validate->errors()->all();
                $response = ['code' => 403, 'msg'=>  $validate_error[0]]; 

            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->createDebet(2, $data);
                
                //print_r($Check->data['data']); exit; 
                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
            
                if($Check->error_code == 223){
                    $response = [
                        'code' => 200,
                        'msg'=>  'Debet Created'
                        //'data' => $Check->data['data']
                    ];
                }else{
                    $response = [
                        'code' => $Check->error_code,
                        'msg'=>  $msg
                    ];
                }
            }    
            return $response;
        }   
    }

    /**********************************************************************************
    * API                   => Request List                                            *
    * Description           => It is to get notificationList                           *
    * Required Parameters   => Access Token                                            *
    * Created by            => Sunil                                                   *
    ************************************************************************************/

    public function debetrequestList(Request $request){
       
        if($request->method() == 'GET'){

            $ApiService = new ApiService();
            $UserRepostitory = new UserRepository();
            $Check = $ApiService->debetrequestList($request);
            $error_msg = new Msg();

            $msg =  $error_msg->responseMsg($Check->error_code);
            if($Check->error_code == 280){
                $data = $Check->data;   
                $responseOld = [
                    'data'  => $data->toArray()    
                ];
                 // print_r($Check->data); exit;           
                //print_r($Check); exit;
                $user_list['users'] = array();
                foreach($responseOld['data']['data']  as $list){
                    $user_array = array();
                    //echo '<pre>';print_r($list); exit;
                    $user_array['id'] =  @$list['id'] ? $list['id'] : 0;
                    $user_array['topic'] =  @$list['topic'] ? $list['topic'] : '';
                    $user_array['photo'] =  @$list['photo'] ? $list['photo'] : '';
                    $user_array['date'] =  @$list['date'] ? $list['date'] : '';
                    $user_array['time_slot'] =  @$list['time_slot'] ? $list['time_slot'] : '';
                    $user_array['status'] =  @$list['status'] ? $list['status'] : 0;
                    $user_array['userid'] =  @$list['u_id'] ? $list['u_id'] : 0;
                    $user_array['picUrl']  =   @$list['picUrl'] ? $list['picUrl'] : '';
                    $user_array['pollitical_orientation']  =   @$list['pollitical_orientation'] ? $list['pollitical_orientation'] : '';
                    $user_array['first_name']  =   @$list['first_name'] ? $list['first_name'] : '';
                    $user_array['last_name']  =   @$list['last_name'] ? $list['last_name'] : '';
                    $user_array['user_name']  =   @$list['username'] ? $list['username'] : '';
                    array_push($user_list['users'],$user_array);

                    $user_list['paging']['current_page'] = $responseOld['data']['current_page'];
                    $user_list['paging']['first_page_url'] = $responseOld['data']['first_page_url'];
                    $user_list['paging']['from'] = $responseOld['data']['from']?$responseOld['data']['from']:0;
                    $user_list['paging']['last_page'] = $responseOld['data']['last_page'];
                    $user_list['paging']['last_page_url'] = $responseOld['data']['last_page_url'];
                    $user_list['paging']['per_page'] = $responseOld['data']['per_page'];
                    $user_list['paging']['to'] = $responseOld['data']['to']?$responseOld['data']['to']:0;
                    $user_list['paging']['total'] = $responseOld['data']['total']?$responseOld['data']['total']:0;
                }
                //echo '<pre>'; print_r($responseOld['data']); exit;
                 $response = [
                    'code'  =>  200,
                    'msg'   =>  $msg,
                    'data'  =>  $user_list,
                    
                ];
            }else{
                $response = [
                    'code' => $Check->error_code,
                    'msg'=>  $msg
                ];
            }

            return $response;
        }   
    }

    /************************************************************************************
    * API                   => Debet acceptDecline                                      *
    * Description           => It is used for acceptDecline  the group                  * 
    * Required Parameters   =>                                                          *
    * Created by            => Sunil                                                    *
    ************************************************************************************/
    public function debetacceptDecline(Request $request){
       
        if($request->method() == 'POST'){
            $data = $request;
            
            $rules = array('id' => 'required');
            $data = $request->all();
            $validate = Validator::make($data,$rules);

            if($validate->fails()){    
                $validate_error  = $validate->errors()->all();  
                $response = ['code'=>403, 'msg'=> $validate_error[0]];        
            }else{
                $ApiService = new ApiService();
                $Check = $ApiService->debetacceptDecline($data);

                $error_msg = new Msg();
                $msg =  $error_msg->responseMsg($Check->error_code);
                //print_r($Check->error_code); exit;
                if($Check->error_code == 309 || $Check->error_code == 308){
                    unset($Check->error_code);
                    $response = [
                        'code'  =>  200,
                        'msg'   =>  $msg,
                        'data'  =>  $Check->data
                    ];
                }else{
                    $response = [
                       // 'code' => $Check->error_code,
                        'code'  =>  200,
                        'msg'=>  $msg,
                        'data'  =>  $Check->data
                    ];
                }
            }

            return $response;
        }   
    }


    public function agoraToken(Request $request){
        $appID = "4544fc186fcc4ae79e2d3ddf6c9ce4c0";
        $appCertificate = "e7786275285a42d3aad478cb91856f3d";
        //$channelName = "e7786275285a42d3aad478cb91856f3d";
        $channelName = $request['channel_name'];
        $userId= Auth::user()->id; 
        $uid = 0; //    $userId;
        $uidStr = "2882341273";
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $tokenuId = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
       // echo 'Token with int uid: ' . $tokenuId . PHP_EOL;
       // echo $tokenuId;
        $tokenAccountId = RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uidStr, $role, $privilegeExpiredTs);
                //echo 'Token with user account: ' . $tokenAccountId . PHP_EOL;

        $response = [
            'code'  =>  200,
            'msg'   =>  'Token created',
            'tokenuId'  =>  $tokenuId,
            'tokenAccountId' => $tokenAccountId,
        ];
        return $response;
    }
}
