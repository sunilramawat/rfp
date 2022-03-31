<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use App\Models\TradeManage;
use App\Models\ChipData;
use App\Models\Partner;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Gender;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;
use Illuminate\Pagination\Paginator;
use Charts;
use DB;



class PartnerController extends Controller{
		
	public function add(Request $request){
		$CrudRepository = new CrudRepository();
		
		$model 		= "App\Models\Gender";	
		$moduleType =  3; 
		$gender = $CrudRepository->view($model,$moduleType);
		
		$model1 		= "App\Models\Categories";	
		$moduleType1 =  4; 
		$category = $CrudRepository->view($model1,$moduleType1);
		//echo '<pre>'; print_r($category); exit;
		$model 		= "App\Models\SubCategories";	
		$moduleType =  8; 
		$subcategories = $CrudRepository->view($model,$moduleType);
		
		
		$model 		= "App\Models\PartnerType";	
		$moduleType =  16; 
		$eventType = $CrudRepository->view($model,$moduleType);


		$model3		= "App\Models\Region";	
		$moduleType =  18; 
		$region = $CrudRepository->view($model3,$moduleType);
		//dd($eventType); exit;
		//$gender = Gender::where('status',1);
		//echo '<pre>'; print_r($region); exit;
		return view('admin.Partner.add',compact('category','subcategories','eventType','region'));
	}
	public function subCat(Request $request)
    {
        $parent_id = $request->cat_id; 
        $subcategories =DB::table('sub_categories')->select('sc_id','sc_name')
        	->where('sc_c_id',$parent_id)
        	->get();                    
       // print_r($subcategories); exit;
        return response()->json([
            'subcategories' => $subcategories
        ]);
        
    }
	public function save(Request $request){

		$data = $request->all();
		//echo '<pre>';	print_r($data); exit;
		$rules = array(
                    'name'         =>  'required',
                    'desc'      =>  'required',
                    'region' => "required|in:1,2,3,4,5",
                    'type_text'      =>  'required',
                    'location'      =>  'required',
                    'opening' => 'nullable',
            		'closing' => 'nullable',
                    'suitable'      =>  'required|in:1,2,3',
                    'event_type'      =>  'required|in:1,2,3,4',
                );

        $validate = Validator::make($data,$rules);
		$filename = time();
		if (@$data['image'] != "") {
			//echo 'das'; exit;
			$extension = $data['image']->getClientOriginalExtension();
			if(strtolower($extension) == 'jpg' || strtolower($extension) == 'png' || strtolower($extension) == 'jpeg' ) {
				
				$FileLogo = $filename . '.' .$data['image']->getClientOriginalExtension();
				$destinationPath = 'public/images';
				$data['image']->move($destinationPath, $FileLogo);
				$documentFile = $destinationPath . '/' . $FileLogo;
				$upload = $FileLogo;
			}
		}		

		
 		//echo '<pre>'; print_r($upload); exit;
	   
 		//echo '<pre>'; print_r($validate); exit;
 		
 		if($validate->fails()){ 
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
 			//echo 'dasda'; exit;
 			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
			if($request->is_premium == true){
				$request->is_premium = 1;
			}else{
				$request->is_premium = 0;
			}
			if($request->status == true){
				$request->status = 1;
			}else{
				$request->status = 0;
			}

			if($request->is_discount == true){
				$request->is_discount = 1;
			}else{
				$request->is_discount = 0;
			}

			if($request->is_recommend == true){
				$request->is_recommend = 1;
			}else{
				$request->is_recommend = 0;
			}
			//$trade_logo = $supp_photo?@$supp_photo: null;
	
   			$form_data 	= array(
							'name' => $request->name ?? null,
							'photo' => $upload ?? null,
							'desc' => $request->desc ?? null,
							'type_text' => $request->type_text ?? null,
							'region' => $request->region ?? null,
							'location' => $request->location ?? null,
							'opening' => $request->opening ?? null,
							'closing' => $request->closing ?? null,
							'suitable' => $request->suitable ?? null,
							'event_type' => $request->event_type ?? null,
							'category' => $request->category ?? null,
							'sub_category' => $request->subcategory ?? null,
							'is_premium' => $request->is_premium ?? null,
							'status' => $request->status ?? null,
							'is_discount' => $request->is_premium ?? null,
							'is_recommend' => $request->is_recommend ?? null,
							'promo_code' => $request->promo_code ?? null,
							'promo_detail' => $request->promo_detail ?? null,
							'event_type' => $request->event_type ?? null,
							'type' => $request->event_type ?? null,
							'created_at' => date('Y-m-d H:i:s'),
							'modify_at' => date('Y-m-d H:i:s'),
							);
			//echo '<pre>'; print_r($form_data); exit;
			//echo rand(); exit;
			$model     =  "App\Models\Partner";	
			$Users 		=  $crudRepository->addsave($model,$form_data);
			$Message 	= "Added successfully";
			return redirect('admin/partner/view')->with('success',$Message);
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
		$model 		= "App\Models\Partner";	
		$moduleType =  2; 
		$Partner = $CrudRepository->view($model,$moduleType);
		$current_page = $request->page?$request->page:1;
		$total_count = $Partner->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		//$Trades_manage = $Trades_manage::paginate(5);

		/*echo '<pre>';
		print_r($Trades_manage);die;*/
		return view('admin.Partner.view',compact('Partner'));

	} 
    
   
    public function changestatus(Request $request){
    	//print_r($request->status); exit;

    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Partner"; 
		$id 		= $request->id;
		$form_data 	= array('status' => $request->status);
		//print_r($form_data); exit;
		$Changestatus = $CrudRepository->changestatus($model,$id,$form_data);
    		

	} 


	public function edit(Request $request){
		
		$CrudRepository = new CrudRepository();
		
		$model 		= "App\Models\Gender";	
		$moduleType =  3; 
		$gender = $CrudRepository->view($model,$moduleType);
		
		$model1 		= "App\Models\Categories";	
		$moduleType1 =  4; 
		$category = $CrudRepository->view($model1,$moduleType1);
		//echo '<pre>'; print_r($category); exit;
		$model 		= "App\Models\SubCategories";	
		$moduleType =  8; 
		$subcategories = $CrudRepository->view($model,$moduleType);
		
		
		$model 		= "App\Models\PartnerType";	
		$moduleType =  16; 
		$eventType = $CrudRepository->view($model,$moduleType);


		$model3		= "App\Models\Region";	
		$moduleType =  18; 
		$region = $CrudRepository->view($model3,$moduleType);
		//dd($eventType); exit;
		//$gender = Gender::where('status',1);
		//echo '<pre>'; print_r($region); exit;
		
		$model 		= "App\Models\Partner";
		$id 		= $request->id;
		$Partner 		= $CrudRepository->edit($model,$id);
		//echo '<pre>'; print_r($Partner); exit;
		return view('admin.Partner.edit',compact('Partner','category','subcategories','eventType','region'));
	
	}


	public function editsave(Request $request){
		//dd($request); exit;
		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Partner";
		$id 		= $request->id;
		$user 		= $model::findorfail($id);
		$data = $request->all();
		//echo '<pre>';	dd($request); exit;
		$rules = array(
                    'name'         =>  'required',
                    'desc'      =>  'required',
                    'region' => "required|in:1,2,3,4,5",
                    'type_text'      =>  'required',
                    'location'      =>  'required',
                    'opening' => 'nullable',
            		'closing' => 'nullable',
                    'suitable'      =>  'required|in:1,2,3',
                    'event_type'      =>  'required|in:1,2,3,4',
                );

        $validate = Validator::make($data,$rules);
		$filename = time();
		if (@$data['image'] != "") {
			//echo 'das'; exit;
			$extension = $data['image']->getClientOriginalExtension();
			if(strtolower($extension) == 'jpg' || strtolower($extension) == 'png' || strtolower($extension) == 'jpeg' ) {
				
				$FileLogo = $filename . '.' .$data['image']->getClientOriginalExtension();
				$destinationPath = 'public/images';
				$data['image']->move($destinationPath, $FileLogo);
				$documentFile = $destinationPath . '/' . $FileLogo;
				$upload = $FileLogo;
			}
		}else{
			$upload =  $user->photo;
		}		

		
 		//echo '<pre>'; print_r($upload); exit;
	   
 		//echo '<pre>'; print_r($validate); exit;
 		
 		if($validate->fails()){ 
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
 			//echo 'dasda'; exit;
 			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
			if($request->is_premium == "true" ){
				$request->is_premium = 1;
			}else if($request->is_premium == "false"){
				$request->is_premium = 0;
			}else{
				if($request->is_premium == null){
					//echo '1121';
					$request->is_premium = $user->is_premium;
				}else{
					$request->is_premium = 0;
				}
			}
			if($request->status == "true"){
				$request->status = 1;
			}else if($request->status == "false"){
				$request->status = 0;
			}else{
				if($request->status == null){
					$request->status = $user->status;
				}else{
					$request->status = 0;
				}

			}

			if($request->is_discount == "true"){
				$request->is_discount = 1;
			}else if($request->is_discount == "false"){
				$request->is_discount = 0;
			}else{
				if($request->is_discount == null){
					$request->is_discount = $user->is_discount;
				}else{
					$request->is_discount = 0;
				}

			}

			if($request->is_recommend == "true"){
				$request->is_recommend = 1;
			}else if($request->is_recommend == "false"){
				$request->is_recommend = 0;
			}else{
				if($request->is_recommend == null){
					$request->is_recommend = $user->is_recommend;
				}else{
					$request->is_recommend = 0;
				}

			}

		
			//$trade_logo = $supp_photo?@$supp_photo: null;
	
   			$form_data 	= array(
							'id' => $request->id ?? $user->id,
							'name' => $request->name ?? $user->name,
							'photo' => $upload ?? null,
							'desc' => $request->desc ?? $user->desc,
							'type_text' => $request->type_text ?? $user->type_text,
							'region' => $request->region ?? $user->region,
							'location' => $request->location ?? $user->location,
							'opening' => $request->opening ?? $user->opening,
							'closing' => $request->closing ?? $user->closing,
							'suitable' => $request->suitable ?? $user->suitable,
							'event_type' => $request->event_type ?? $user->event_type,
							'category' => $request->category ?? $user->category,
							'sub_category' => $request->subcategory ?? $user->sub_category,
							'is_premium' => $request->is_premium ?? $user->is_premium,
							'status' => $request->status ?? $user->status ,
							'is_discount' => $request->is_premium ?? $user->is_premium,
							'is_recommend' => $request->is_recommend ?? $user->is_recommend ,
							'promo_code' => $request->promo_code ?? $user->promo_code,
							'promo_detail' => $request->promo_detail ?? $user->promo_detail,
							'event_type' => $request->event_type ?? $user->event_type,
							'type' => $request->event_type ?? $user->event_type,
							);
			//echo '<pre>'; print_r($form_data); exit;
			//echo rand(); exit;
			$model     =  "App\Models\Partner";	
			$Users 		= $CrudRepository->editsave($model,$id,$form_data);
			//$Users 		=  $crudRepository->addsave($model,$form_data);
			$Message 	= "Updated successfully";
			return redirect('admin/partner/view')->with('success',$Message);
 		}
	
	}

	public function delete(Request $request){

		$CrudRepository = new CrudRepository();
			$model     =  "App\Models\Partner";	
			$id 		= $request->id;
			$Users 		= $CrudRepository->harddelete($model,$id);
			$Message 	= "Delete successfully";
			return redirect('admin/partner/view')->with('error',$Message);
		
	}


	public function detail(Request $request){

	
		$CrudRepository = new CrudRepository();
		
		$model 		= "App\Models\Gender";	
		$moduleType =  3; 
		$gender = $CrudRepository->view($model,$moduleType);
		
		$model1 		= "App\Models\Categories";	
		$moduleType1 =  4; 
		$category = $CrudRepository->view($model1,$moduleType1);
		//echo '<pre>'; print_r($category); exit;
		$model 		= "App\Models\SubCategories";	
		$moduleType =  8; 
		$subcategories = $CrudRepository->view($model,$moduleType);
		
		
		$model 		= "App\Models\PartnerType";	
		$moduleType =  16; 
		$eventType = $CrudRepository->view($model,$moduleType);


		$model3		= "App\Models\Region";	
		$moduleType =  18; 
		$region = $CrudRepository->view($model3,$moduleType);
		//dd($eventType); exit;
		//$gender = Gender::where('status',1);
		//echo '<pre>'; print_r($region); exit;
		
		$model 		= "App\Models\Partner";
		$id 		= $request->id;
		$Partner 		= $CrudRepository->edit($model,$id);
		//echo '<pre>'; print_r($Partner); exit;
		return view('admin.Partner.detail',compact('Partner','category','subcategories','eventType','region'));
	
	}



}
