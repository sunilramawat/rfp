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


class FaqController extends Controller{
		
	public function add(Request $request){


		return view('admin.Faq.add');
	}

	public function save(Request $request){

		$data = $request->all();
		//dd($data); exit;
		//$data['question'] = ucfirst($data['question']);
		//$data['answer'] = ucfirst($data['answer']);
		//echo '<pre>';  print_r($data); exit;
		$rules = array(
                    'question'         =>  'required|max:45',
                    'answer'      =>  'required|max:45',
                    );

		$validate = Validator::make($data,$rules);
		//echo '<pre>';	print_r($validate->fails()); exit;
		if($validate->fails()){ 		
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
			$crudRepository = new CrudRepository();
			//$modelName 		= "App\Models\TradeManage";	
		
			$question = ucfirst($request->question) ?? null;
			//$answer = ucfirst($request->answer) ?? null;
			$form_data 	= array(
							'question' => $question,
							'answer' => $request->answer,
							'f_status' =>1,
							);
			/*print_r($form_data); 
			echo rand(); exit;*/
			$model     =  "App\Models\Faq";	
			$Users 		=  $crudRepository->faqaddsave($model,$form_data);
			$Message 	= "Added successfully";
			return redirect('admin/faq/view')->with('success',$Message);
 		
 			
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
		$model 		= "App\Models\Faq";	
		$moduleType =  13; 
		$Faq = $CrudRepository->view($model,$moduleType);
		$current_page = $request->page?$request->page:1;
		$total_count = $Faq->total_count;
		if($total_count<10){
			$row_count = $total_count;	
		}else{
			$row_count = 10;
		}
		//$Trades_manage = $Trades_manage::paginate(5);

		/*echo '<pre>';
		print_r($Trades_manage);die;*/
		return view('admin.Faq.view',compact('Faq','current_page','total_count','row_count'));

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
		$model 		= "App\Models\Faq";
		$id 		= $request->id;
		$Faq 		= $CrudRepository->edit($model,$id);
		return view('admin.Faq.edit',compact('Faq'));
	
	}


	public function editsave(Request $request){
		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Faq";
		$id 		= $request->id;
		$user 		= $model::findorfail($id);

		$form_data 	= array(
						'question' => $request->question ? $request->question : $user->question,
						'answer'  => $request->answer ? $request->answer  : $user->answer);
	
		$Users 		= $CrudRepository->editsave($model,$id,$form_data);
		$Message 	= "update successfully";
		return redirect('admin/faq/view')->with('success',$Message);
	
	}

	public function delete(Request $request){

		$CrudRepository = new CrudRepository();
		
		$model 		= "App\Models\Faq";	
		$model1 		= "App\Models\Answer";	
		$id 		= $request->id;
		$Users 		= $CrudRepository->harddeletefaq($model,$model1,$id);
		//echo '<pre>'; print_r($Product_manage); exit;
		$Message 	= "Deleted successfully";
		return redirect('admin/faq/view')->with('success',$Message);
	

		

	}


	public function chipdetail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\ChipData";
		$id 		= $request->id;
		$Chips 		= $CrudRepository->getdetail($model,$id);

		return view('Admin.Chips.chip_detail',compact('Chips'));
	}



}
