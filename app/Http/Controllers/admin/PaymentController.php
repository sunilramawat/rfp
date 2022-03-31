<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;
use Illuminate\Pagination\Paginator;

use Charts;




class PaymentController extends Controller{
	
	
	public function add(Request $request){

		return view('admin.Order.add');
	}


	public function save(Request $request){

		$data = $request->all();
	    $rules = array('trade_name' => 'required');
 		$validate = Validator::make($data,$rules);
 		
 		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
 			
 			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
		
			$trade_name = $request->trade_name ?? null;
			$trade_logo = $request->trade_logo ?? null;
			$form_data 	= array(
							'trade' => $trade_name,
							'trade_logo' => $trade_logo,
							'block_status' =>false,
							'created_at' => date('Y-m-d H:i:s'),
							'modify_at' => date('Y-m-d H:i:s'),
							);
			$model     =  "App\Models\TradeManage";	
			$Users 		=  $crudRepository->addsave($model,$form_data);
			$Message 	= "Added successfully";
			return redirect('admin/trade/view')->with('success',$Message);
 		
		   /* $Check = $companyservices->companysave($data);

		    if($Check->error_code == 200){
		    	$Message = "successfully added";
				return redirect('admin/Trade/view')->with('success',$Message);
		    }else{
		    	$Message = "something went wrong";
				return redirect()->back()->with('error', $Message);
		    }*/
		}    
	} 

	public function view(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order";
		$moduleType =  7; 
		$data =array();
		//if($request->from_date != ''){
			$data['fromDate'] =$request->from_date;
			$data['toDate'] =$request->to_date;
			$data['supplier'] =$request->supplier;
			$data['order_status'] =$request->order_status;
			//echo '<pre>';  print_r($data); exit;
			//$Supplier_manage = $CrudRepository->search($model,$moduleType,$data);
		//}
		$Supplier_manage = $CrudRepository->view($model,$moduleType,$data);
		//echo '<pre>';  print_r($Supplier_manage); exit;
		$current_page = $request->page?$request->page:1;
		$total_count = $Supplier_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}

		$model 		= "App\Models\UserAuthority";	
		$moduleType =  5; // SupplierList
		$Supplier_list = $CrudRepository->list($model,$moduleType);

		//$Trades_manage = $Trades_manage::paginate(5);

		/*echo '<pre>'; print_r($Supplier_list);die;*/
		return view('admin.Payment.view',compact('Supplier_manage','current_page','total_count','row_count','Supplier_list'));

	} 
    
    public function search(Request $request){
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order"; 
		$Trades_manage = $CrudRepository->search($model,$request->search);

		$current_page = $request->page?$request->page:1;
		$total_count = $Trades_manage->total_count;

		return view('admin.Trade.search',compact('Trades_manage','current_page','total_count'));
    }

    public function changestatus(Request $request){
    	//echo 'sdada'; exit;
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order"; 
		$id 		= $request->id;
		if($request->status == 1){
			$request->status = true;
		}else{
			$request->status = false;
		}
		$form_data 	= array('is_block' => $request->status );
		//echo '<pre>';  print_r($form_data); exit;
		$Changestatus = $CrudRepository->changestatus($model,$id,$form_data);
    		

	} 


	public function edit(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order";
		$id 		= $request->id;
		$Supplier 		= $CrudRepository->edit($model,$id);
		//echo '<pre>'; print_r($Supplier);exit;
		return view('admin.Supplier.edit',compact('Supplier'));
	
	}


	public function editsave(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order";
		$id 		= $request->id;
		$user 		= User::findorfail($id);
		
		$form_data 	= array(
		'business_name' => $request->business_name ? $request->business_name : $user->business_name,
		'email'  => $request->email ? $request->email : $user->email,
		'phone_code' 	 => $request->phone_code ? $request->phone_code : $user->phone_code,
		'phone_number' 	 => $request->phone_number ? $request->phone_number : $user->phone_number,
		'business_identification_number' => $request->business_identification_number ? $request->business_identification_number : $user->business_identification_number,
		'address' 	 => $request->address ? $request->address: $user->address,
		'is_block'=> $request->is_block ? $request->is_block : $user->is_block);

		$Users 		= $CrudRepository->editsave($model,$id,$form_data);
		$Message 	= "update successfully";
		return redirect('admin/supplier/view')->with('success',$Message);
	
	}

	public function delete(Request $request){

		$CrudRepository = new CrudRepository();
		$model     =  "App\Models\Order";	
		$id 		= $request->id;
		$Users 		= $CrudRepository->dodelete($model,$id);
		$Message 	= "Delete successfully";
		return redirect('admin/trade/view')->with('error',$Message);

	}


	public function detail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order";
		$id 		= $request->id;
		$Supplier 		= $CrudRepository->orderdetail($model,$id);
		//echo '<pre>'; print_r($Supplier); exit;
		return view('admin.Payment.detail',compact('Supplier'));
		//return view('Admin.Chips.chip_detail',compact('Chips'));
	}



	public function order(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Order";
		$moduleType =  7; 
		$Supplier_manage = $CrudRepository->view($model,$moduleType);
		//echo '<pre>';  print_r($Supplier_manage); exit;
		$current_page = $request->page?$request->page:1;
		$total_count = $Supplier_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}

		//$Trades_manage = $Trades_manage::paginate(5);

		/*echo '<pre>';
		print_r($Trades_manage);die;*/
		return view('admin.Payment.order',compact('Supplier_manage','current_page','total_count','row_count'));

	} 

}
