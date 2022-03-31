<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use App\Models\SupplierManage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;
use Illuminate\Pagination\Paginator;

use Charts;




class SupplierController extends Controller{
	
	
	public function add(Request $request){

		return view('admin.Supplier.add');
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
		$model 		= "App\Models\UserAuthority";
		$moduleType =  5;
		//echo '<pre>';  print_r($request->fromDate); exit;
		$data =array();
		if($request->from_date != ''){
			$data['fromDate'] =$request->from_date;
			$data['toDate'] =$request->to_date;
			//$Supplier_manage = $CrudRepository->search($model,$moduleType,$data);
		}
		//print_r($request->from_date); exit;
		$Supplier_manage = $CrudRepository->view($model,$moduleType,$data);
		
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
		return view('admin.Supplier.view',compact('Supplier_manage','current_page','total_count','row_count'));

	} 
    
    public function search(Request $request){
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\UserAuthority";
		$moduleType =  5; 
		//print_r($request->from_date); exit;
		$data =array();
		$data['fromDate'] =$request->from_date;
		$data['toDate'] =$request->to_date;
		$Supplier_manage = $CrudRepository->search($model,$moduleType,$data);

		$current_page = $request->page?$request->page:1;
		$total_count = $Supplier_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		return view('admin.Supplier.search',compact('Supplier_manage','current_page','total_count','row_count'));
    }

    public function changestatus(Request $request){
    	//echo 'sdada'; exit;
    	$CrudRepository = new CrudRepository();
		$model 		= "App\User"; 
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
		$model 		= "App\User";
		$id 		= $request->id;
		$Supplier 		= $CrudRepository->edit($model,$id);
		//echo '<pre>'; print_r($Supplier);exit;
		return view('admin.Supplier.edit',compact('Supplier'));
	
	}


	public function editsave(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		$user 		= User::findorfail($id);
		$arg 	= $request->all();
		if(!empty($request->image)){
			$filename = $_FILES['image']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$photo =  curl_file_create($_FILES['image']['tmp_name']);
			$photo->postname = rand().'.'.$ext;
			//print_r($request->id); exit;
			$data	= array('id'=> 3,
 						'moduleName' => 'SUPPLIER',
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
 				$supp_photo = $img->image;
	        }
 			//$result	= $this->curl($URL,$Method,$data);
 		}
		$phone = explode(" ",$request->phone_number);
		$phone[0] = str_replace("+","",$phone[0]);
		//print_r ($phone[0]);exit;
		$form_data 	= array(
		'business_name' => $request->business_name ? $request->business_name : $user->business_name,
		'business_logo_url' => @$supp_photo?$supp_photo:$user->business_logo_url,
		'email'  => $request->email ? $request->email : $user->email,
		'phone_code' 	 => $request->phone_number ? $phone[0] : $user->phone_code,
		'phone_number' 	 => $request->phone_number ? $phone[1] : $user->phone_number,
		'business_identification_number' => $request->business_identification_number ? $request->business_identification_number : $user->business_identification_number,
		'address' 	 => $request->address ? $request->address: $user->address,
		'is_block'=> $request->is_block ? $request->is_block : $user->is_block);

		$Users 		= $CrudRepository->editsave($model,$id,$form_data);
		$Message 	= "update successfully";
		return redirect('admin/supplier/view')->with('success',$Message);
	
	}

	public function delete(Request $request){

		$CrudRepository = new CrudRepository();
		$model     =  "App\Models\TradeManage";	
		$id 		= $request->id;
		$Users 		= $CrudRepository->dodelete($model,$id);
		$Message 	= "Delete successfully";
		return redirect('admin/trade/view')->with('error',$Message);

	}


	public function detail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\User";
		$id 		= $request->id;
		$Supplier 		= $CrudRepository->getdetail($model,$id);
		//echo '<pre>'; print_r($Supplier[0]['supplier_name']); exit;
		return view('admin.Supplier.detail',compact('Supplier'));
		//return view('Admin.Chips.chip_detail',compact('Chips'));
	}

	public function product(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\ProductManage";	
		$moduleType =  8; 
		$id 		= $request->id;
		$Product_manage = $CrudRepository->view($model,$moduleType,$id);
		$modal 		= "App\User";
		//echo '<pre>'; print_r($Product_manage); exit;
		$Product_user_data = $CrudRepository->getdetail($modal,$id);
		$Product_name = $Product_user_data['business_name'];
		//print_r($id);exit;
		//echo $Trade_name ; exit;
		$current_page = $request->page?$request->page:1;
		$total_count = $Product_manage->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		//echo '<pre>';print_r($Product_manage);die;
		return view('admin.Supplier.product',compact('Product_manage','Product_name','current_page','total_count','row_count'));
	}

	public function productstatus(Request $request){
    	//echo '<pre>'; print_r($request->status); exit;
    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\ProductManage";	 
		$id 		= $request->id;
		if($request->status == 1){
			$request->status = true;
		}else{
			$request->status = false;
		}
		$form_data 	= array('block_status' => $request->status );
		//echo '<pre>';  print_r($id); exit;
		$Productstatus = $CrudRepository->productstatus($model,$id,$form_data);
   
	} 
}
