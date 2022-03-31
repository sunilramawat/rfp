<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
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


class TradeController extends Controller{
		
	public function add(Request $request){

		return view('admin.Trade.add');
	}

	public function save(Request $request){

		$data = $request->all();
		$data['trade_name'] = ucfirst($data['trade_name']);
		//echo '<pre>';  print_r($data); exit;
		$rules = array('trade_name' => 'required|unique:trade_manage,trade');
 		$validate = Validator::make($data,$rules);
		//echo '<pre>';	print_r($validate->fails()); exit;
		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
			if(!empty($_FILES['image']['name'])){
			//echo 'dasd'; exit;
			//$image_info = getimagesize($_FILES['image']['name']);
			//print_r($image_info);	
			$filename = $_FILES['image']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$photo =  curl_file_create($_FILES['image']['tmp_name']);
			$photo->postname = rand().'.'.$ext;
			$data	= array('id'=> 1,
 						'moduleName' => 'TRADE',
 						'image'=>$photo);
 			//echo '<pre>';print_r($_FILES['image']); exit;
 			$url 	= "http://3.139.177.20:9090/services/productsuppliercustomer/api/admin/update-images";
 			$method = "PUT";
		 	
 			$curl = curl_init();

	        curl_setopt_array($curl, array(
	            CURLOPT_URL => $url,
	            CURLOPT_POST=>1,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_ENCODING => "",
	            CURLOPT_MAXREDIRS => 10,
	            CURLOPT_TIMEOUT => 30000,
	            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	            CURLOPT_CUSTOMREQUEST => $method,
	            CURLOPT_POSTFIELDS => $data,
	            CURLOPT_HTTPHEADER => array(
	                // Set here requred headers
	                "accept: */*",
	                "accept-language: en-US,en;q=0.8",
	                "content-type: multipart/form-data",
	            ),
	        ));

	        $response = curl_exec($curl);
			//echo '<pre>';print_r($response); exit;
	        $err = curl_error($curl);

	        curl_close($curl);

	        if ($err) {
	            return "cURL Error #:" . $err;
	        } else {
	          	$img = json_decode($response);
 				@$supp_photo = @$img->image;
	        }
 			//$result	= $this->curl($URL,$Method,$data);
 			}
	 		//echo '<pre>'; print_r($supp_photo ); exit;
		   
	 		/*echo '<pre>'; print_r($validate); exit;
	 		
	 		
	 		*/	//echo 'dasda'; exit;
 			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
		
			$trade_name = ucfirst($request->trade_name) ?? null;
			$trade_logo = @$supp_photo?@$supp_photo: null;
			$form_data 	= array(
							'trade' => $trade_name,
							'trade_logo' => $trade_logo,
							'block_status' =>false,
							'created_at' => date('Y-m-d H:i:s'),
							'modify_at' => date('Y-m-d H:i:s'),
							);
			/*print_r($form_data); 
			echo rand(); exit;*/
			$model     =  "App\Models\TradeManage";	
			$Users 		=  $crudRepository->addsave($model,$form_data);
			$Message 	= "Added successfully";
			return redirect('admin/trade/view')->with('success',$Message);
 		
 			
 		}
		   /* $Check = $companyservices->companysave($data);

		    if($Check->error_code == 200){
		    	$Message = "successfully added";
				return redirect('admin/Trade/view')->with('success',$Message);
		    }else{
		    	$Message = "something went wrong";
				return redirect()->back()->with('error', $Message);
		    }*/
		//}    
	} 

	public function view(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\TradeManage";	
		$moduleType =  2; 
		$Trades_manage = $CrudRepository->view($model,$moduleType);
		$current_page = $request->page?$request->page:1;
		$total_count = $Trades_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		//$Trades_manage = $Trades_manage::paginate(5);

		/*echo '<pre>';
		print_r($Trades_manage);die;*/
		return view('admin.Trade.view',compact('Trades_manage','current_page','total_count','row_count'));

	} 
    
    public function search(Request $request){
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\TradeManage"; 
		$moduleType =  2; 
		$Trades_manage = $CrudRepository->search($model,$moduleType,$request->search);

		$current_page = $request->page?$request->page:1;
		$total_count = $Trades_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		return view('admin.Trade.search',compact('Trades_manage','current_page','total_count','row_count'));
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
		$moduleType =  9; 
		$id 		= $request->id;
		$Product_manage = $CrudRepository->view($model,$moduleType,$id);
		//echo '<pre>'; print_r($Product_manage); exit;
		$total_count = $Product_manage->total_count;
		if($total_count == 0 ){
			$model     =  "App\Models\TradeManage";	
			$id 		= $request->id;
			$Users 		= $CrudRepository->dodelete($model,$id);
			$Message 	= "Delete successfully";
			return redirect('admin/trade/view')->with('error',$Message);
		}else{
			$Message 	= "Sorry, this trade cannot be deleted. It is associated with multiple products.";
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



}
