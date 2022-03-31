<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Repository\SpecialitiesRepository;
use App\Http\Controllers\Utility\DataService;
use Illuminate\Support\Facades\Hash;

Class SpecialitiesService{


	public  function getspecialities()
	{

		$data = new DataService();
		$SpecialitiesRepository = new SpecialitiesRepository();
		$getspecialities  =   $SpecialitiesRepository->getspecialities();

		if($getspecialities){

			$data->error_code = 214;
			$data->data = $getspecialities->toArray();
		
		}

		return  $data;

	}

}