<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use App\Models\SubTradeManage;
use App\Models\TradeManage;
use App\Models\ChipData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;
use Illuminate\Pagination\Paginator;

use Charts;




class SubTradeController extends Controller{
	
	
	public function add(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\TradeManage";	
		$moduleType =  4; // TradeList
		$Trade_list = $CrudRepository->view($model,$moduleType);
		//$Trade_name = $SubTrades_manage[0]['TradeManage']['trade']?$SubTrades_manage[0]['TradeManage']['trade']:'';
		/*echo '<pre>';
		print_r($SubTrades_manage[0]['TradeManage']['trade']);die;*/
		//echo '<pre>';print_r($Trade_list); exit;
		return view('admin.SubTrade.add',compact('Trade_list'));

		
		
	}


	public function save(Request $request){

		$data = $request->all();
		//print_r($data); exit;
		$data['sub_trade'] = ucfirst($data['sub_trade']);
	    $rules = array('sub_trade' => 'required|unique:sub_trade_manage,sub_trade');
	   
 		$validate = Validator::make($data,$rules);
 		
 		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
 			
 			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
		
			$trade_name = $request->trade_id ?? null;
			$sub_trade = ucfirst($request->sub_trade) ?? null;
			$form_data 	= array(
							'trade_id' => $trade_name,
							'sub_trade' => $sub_trade,
							'created_at' => date('Y-m-d H:i:s'),
							'modify_at' => date('Y-m-d H:i:s'),
							);
			$model     =  "App\Models\SubTradeManage";	
			$Users 		=  $crudRepository->addsave($model,$form_data);
			$Message 	= "Added successfully";
			return redirect('admin/subtrade/view/'.@$trade_name)->with('success',$Message);
 		
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
		$model 		= "App\Models\SubTradeManage";	
		$moduleType =  3; 
		$id 		= $request->id;
		$SubTrades_manage = $CrudRepository->view($model,$moduleType,$id);

		
		$modal 		= "App\Models\TradeManage";
		$Trade_namer_data = $CrudRepository->getdetail($modal,$id);
		$Trade_name = $Trade_namer_data['trade'];
		//echo $id ; exit;
		$current_page = $request->page?$request->page:1;
		$total_count = $SubTrades_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		/*echo '<pre>';
		print_r($SubTrades_manage[0]['TradeManage']['trade']);die;*/
		return view('admin.SubTrade.view',compact('SubTrades_manage','Trade_name','current_page','total_count','row_count'));

	} 
    

    public function changestatus(Request $request){


    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\TradeManage"; 
		$id 		= $request->id;
		$form_data 	= array('block_status' => $request->status);
		$Changestatus = $CrudRepository->changestatus($model,$id,$form_data);
    		

	} 


	public function edit(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\TradeManage";
		$id 		= $request->id;
		$Users 		= $CrudRepository->edit($model,$id);
		return view('Admin.Users.edit',compact('Users'));
	
	}


	public function editsave(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Chip";
		$id 		= $request->id;
		$user 		= User::findorfail($id);

		$form_data 	= array(
						'first_name' => $request->firstname ? $request->firstname : $user->first_name,
						'last_name'  => $request->lastname 	? $request->lastname  : $user->last_name,
						'email' 	 => $request->email 	? $request->email 	  : $user->email,
						'user_status'=> $request->status 	? $request->status 	  : $user->user_status);

		$Users 		= $CrudRepository->editsave($model,$id,$form_data);
		$Message 	= "update successfully";
		return redirect('admin/chip/view')->with('success',$Message);
	
	}

	public function delete(Request $request){
		$CrudRepository = new CrudRepository();
		
		$model 		= "App\Models\ProductManage";	
		$moduleType =  12; 
		$id 		= $request->id;
		$Product_manage = $CrudRepository->view($model,$moduleType,$id);
		//echo '<pre>'; print_r($Product_manage); exit;
		$total_count = $Product_manage->total_count;
		if($total_count == 0 ){

			$model     =  "App\Models\SubTradeManage";	
			$id 		= $request->id;

			$SubTrades_manage = $CrudRepository->getdetail($model,$id);
			//echo '<pre>'; print_r(); exit;
			$trad_id = $SubTrades_manage['trade_id'];
			$Users 		= $CrudRepository->dodelete($model,$id);
			$Message 	= "Delete successfully";
			return redirect('admin/subtrade/view/'.@$trad_id)->with('error',$Message);
		}else{
			$Message 	= "Sorry, this sub trade cannot be deleted. It is associated with multiple products.";
			//return redirect('admin/trade/view')->with('success',$Message);*/
			return 0;
		}

	}


	public function chipdetail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\ChipData";
		$id 		= $request->id;
		$Chips 		= $CrudRepository->getdetail($model,$id);

		return view('Admin.Chips.chip_detail',compact('Chips'));
	}

	public function search(Request $request){
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\SubTradeManage"; 
		$moduleType =  3; 
		$id 		= $request->id;

		$SubTrades_manage = $CrudRepository->search($model, $moduleType, $request->search,$request->trade_id);
		//echo '<pre>'; print_r($request); exit;
		$Trade_name = $SubTrades_manage[0]['TradeManage']['trade']?$SubTrades_manage[0]['TradeManage']['trade']:'';
		$current_page = $request->page?$request->page:1;
		$total_count = $SubTrades_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		
		return view('admin.SubTrade.search',compact('SubTrades_manage','Trade_name','current_page','total_count','row_count'));
		//return view('admin.SubTrade.search',compact('Trades_manage','current_page','total_count'));
    }



}
