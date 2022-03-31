<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Auth;
use Validator;
use App\User;
use App\Models\Chip;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\UserRepository;
use App\Http\Controllers\Repository\CrudRepository;
use Session;
use \Cache;
use \Artisan;
class LoginController extends Controller
{

    public function login(Request $request){
        /*$pass = "123456";
        $hp= hash::make($pass);
        print_r($hp);
        die;*/
        return view('admin.login');  
    }
 

	public function dologin(Request $request){

 		$rules = array('email'=>'required|email:rfc,dns',
 					'password'=>'required | min:6',
 					'captcha'=>'required|captcha'
 					 );
 		
 		$validate = Validator::make($request->all(),$rules);
 		//$remember  = ( !empty( $request->remember ) )? true : false; 
 		 $remember = $request->has('remember') ? true : false;

 		// 	echo '<pre>'; print_r($remember); exit;
 			//dd($validate);
 		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		
 		}else{
 			$arg = $request->all();
 			$getuser = User::where('email',$arg['email'])->first();
 			$credential = [
			    'email' => $arg['email'],
			    'password' => $arg['password'],
			];

 			$Checkuser = Auth::attempt(['email' => $arg['email'], 'password' => $arg['password']], $remember);
 			//$Checkuser = Auth::check($arg['password'], $getuser->password_hash);
 			//$pass = bcrypt($arg['password']);
 			
 			if(!empty($Checkuser)){
 				//$Checkuser = password_verify($arg['password'], $getuser->password_hash);
	 			if($Checkuser == 1){
	 				$user = auth()->user();
	 				if(Auth::viaRemember())
			        {
			            dd("remembered successfully");
			        }else{
			            //dd("failed to remember");
			        }

	 				//dd($user);
	 				$user_detail = User::getUserDetail($getuser->id);
	 				//session(['userId',$user_detail->id]);
	 				//session(['userId',Auth::user()->id]);
	 				session(['UserId'=>$user_detail->id]);
 					//echo '<pre>'; print_r($user); exit;
	 				//echo '<pre>'; print_r(session('UserId')); exit;
	 				//print_r($user['id']);die;
	 				//if(session('UserId') == 5){ 
	 				if($user['id'] == 1 || $user['id'] == 5){ 
	 					Auth::login($user, $remember);
	 					//print_r(Auth::user()->id);die;
	 					$Message = "Login successfully";
						return redirect('admin/dashboard')->with('success',$Message);
		 			}else{

		 				$Message = "User email / password does not match";
		 				return redirect()->back()->with('error', $Message);
		 		
		 			}

		 			
	 			
	 			}else{
	 				
	 				$Message = "User email / password does not match";
		 			return redirect()->back()->with('error', $Message);
		 		}
 			}else{
	 				
	 			$Message = "User email / password does not match";
		 		return redirect()->back()->with('error', $Message);
		 	}
 			
 			
 		}
	} 
 


	public function dashboard(){

		if(session('UserId') == "" || empty(session('UserId'))){
			//echo 'sadasd'; exit;
            return redirect('/admin');
        }else{

        	$CrudRepository = new CrudRepository();
			$model 		= "App\User";
			// Customer Count
			$moduleType = 1;
			$Users = $CrudRepository->view($model,$moduleType);
			$user_total_count = $Users->total_count;
			//echo '<pre>'; print_r($user_total_count); exit;

			// Supplier Count
			/*$moduleType =  5;
			$Supplier_manage = $CrudRepository->view($model,$moduleType);
			$supplier_total_count = $Supplier_manage->total_count;*/
			
			// Partner Count
			$model1 		= "App\Models\Partner";
			$moduleType1 =  2; 
			$Patners = $CrudRepository->view($model1,$moduleType1);
			$patner_total_count = $Patners->total_count;

			// earning Count
			$model2 		= "App\User";
			$moduleType2 = 5;
			$Premium = $CrudRepository->view($model2,$moduleType2);
			$premium_user_total_count = $Premium->total_count;
			//print_r($data); exit;

			// Customer 
			$data =array();	
			$model 		= "App\User";
			$Chips 		= $CrudRepository->getdashboarduser($model,$data);
			//echo '<pre>';print_r($Chips); exit;
			$customerArr = array();
			$customerArr1 = array();
	        foreach ($Chips as $userskey => $usersvalue) {
	            $chatMonth = array();
	            $chatCustomer = array();
	            $chatMonth = $usersvalue->month?$usersvalue->month:'xx'; 
	            $chatCustomer = $usersvalue->customer; 

	            $customerArr[] = $chatMonth;
	            $customerArr1[] = $chatCustomer;
	        }

            //Supplier and  member



            $Chips1 		= $CrudRepository->getdashboardusersuplier($model,$data);
			//echo '<pre>';print_r($Chips1); exit;
			$supplierArr = array();
			$supplierArr1 = array();
	        foreach ($Chips1 as $userskey1 => $usersvalue1) {
	            $chatMonth1 = array();
	            $chatCustomer1 = array();
	            $chatMonth1 = $usersvalue1->month; 
	            $chatCustomer1 = $usersvalue1->customer; 

	            $supplierArr[] = $chatMonth1;
	            $supplierArr1[] = $chatCustomer1;
	        }

	        //Earning

	        /*$CrudRepository = new CrudRepository();
			$model 		= "App\Models\Order";
			$data =array();
			$Chips 		= $CrudRepository->getearning($model,$data);
			$charArr = array();
			$charArr1 = array();
			$months_arr = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			//echo '<pre>';print_r($Chips); exit;
			foreach ($months_arr as $key => $value) {
		        $chatMonth = array();
		        $chatCustomer = array();
			    foreach ($Chips as $userskey => $usersvalue) {
		            if($value ==  $usersvalue->month){
		            	$chatMonth = $usersvalue->month; 
		            	$chatCustomer = number_format($usersvalue->earnings,2); 
		        	}
		        }
		        if($value !=  @$usersvalue->month){
		        	$chatMonth = $value; 
		        }
		        $charArr[] = $chatMonth;
		        $charArr1[] = $chatCustomer;
		            
		    }*/
			return view('admin.dashboard',compact('user_total_count','patner_total_count','premium_user_total_count','customerArr','customerArr1','supplierArr','supplierArr1'));
		}
	}



	public function forgot(){

		return view('admin.forgot'); 
	}



	public function doforgot(Request $request){
		$rules = array('email'=>'required|email:rfc,dns',
 					'captcha'=>'required|captcha' );
 		
 		$validate = Validator::make($request->all(),$rules);

 		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		
 		}else{
			$CustomVerfication = new CustomVerfication();
			$UserRepostitory   = new UserRepository();
			$SendEmail = new SendEmails();
			$user   =  $UserRepostitory->check_unactive_user($request->all());
			//echo '<pre>'; print_r($user['email']); exit;
			if($user['email'] == 'trade@mailinator.com'){
				$code = $CustomVerfication->generateRandomNumber();
				$url =  url("activation/".$code);
				$message = $url;
				$update = $UserRepostitory->update_forgot_code($user->id,$code);

				$Send = $SendEmail->sendRestPasswordEmail($request->email,$message,$user->name,$user->id,$code);
				return redirect('admin/forgot')->with('success', 'Reset password email sent successfully.');
			}else{

				return redirect('admin/forgot')->with('error', 'Email does not exist !!');

			}
		}	
	}	

	public function resetPass(Request $request){
		$id = $request->id;
		$code = $request->code;   

		$UserRepostitory   = new UserRepository();
		$getuser = $UserRepostitory->getuserById($id);
		//echo '<pre>'; print_r($getuser); exit;
		$getCode = $getuser['reset_key'];
		$endTime = strtotime("+5 minutes",strtotime($getCode));
		$newTime = date('H:i:s',$endTime);
		
		if($getCode == $request->code){
			if(time() <= strtotime($newTime)){

				return view('admin.reset',compact('id','code')); 
			}else{
				return redirect('admin/')->with('error','Reset password link is expired.');
			}
		}else{
			
			return redirect('admin/')->with('error','Reset password link is expired.');
		}	
	}	

	public function doresetpass(Request $request){
		$UserRepostitory   = new UserRepository();
	
		/*$rules = array('newpassword' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', 
        'confirmpassword' => 'required_with:newpassword|same:newpassword|min:6');*/
        $rules = [
        'new_password' => [
            'required',
            'string',
            'min:6',             // must be at least 10 characters in length
            //'regex:/[a-z]/',      // must contain at least one lowercase letter
            /*'regex:/[A-Z]/', */     // must contain at least one uppercase letter
            //'regex:/[0-9]/',      // must contain at least one digit
            //'regex:/[@$!%*#?&]/', // must contain a special character
        ],
        'confirm_password'    => 'required_with:new_password|same:new_password',
    ];
 		$validate = Validator::make($request->all(),$rules);

 		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{

 			$getuser   =  $UserRepostitory->getuserById($request->userid);
			
 			if($request->code == $getuser['reset_key']){
 				
	 			if($request->confirm_password == $request->new_password){

	 				$Password = Hash::make($request->new_password);
	 				$arg['code'] = $request->code;
	 				$arg['id'] = 1;
	 				$arg['password'] = $request->new_password;
	 				$arg['email'] = @$getuser['email'];

	 				$user = $UserRepostitory->update_password($arg);

	 				return redirect('admin/')->with('success','Reset password successfully');
	 			
	 			}else{

					return redirect('activation/'.$request->userid.'/'.$request->code)->with('error', 'Confirm password not match with New Password!!');
	 			}
	 		}else{

				return redirect('admin/')->with('error', 'Activation link is expired !!');
	 		}	


		}	
	}


	public function activation(Request $request){
		//print_r($request->all());die;
        $id = $request->id;
        $code = $request->code;   

        $UserRepostitory   = new UserRepository();
        $getuser = $UserRepostitory->getuserById($id);
        $getCode = $getuser['activation_code'];
        $endTime = strtotime("+5 minutes",strtotime($getCode));
        $newTime = date('H:i:s',$endTime);
        if($getCode == $request->code){
        	$user = $UserRepostitory->update_activation($id);
            return redirect('/activation/'.$id.'/'.$code)->with('success', 'user activated!');
            
        }else{
            
            return redirect('admin/')->with('error','Activation link is expired. !! ');
        }   
    } 

	

	public function logout(Request $request){
    	Session::flush();
    	Artisan::call('cache:clear');
    	return redirect('admin/');
    }


	public function refreshCaptcha(){

		return response()->json(['captcha' => captcha_img()]);
	}

 }
