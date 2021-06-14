<?php

use Illuminate\Http\Request;

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

Route::middleware(['auth.middleware', 'tenant'])->group(function () {

    //Product
    Route::post('products', 'ProductController@getAll')->name('products');
    Route::post('product/detail','ProductController@getDetail');
    Route::post('product/attributes','ProductController@attributes');

    //Product Tags
    Route::post('updateProductTags', 'ProductTagsController@store')->name('updateProductTags');
    Route::post('product/colors', 'PRDTMS0Controller@getProductColors');

    //LineSheet Routes

    Route::group(['prefix' => 'linesheet'], function () {

        Route::post('store', 'LineSheetController@store');
        Route::post('listing', 'LineSheetController@getAll');
        Route::post('status', 'LineSheetController@changeLineSheetStatus');
        Route::post('duplicate', 'LineSheetController@duplicateLineSheet');
        Route::post('share', 'LineSheetController@lineSheetShare');
        Route::post('user', 'LineSheetController@getUserLineSheet');
        Route::post('image','LineSheetController@setImage');
        Route::post('detail','LineSheetController@getLinesheet');
        Route::post('create/new', 'LineSheetController@createNewLinesheet');
    });

    //LineSheet groups
    Route::group(['prefix' => 'lineSheetGroups'], function () {
        Route::post('store', 'LineSheetGroupController@store');
        Route::post('show', 'LineSheetGroupController@show');
        Route::post('addproducts', 'LineSheetGroupProductsController@store');
        Route::post('notes', 'LineSheetGroupProductsController@storeNotes');
        Route::post('products/insert', 'LineSheetGroupProductsController@storeAll');
        Route::post('customSort', 'LineSheetGroupProductsController@customSort');
        Route::post('delete', 'LineSheetGroupProductsController@deleteLineSheetGroup');
        Route::post('product/delete', 'LineSheetGroupProductsController@deleteLineSheetGroupProduct');
        Route::post('notes/delete', 'LineSheetGroupProductsController@deleteNotes');


    });

    //Order
    Route::group(['prefix' => 'order'], function () {
        Route::post('addHeaderDtl', 'OrderController@updateOrderHeader');
        Route::post('detail/fields', 'PreOrderHdrController@getFields');
        Route::post('detail/get', 'PreOrderHdrController@getOrderDetail');
        Route::post('product/detail/edit', 'PreOrderHdrController@editOrderDetailProduct');
        Route::post('details/edit', 'PreOrderHdrController@editOrderDetailProducts');
        Route::post('listing', 'OrderController@getAll');
        Route::post('delete', 'OrderController@deleteOrder');
        Route::post('assignSimilar','PreOrderHdrController@assignSimilar');
    });

    // Tenant.
    Route::get('tenant/get', 'TenantController@get')->name('getTenant');

    // Users.
    Route::get('user/logout', 'UserController@logout')->name('userLogout');

    //Filters
    Route::post('table/column/get-specific-key-values', 'FilterController@getSpecificColumnValue');
    Route::post('table/column/all', 'FilterController@getTableColumnAll');
    Route::post('table/column/key-multi-values', 'FilterController@getTableColumnKeyMultiValues');
    Route::post('getAvailability/all', 'StyleAvailController@getAvailability');
    Route::post('salesRep', 'UserController@getSalesRep');
    Route::post('swatPO','PreOrderHdrController@getSwatPO');
    Route::post('brands','ProdPLMController@getBrands');
    Route::post('options','FilterController@getOptionsValue');

    //customer
    Route::post('getcustomer', 'CUSTMS0Controller@getCustomerListing');

    //Workspace
    Route::post('workspace/insert', 'WorkspaceController@create');
    Route::post('workspace/deleteProduct', 'WorkspaceController@deleteSelectedProducts');
    Route::post('workspace/delete', 'WorkspaceController@deleteAll');
    Route::post('workspace/show', 'WorkspaceController@show');
    Route::post('workspace/edit', 'WorkspaceController@edit');
    Route::post('workspace/move', 'WorkspaceController@moveToDifferentGroup');

    //Cart
    Route::post('cart/show', 'CartController@show');
    Route::post('cart/addToCart', 'CartController@addToCart');
    Route::post('cart/updateProduct', 'CartController@updateCart');
    Route::post('cart/deleteProduct', 'CartController@deleteProductColor');
    Route::post('cart/delete', 'CartController@deleteProduct');
    Route::post('cart/deleteAll', 'CartController@destroy');
    Route::post('cart/linesheet/add', 'CartController@addLineSheetToCart');

    Route::post('cart/addToOrders', 'CartController@addToOrders');

    //createPdf
    Route::post('/pdf', 'PDFController@createPDF');

    //createExcel
    Route::post('/excel', 'ExcelController@createExcel');

    //div logo
    Route::post('logos', 'DivLogoController@getAll');
});

// User
Route::post('user/login', 'UserController@login')->name('loginUser');
Route::post('user/register', 'UserController@register')->name('registerUser');
Route::post('user/send_password_reset_token', 'ResetPasswordController@sendPasswordResetToken')->name('SendPasswordResetToken');
Route::post('user/reset_password', 'ResetPasswordController@resetPassword')->name('ResetPassword');
