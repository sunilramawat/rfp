<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Repository\UserRepository;
use App\Http\Controllers\Utility\DataService;
use Illuminate\Support\Facades\Hash;

Class UserService{


	public  function getUserList()
	{
		$UserRepository = new UserRepository();
		$getUserList = $UserRepository->get_user_list();
		return $getUserList;  

	}

}