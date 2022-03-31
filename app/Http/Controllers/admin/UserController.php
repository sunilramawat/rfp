<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;
use App\Http\Controllers\Repository\UserRepository;
use Illuminate\Pagination\Paginator;

class UserController extends Controller{
	
	public function view(Request $request){

		$CrudRepository = new CrudRepository();
		//$model 		= "App\User";	
		$model 		= "App\User";
		$moduleType = 1; 
		$data =array();
		if($request->from_date != ''){
			$data['fromDate'] = $request->from_date;
			$data['toDate'] = $request->to_date;
			//$Supplier_manage = $CrudRepository->search($model,$moduleType,$data);
		//print_r($data); exit;
		}
		$Users = $CrudRepository->view($model,$moduleType,$data);
		//echo '<pre>'; print_r($Users); exit;
		$current_page = $request->page?$request->page:1;
		$total_count = $Users->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		return view('admin.User.view',compact('Users','current_page','total_count','row_count'));
	} 
    

    public function changestatus(Request $request){
    	//print_r($request->status); exit;

    	$CrudRepository = new CrudRepository();
		$model 		= "App\User"; 
		$id 		= $request->id;
		$form_data 	= array('user_status' => $request->status,
							'isdelete' => 0,
		);
		
		//print_r($form_data); exit;
		$Changestatus = $CrudRepository->changestatus($model,$id,$form_data);
    		

	} 


	public function edit(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		$Users 		= $CrudRepository->edit($model,$id);
		return view('Admin.Users.edit',compact('Users'));
	
	}


	public function editsave(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		$user 		= User::findorfail($id);

		$form_data 	= array(
						'first_name' => $request->firstname ? $request->firstname : $user->first_name,
						'last_name'  => $request->lastname 	? $request->lastname  : $user->last_name,
						'email' 	 => $request->email 	? $request->email 	  : $user->email,
						'user_status'=> $request->status 	? $request->status 	  : $user->user_status);

		$Users 		= $CrudRepository->editsave($model,$id,$form_data);
		$Message 	= "update successfully";
		return redirect('admin/user/view')->with('success',$Message);
	
	}

	public function delete(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		//echo $id; exit;
		$Users 		= $CrudRepository->dodelete($model,$id);
		
		$Message 	= "Delete successfully";
		return redirect('admin/user/view')->with('error',$Message);

	}

	public function detail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		$Users 		= $CrudRepository->getdetail($model,$id);
		
		// order list
		$model 		= "App\Models\Order";
		$moduleType =  7; 
		$data =array();
		if($request->from_date != ''){
			$data['fromDate'] =$request->from_date;
			$data['toDate'] =$request->to_date;
			
		}
		if($request->selected_date != ''){
			//.print_r($request->selected_date); exit;
			$data['selected_date'] =$request->selected_date;
		}
		$data['customerId'] = $id;
		//$Supplier_manage = $CrudRepository->view($model,$moduleType,$data);
		
		//echo '<pre>';  print_r($Users['id']); exit;


		return view('admin.User.detail',compact('Users'));
	}

	public function search(Request $request){
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\UserAuthority";
		$moduleType = 1; 
		$Users = $CrudRepository->search($model,$moduleType,$request->search);

		$current_page = $request->page?$request->page:1;
		$total_count = $Users->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		return view('admin.User.search',compact('Users','current_page','total_count','row_count'));
    }

    public function customer(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$data['from_date'] = '';
		$Chips 		= $CrudRepository->getdetailuser($model,$data);
		//echo '<pre>';print_r($Chips); exit;
		 $charArr = array();
		 $charArr1 = array();
        foreach ($Chips as $userskey => $usersvalue) {
            $chatMonth = array();
            $chatCustomer = array();
            $chatMonth = $usersvalue->month?$usersvalue->month:'Jan'; 
            $chatCustomer = $usersvalue->customer; 

            $charArr[] = $chatMonth;
            $charArr1[] = $chatCustomer;
            # code...
        }
		//echo '<pre>';print_r(json_encode($charArr)); exit;
		return view('admin.User.customer',compact('charArr','charArr1'));
	}


	

}
