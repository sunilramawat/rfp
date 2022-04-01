<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('category_list','ApiController@category_list');
Route::get('report_text_list','ApiController@report_text_list');
Route::get('group_category_list','ApiController@group_category_list');
Route::get('subcategory_list','ApiController@subcategory_list');
Route::get('graph_list','ApiController@graph_list')->middleware('auth:api');

Route::post('register','ApiController@register');
Route::post('login','ApiController@login');
Route::post('verifyUser','ApiController@verifyUser');
Route::post('requestVerification','ApiController@requestVerification')->middleware('auth:api');

Route::post('socialLogin','ApiController@socialLogin');

Route::get('profile','ApiController@profile')->middleware('auth:api');
Route::post('update_profile','ApiController@profile')->middleware('auth:api');

Route::post('update_device','ApiController@update_device')->middleware('auth:api');

Route::get('check_username','ApiController@check_username')->middleware('auth:api');  
Route::post('changePassword','ApiController@changePassword')->middleware('auth:api');
Route::post('forgotPassword','ApiController@forgotPassword');
Route::post('resetPassword','ApiController@resetPassword');

Route::get('other_profile','ApiController@other_profile')->middleware('auth:api');
Route::post('create_post','ApiController@createPost')->middleware('auth:api');
Route::post('repost','ApiController@repost')->middleware('auth:api');
Route::get('photo_list','ApiController@photo_list')->middleware('auth:api');
Route::get('reel_list','ApiController@reel_list')->middleware('auth:api');
Route::post('reel_detail','ApiController@reel_detail')->middleware('auth:api');
Route::get('status_list','ApiController@status_list')->middleware('auth:api');
Route::post('user_status','ApiController@user_status')->middleware('auth:api');
Route::get('post_list','ApiController@post_list')->middleware('auth:api');

Route::get('forecast_list','ApiController@forecast_list')->middleware('auth:api');

Route::get('activity_list','ApiController@activity_list')->middleware('auth:api');

Route::delete('delete_post','ApiController@deletePost')->middleware('auth:api');
Route::get('post_detail','ApiController@post_detail')->middleware('auth:api');

Route::post('comment','ApiController@commentPost')->middleware('auth:api');


Route::get('logout','ApiController@logout')->middleware('auth:api');
Route::get('stock_list','ApiController@stock_list');
Route::get('current_price','ApiController@current_price');
Route::get('tranding','ApiController@tranding');
Route::get('tranding_list','ApiController@tranding_list')->middleware('auth:api');
Route::get('all_post_list','ApiController@all_post_list')->middleware('auth:api');

Route::post('like','ApiController@like')->middleware('auth:api');

Route::post('follow','ApiController@follow')->middleware('auth:api');
Route::post('comment_like','ApiController@comment_like')->middleware('auth:api');
Route::post('vote','ApiController@vote')->middleware('auth:api');
Route::post('favourite','ApiController@favourite')->middleware('auth:api');
Route::get('watch_list','ApiController@watch_list')->middleware('auth:api');


// Group Section 
Route::get('followUser','ApiController@followUser')->middleware('auth:api');
Route::post('createGroup','ApiController@createGroup')->middleware('auth:api');
Route::post('joinGroup','ApiController@joinGroup')->middleware('auth:api');
Route::post('addMember','ApiController@addMember')->middleware('auth:api');
Route::post('makeAdmin','ApiController@makeAdmin')->middleware('auth:api');
Route::post('removeAdmin','ApiController@removeAdmin')->middleware('auth:api');
Route::get('groupUser','ApiController@groupUser')->middleware('auth:api');
Route::get('group_detail','ApiController@group_detail')->middleware('auth:api');
Route::get('mygroup_list','ApiController@mygroup_list')->middleware('auth:api');
Route::get('popular_list','ApiController@popular_list')->middleware('auth:api');
Route::delete('removeGroupUser','ApiController@removeGroupUser')->middleware('auth:api');
Route::post('deleteGroup','ApiController@deleteGroup')->middleware('auth:api');
Route::get('requestList','ApiController@requestList')->middleware('auth:api');
Route::get('roomList','ApiController@roomList')->middleware('auth:api');
Route::post('acceptDecline','ApiController@acceptDecline')->middleware('auth:api');

//Forum Section 
Route::post('createForum','ApiController@createForum')->middleware('auth:api');
Route::get('ForumTopicList','ApiController@forum_topic_list')->middleware('auth:api');
Route::get('ForumList','ApiController@forum_list')->middleware('auth:api');
Route::get('ForumDetail','ApiController@forum_detail')->middleware('auth:api');
Route::post('ForumLike','ApiController@forum_like')->middleware('auth:api');
Route::post('ForumComment','ApiController@forum_commentPost')->middleware('auth:api');
Route::post('ForumCommentLike','ApiController@forum_comment_like')->middleware('auth:api');
////////////////////


//VoteThem Section 
Route::post('createVoteThem','ApiController@create_vote_them')->middleware('auth:api');
Route::get('VoteThemList','ApiController@vote_them_list')->middleware('auth:api');
Route::get('VoteThemDetail','ApiController@vote_them_detail')->middleware('auth:api');
Route::post('VoteThemLike','ApiController@vote_them_like')->middleware('auth:api');
Route::post('VoteThemComment','ApiController@vote_them_commentPost')->middleware('auth:api');
Route::post('VoteThemCommentLike','ApiController@vote_them_comment_like')->middleware('auth:api');
////////////////////

Route::post('groupChat','ApiController@groupChat')->middleware('auth:api');
Route::get('groupChatMessageList','ApiController@groupChatMessageList')->middleware('auth:api');
Route::delete('groupCancleRequest','ApiController@groupCancleRequest')->middleware('auth:api');

////////////////////////////////////////////

Route::get('grouppost_list','ApiController@grouppost_list')->middleware('auth:api');
Route::post('create_group_post','ApiController@groupcreatePost')->middleware('auth:api');
Route::post('grouprepost','ApiController@grouprepost')->middleware('auth:api');
Route::post('groupvote','ApiController@groupvote')->middleware('auth:api');
Route::get('group_post_detail','ApiController@group_post_detail')->middleware('auth:api');
Route::delete('groupdelete_post','ApiController@groupdelete_post')->middleware('auth:api');
Route::post('grouplike','ApiController@grouplike')->middleware('auth:api');
Route::post('groupComment','ApiController@groupcommentPost')->middleware('auth:api');
Route::post('groupcomment_like','ApiController@groupcomment_like')->middleware('auth:api');
Route::post('groupfavourite','ApiController@groupfavourite')->middleware('auth:api');
//////////////////////////////////////////

Route::post('setpreferences','ApiController@setpreferences')->middleware('auth:api');
Route::get('setpreferences','ApiController@setpreferences')->middleware('auth:api');

Route::get('gallery','ApiController@gallery')->middleware('auth:api');

Route::delete('gallery','ApiController@gallery')->middleware('auth:api');

Route::post('make_default','ApiController@make_default')->middleware('auth:api');

Route::post('visibility','ApiController@visibility')->middleware('auth:api');

Route::get('match','ApiController@match')->middleware('auth:api');
Route::delete('match','ApiController@match')->middleware('auth:api');

Route::get('pending_match','ApiController@pending_match')->middleware('auth:api');

Route::post('report','ApiController@report')->middleware('auth:api');

Route::get('user_detail','ApiController@user_detail')->middleware('auth:api');

Route::delete('deleteAccount','ApiController@deleteAccount')->middleware('auth:api');	

Route::get('notificationList','ApiController@notificationList')->middleware('auth:api');




Route::get('recommend_list','ApiController@recommend_list')->middleware('auth:api');


Route::get('subscriptionsList','ApiController@subscriptionsList');
Route::post('pendingSubscriptionPlan','ApiController@pendingSubscriptionPlan')->middleware('auth:api');
Route::get('cronJobForSubscreption','ApiController@cronJobForSubscreption');

Route::post('androidSubscreption','ApiController@androidSubscreption')->middleware('auth:api');


// twilio
Route::post('chat_user', "ApiController@chat_user")->middleware('auth:api');
Route::get('chat_token','ApiController@chat_token')->middleware('auth:api');
Route::post('chat_post_event','ApiController@chat_post_event');
Route::post('chat_pre_event','ApiController@chat_pre_event');
Route::get('chat_update_uername','ApiController@chat_update_uername');
Route::post('addchatuser','ApiController@addchatuser');
Route::post('contact','ApiController@contact');

Route::get('check_pending','ApiController@check_pending');
Route::get('update_previous','ApiController@update_previous');

Route::get('notification_match_detail','ApiController@notification_match_detail')->middleware('auth:api');
// Question Answer
Route::get('question','ApiController@question')->middleware('auth:api');
Route::post('answer','ApiController@answer')->middleware('auth:api');
Route::delete('answer_delete','ApiController@answer_delete')->middleware('auth:api');

Route::post('chip','ApiController@chip')->middleware('auth:api');
Route::get('chip_list','ApiController@chip_list')->middleware('auth:api');
Route::get('chip_data_list','ApiController@chip_data_list')->middleware('auth:api');
Route::post('userList','ApiController@userList')->middleware('auth:api');

Route::post('alluserList','ApiController@alluserList')->middleware('auth:api');

//Forum Section 

Route::post('createDebet','ApiController@createDebet')->middleware('auth:api');
Route::get('debetrequestList','ApiController@debetrequestList')->middleware('auth:api');
Route::post('debetacceptDecline','ApiController@debetacceptDecline')->middleware('auth:api');


/*
Route::middleware('auth')->group(function () {
    Route::get('profile', [App\Http\Controllers\ApiController::class, 'profile'])->name('profile');
    });*/