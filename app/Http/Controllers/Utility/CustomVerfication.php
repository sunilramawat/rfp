<?php
namespace App\Http\Controllers\Utility;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;

final class CustomVerfication{

	public static  function generateRandomNumber(){

		$number = rand(1000 , 9999 );
		return $number;	
	}	

	public static function phoneVerification($message, $recipients){
		$account_sid = getenv("TWILIO_SID");
  	$auth_token = getenv("TWILIO_AUTH_TOKEN");
  	$twilio_number = getenv("TWILIO_NUMBER");
		/*$account_sid = "AC148f36a13b0587e7fbdae595827ee65b";
  	$auth_token = "b71128d28774589e6f2490de25a8acea";
  	$twilio_number = "+14156393672";
		*/

  	$client = new Client($account_sid, $auth_token);
  	$client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message] );

  	echo '<pre>'; print_r($client); exit;

	} 
	
	public function imageUpload($image,$pathName){

		$filesize = filesize($image);
		$fileName = $image->getClientOriginalName();
		$fileExtension = $image->getClientOriginalExtension();
		$fileName = 'image'.rand(11111, 99999) . '.' . $fileExtension;
		$destinationPath = 'images/'.$pathName;
		$upload_success = $image->move($destinationPath, $fileName);
		$images = $pathName.'/'.$fileName;


		return $images;
	}

	public function chat_user($userid){
		$userId = $userid;
        // Find your Account SID and Auth Token at twilio.com/console
        // and set the environment variables. See http://twil.io/secure
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);
        //print_r($twilio); exit;
        $user = $twilio->conversations->v1->users
                                          ->create($userId);


        //print_r($user->sid); exit;
        return  $user->sid;
    }

	public function twilio_test(){
		
		echo 'dasd'; exit;
		// Required for all Twilio access tokens
		/*$twilioAccountSid = 'ACb109c837820881166ea43e29c72430b6';
		$twilioApiKey = 'SK777e2fe9cc5acb1b616da32c5b875b91';
		$twilioApiSecret = 'gBe9gKZaTsxdrrN5C7FXA8PLPG3RurE6';

		// Required for Chat grant
		$serviceSid = 'IS5fbfc15d521f4909b32e09a51ae904e6';*/

		$twilioAccountSid = 'AC0d142cde71739f902052d41f99bdcd72';
		$twilioApiKey = 'SK777e2fe9cc5acb1b616da32c5b875b91s';
		$twilioApiSecret = 's';

		// Required for Chat grant
		$serviceSid = 'IS5fbfc15d521f4909b32e09a51ae904e61';
		// choose a random username for the connecting user
		$identity = "john_doe";

		// Create access token, which we will serialize and send to the client
		$token = new AccessToken(
		    $twilioAccountSid,
		    $twilioApiKey,
		    $twilioApiSecret,
		    3600,
		    $identity
		);
		print_r($token); exit;
		// Create Chat grant
		$chatGrant = new ChatGrant();
		$chatGrant->setServiceSid($serviceSid);

		// Add grant to token
		$token->addGrant($chatGrant);

		// render token to string
		echo $token->toJWT();
	}
	public function getToken(Request $request)
	{
	    $this->identity = $request->identity;
	    // Create access token, which we will serialize and send to the client
	    $token = new AccessToken(
	    $this->twilio_account_sid,
	    $this->twilio_api_key,
	    $this->twilio_api_secret,
	    3600,
	    $this->identity
	    );

	    // Create Chat grant
	    $chat_grant = new ChatGrant();
	    $chat_grant->setServiceSid($this->service_sid);

	    // Add grant to token
	    $token->addGrant($chat_grant);

	    // render token to string
	    echo $token->toJWT();
	}
}
