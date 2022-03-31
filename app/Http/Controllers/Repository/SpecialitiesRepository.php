<?php

namespace App\Http\Controllers\Repository;

use App\User;
use App\Models\Specialities;
use App\Http\Controllers\Utility\CustomVerfication;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Utility\SendEmails;

Class SpecialitiesRepository extends User{

	public function getspecialities(){

		$getspecialities 	=	Specialities::get();
		return $getspecialities; 
	}



} 

