<?php

namespace App\Http\Controllers\admin;

use Route;
use Auth;
use Validator;
use App\User;
use App\Models\Chip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\CustomVerfication;
use App\Http\Controllers\Utility\SendEmails;
use App\Http\Controllers\Repository\CrudRepository;

class FaultController extends Controller{
	
	public function view(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Chip";	
		$moduleType =  2; 
		$Chips = $CrudRepository->view($model,$moduleType);
		/*echo '<pre>';
		print_r($Chips);die;*/
		return view('Admin.Fault_device.view',compact('Chips'));
	} 
    

    public function changestatus(Request $request){


    	$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Chip"; 
		$id 		= $request->id;
		$form_data 	= array('status' => $request->status);
		$Changestatus = $CrudRepository->changestatus($model,$id,$form_data);
    		

	} 


	public function edit(Request $request){
		$data = $request->all();
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
        if($validate->fails()){ 
 			return redirect()->back()->withInput()->withErrors($validate);  
 		}else{
			$CrudRepository = new CrudRepository();
			$model 		= "App\Models\Chip";
			$id 		= $request->id;
			$Users 		= $CrudRepository->edit($model,$id);
			return view('Admin.Fault_device.edit',compact('Users'));
		}
	
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
		$model 		= "App\Models\Chip";
		$id 		= $request->id;
		$Users 		= $CrudRepository->dodelete($model,$id);
		$Message 	= "Delete successfully";
		return redirect('admin/chip/view')->with('error',$Message);

	}


	public function chipdetail(Request $request){

		$CrudRepository = new CrudRepository();
		$model 		= "App\Models\Chip";
		$id 		= $request->id;
		$Chips 		= $CrudRepository->getdetail($model,$id);

		return view('Admin.Fault_device.chip_detail',compact('Chips'));
	}



}
