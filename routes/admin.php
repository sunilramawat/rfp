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

Route::group(['middleware' => ['admin','prevent-back-history']], function (){


    Route::get('/', function () {
        return view('admin.login');
    });

    Route::post('/do-login','admin\LoginController@dologin');
    Route::get('/dashboard','admin\LoginController@dashboard');
    Route::get('/logout','admin\LoginController@logout');   
    Route::get('/refreshCaptcha','admin\LoginController@refreshCaptcha');   

    Route::get('/forgot','admin\LoginController@forgot');
    Route::post('/do-forgot','admin\LoginController@doforgot');
    Route::post('/reset','admin\LoginController@resetpass');

     /*
    |--------------------------------------------------------------------------
    | ------------------Supplier ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    Route::get('/supplier/view','admin\SupplierController@view');
    Route::get('/supplier/ChangeStatus/{id}/{status}','admin\SupplierController@changestatus');
    Route::get('/supplier/ProductStatus/{id}/{status}','admin\SupplierController@productstatus');
    Route::get('/supplier/detail/{id}','admin\SupplierController@detail');
    Route::get('/supplier/product/{id}','admin\SupplierController@product');
    Route::get('/supplier/edit/{id}','admin\SupplierController@edit');
    Route::post('/supplier/edit-save','admin\SupplierController@editsave');
    Route::get('/supplier/search','admin\SupplierController@search');

     /*
    |--------------------------------------------------------------------------
    | ------------------Pending Request ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    Route::get('/pendingrequest/view','admin\PendingRequestController@view');
    Route::get('/pendingrequest/Approve/{id}/{status}','admin\PendingRequestController@approve');
    Route::get('/pendingrequest/Reject/{id}/{status}','admin\PendingRequestController@reject');
    Route::get('/pendingrequest/detail/{id}','admin\PendingRequestController@detail');
    Route::get('/pendingrequest/edit/{id}','admin\PendingRequestController@edit');
    Route::post('/pendingrequest/edit-save','admin\PendingRequestController@editsave');
    
   
    

    /*
    |--------------------------------------------------------------------------
    | ------------------Customer ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/user/view','admin\UserController@view');
    Route::get('/user/add','admin\UserController@add');
    Route::post('/user/save','admin\UserController@save');
    Route::get('/user/ChangeStatus/{id}/{status}','admin\UserController@changestatus');
    Route::get('/user/ChangeStatus/{id}/{status}','admin\UserController@changestatus');
    Route::get('/user/detail/{id}','admin\UserController@detail');
    Route::get('/user/delete/{id}','admin\UserController@delete');
    Route::get('/user/search','admin\UserController@search');
    


    /*
    |--------------------------------------------------------------------------
    | ------------------Manage Order ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    Route::get('/order/view','admin\OrderController@view');
    Route::get('/order/ChangeStatus/{id}/{status}','admin\OrderController@changestatus');
    Route::get('/order/detail/{id}','admin\OrderController@detail');
    Route::get('/order/edit/{id}','admin\OrderController@edit');
    //Route::get('/order/payout/{id}','admin\OrderController@payout');
    Route::post('/order/edit-save','admin\OrderController@editsave');
    Route::get('/order/order','admin\OrderController@order');
    Route::get('/order/export', 'admin\OrderController@export');
   
    /*
    |--------------------------------------------------------------------------
    | ------------------Manage Payment ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    Route::get('/payment/view','admin\PaymentController@view');
    Route::get('/payment/ChangeStatus/{id}/{status}','admin\PaymentController@changestatus');
    Route::get('/payment/detail/{id}','admin\PaymentController@detail');
    Route::get('/payment/edit/{id}','admin\PaymentController@edit');
    Route::post('/payment/edit-save','admin\PaymentController@editsave');
    Route::get('/payment/order','admin\PaymentController@order');


     /*
    |--------------------------------------------------------------------------
    | ------------------Manage Payout ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    Route::get('/payout/view','admin\PayoutController@view');
    Route::get('/payout/payout','admin\PayoutController@payout');
    Route::get('/payout/ChangeStatus/{id}/{status}','admin\PayoutController@changestatus');
    Route::get('/payout/detail/{id}','admin\PayoutController@detail');
    Route::get('/payout/edit/{id}','admin\PayoutController@edit');
    Route::post('/payout/edit-save','admin\PayoutController@editsave');
    Route::get('/payout/order','admin\PayoutController@order');
    Route::get('/payout/export', 'admin\PayoutController@export');
    /*
    |--------------------------------------------------------------------------
    | ------------------Trade ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/trade/view','admin\TradeController@view');
    Route::get('/trade/add','admin\TradeController@add');
    Route::post('/trade/save','admin\TradeController@save');
    Route::get('/trade/ChangeStatus/{id}/{status}','admin\TradeController@changestatus');
    Route::get('/trade/detail/{id}','admin\TradeController@detail');
    Route::get('/trade/delete/{id}','admin\TradeController@delete');
    Route::get('/trade/search','admin\TradeController@search');

    /*
    |--------------------------------------------------------------------------
    | ------------------Sub Trade ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    /*Route::get('/manage-trade-sub-categories', function () {
        return view('admin.managetradesubcategories');
    });
    */
    Route::get('/subtrade/view/{id}','admin\SubTradeController@view');
    Route::get('/subtrade/add','admin\SubTradeController@add');
    Route::post('/subtrade/save','admin\SubTradeController@save');
    Route::get('/subtrade/ChangeStatus/{id}/{status}','admin\SubTradeController@changestatus');
    Route::get('/subtrade/detail/{id}','admin\SubTradeController@detail');
    Route::get('/subtrade/delete/{id}','admin\SubTradeController@delete');
     Route::get('/subtrade/search','admin\SubTradeController@search');
    /*
    |--------------------------------------------------------------------------
    | ------------------Report ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/report/customer/','admin\ReportController@customer');
    Route::get('/report/payout/','admin\ReportController@payout');
    Route::get('/report/supplier/','admin\ReportController@supplier');
    Route::get('/report/order/','admin\ReportController@order');
    Route::get('/report/cancel/','admin\ReportController@cancel');
    Route::get('/report/refund/','admin\ReportController@refund');
    Route::get('/report/earning/','admin\ReportController@earning');


     /*
    |--------------------------------------------------------------------------
    | ------------------CMS ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/cms/view','admin\CmsController@view');
    Route::get('/cms/add','admin\CmsController@add');
    Route::get('/cms/edit/{id}','admin\CmsController@edit');
    Route::post('/cms/edit-save','admin\CmsController@editsave');
    Route::post('/cms/save','admin\CmsController@save');
    Route::get('/cms/ChangeStatus/{id}/{status}','admin\CmsController@changestatus');
    Route::get('/cms/detail/{id}','admin\CmsController@detail');
    Route::get('/cms/delete/{id}','admin\CmsController@delete');
   
    /*
    |--------------------------------------------------------------------------
    | ------------------FAQ ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/faq/view','admin\FaqController@view');
    Route::get('/faq/add','admin\FaqController@add');
    Route::get('/faq/edit/{id}','admin\FaqController@edit');
    Route::post('/faq/edit-save','admin\FaqController@editsave');
    Route::post('/faq/save','admin\FaqController@save');
    Route::get('/faq/ChangeStatus/{id}/{status}','admin\FaqController@changestatus');
    Route::get('/faq/detail/{id}','admin\FaqController@detail');
    Route::get('/faq/delete/{id}','admin\FaqController@delete');


     /*
    |--------------------------------------------------------------------------
    | ------------------Patner ROUTE----------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/partner/view','admin\PartnerController@view');
    Route::get('/partner/add','admin\PartnerController@add');
    Route::get('/partner/edit/{id}','admin\PartnerController@edit');
    Route::post('/partner/edit-save','admin\PartnerController@editsave');
    Route::post('/partner/save','admin\PartnerController@save');
    Route::get('/partner/ChangeStatus/{id}/{status}','admin\PartnerController@changestatus');
    Route::get('/partner/detail/{id}','admin\PartnerController@detail');
    Route::get('/partner/delete/{id}','admin\PartnerController@delete');
    Route::post('/partner/subcat', 'admin\PartnerController@subCat');


     /*
    |--------------------------------------------------------------------------
    | ------------------Report ROUTE-------------------------------------------
    |--------------------------------------------------------------------------
    */
    
    Route::get('/report/view','admin\ReportController@view');



    Route::get('/manage-supplier', function () {
        return view('admin.managesupplier');
    });

    Route::get('/manage-supplier-product', function () {
        return view('admin.managesupplierproduct');
    });

    Route::get('/manage-supplier-detail', function () {
        return view('admin.managesupplierdetail');
    });

    Route::get('/pending-request', function () {
        return view('admin.pendingrequest');
    });

    Route::get('/pending-request-detail', function () {
        return view('admin.pendingrequestdetail');
    });

    Route::get('/manage-customers', function () {
        return view('admin.managecustomers');
    });

    Route::get('/manage-customers-detail', function () {
        return view('admin.managecustomersdetail');
    });

    Route::get('/manage-customers-order-detail', function () {
        return view('admin.managecustomersorderdetail');
    }); 

    Route::get('/manage-order', function () {
        return view('admin.manageorder');
    });

    Route::get('/manage-categories', function () {
        return view('admin.managecategories');
    });

    Route::get('/manage-categories-trade', function () {    
        return view('admin.managecategoriestrade');
    });

    Route::get('/manage-categories-sub-trade', function () {
        return view('admin.managecategoriesubtrade');
    });

    

    Route::get('/manage-payout-list', function () {
        return view('admin.managepayoutlist');
    });

    Route::get('/manage-reports', function () {
        return view('admin.managereports');
    });


    

   /* Route::get('/forgot', function () {
        return view('admin.forgot');
    });



    Route::get('/reset', function () {
        return view('admin.reset');
    });*/
});



