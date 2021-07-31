<?php

Route::redirect('/', '/login');
// Route::get('/home', function () {
//     if (session('status')) {
//         return redirect()->route('admin.home')->with('status', session('status'));
//     }

//     return redirect()->route('admin.home');
// });
Route::get('/home', 'HomeController@index')->name('admin.home');
Route::get('/verify', 'EmailVerifyController@verifyEmail')->name('admin.verify');

Auth::routes(['register' => false]);
// Admin

Route::group([
    'prefix' => 'user',
    'as' => 'user.',
    'namespace' => 'User',
    'middleware' => ['auth']
], function () {
    Route::get('/', 'HomeController@index')->name('home');
});

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'namespace' => 'Admin',
    'middleware' => ['auth', 'admin']
], function () {
  //  Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Countries
    Route::delete('countries/destroy', 'CountriesController@massDestroy')->name('countries.massDestroy');
    Route::resource('countries', 'CountriesController');

    // Cities

    Route::delete('pincodes/destroy', 'PinCodesController@massDestroy')->name('pincodes.massDestroy');
    Route::get('pincodes/getStates/{cid?}', 'PinCodesController@getStates')->name('pincodes.getStates');
    Route::get('pincodes/getCities/{cid?}/{sid?}', 'PinCodesController@getCities')->name('pincodes.getCities');
    Route::resource('pincodes', 'PinCodesController');

    // Cities

    Route::delete('cities/destroy', 'CitiesController@massDestroy')->name('cities.massDestroy');
    Route::get('cities/getStates/{cid?}', 'CitiesController@getStates')->name('cities.getStates');
    Route::resource('cities', 'CitiesController');

    // Regions

    Route::delete('regions/destroy', 'RegionsController@massDestroy')->name('regions.massDestroy');
    //Route::get('cities/getStates/{cid?}', 'RegionsController@getStates')->name('regions.getStates');
    Route::resource('regions', 'RegionsController');

    // Delivery boys

    Route::delete('deliveryboys/destroy', 'DeliveryBoysController@massDestroy')->name('deliveryboys.massDestroy');
    //Route::get('cities/getStates/{cid?}', 'RegionsController@getStates')->name('regions.getStates');
    Route::post('deliveryboys/changeKYCStatus', 'DeliveryBoysController@changeKYCStatus')->name('deliveryboys.changeKYCStatus');
    Route::resource('deliveryboys', 'DeliveryBoysController');

    // Campaign
    Route::delete('campaigns/destroy', 'CampaignController@massDestroy')->name('campaigns.massDestroy');
    Route::get('campaigns/getCampaignMaster/{cid?}', 'CampaignController@getCampaignMaster')->name('campaigns.getCampaignMaster');
    Route::get('campaigns/getAllActiveCustomer', 'CampaignController@getAllActiveCustomer')->name('campaigns.getAllActiveCustomer');
    Route::resource('campaigns', 'CampaignController');

    // Products
    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');
    Route::get('products/getSubCategories/{cid?}', 'ProductsController@getSubCategories')->name('products.getSubCategories');
    Route::resource('products', 'ProductsController');

    // Categories
    Route::delete('categories/destroy', 'CategoriesController@massDestroy')->name('categories.massDestroy');
    Route::resource('categories', 'CategoriesController');

    // Units
    Route::delete('units/destroy', 'UnitsController@massDestroy')->name('units.massDestroy');
    Route::resource('units', 'UnitsController');

    // Customers
    Route::delete('customers/destroy', 'CustomersController@massDestroy')->name('customers.massDestroy');
    Route::resource('customers', 'CustomersController');

    // States
    Route::delete('states/destroy', 'StatesController@massDestroy')->name('states.massDestroy');
    Route::resource('states', 'StatesController');

    // Banners
    Route::delete('banners/destroy', 'BannersController@massDestroy')->name('banners.massDestroy');
    Route::resource('banners', 'BannersController');

    // Product Units
    Route::delete('product_units/destroy', 'ProductUnitsController@massDestroy')->name('product_units.massDestroy');
    Route::get('product_units/getUnits/{cid?}', 'ProductUnitsController@getUnits')->name('product_units.getUnits');
    Route::get('product_units/addOrRemoveInventory/{pid?}', 'ProductUnitsController@addOrRemoveInventory')->name('product_units.addOrRemoveInventory');
    Route::post('product_units/storeInventory', 'ProductUnitsController@storeInventory')->name('product_units.storeInventory');
    Route::resource('product_units', 'ProductUnitsController');

    // Baskets
    Route::delete('baskets/destroy', 'BasketsController@massDestroy')->name('baskets.massDestroy');
    Route::post('baskets/get-products', 'BasketsController@getProducts')->name('communications.getProducts');
    Route::resource('baskets', 'BasketsController');

    // Communications
    Route::delete('communications/destroy', 'UserCommunicationMessagesController@massDestroy')->name('communications.massDestroy');
    Route::get('communications/check-past-time/{id}', 'UserCommunicationMessagesController@checkPastTime')->name('communications.checkPastTime');
    Route::post('communications/send-test-sms', 'UserCommunicationMessagesController@sendTestSms')->name('communications.sendTestSms');
    Route::post('communications/send-test-email', 'UserCommunicationMessagesController@sendTestEmail')->name('communications.sendTestEmail');
    Route::post('communications/get-user-type-data', 'UserCommunicationMessagesController@getUserTypeData')->name('communications.getUserTypeData');
    Route::resource('communications', 'UserCommunicationMessagesController');

    // Orders
    // Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    Route::get('orders/cancelOrder/{cid?}', 'OrdersController@cancelOrder')->name('orders.cancelOrder');
    Route::get('orders/reAssign/{cid?}', 'OrdersController@reAssign')->name('orders.reAssign');
    Route::post('orders/check-delivery-boy-availability', 'OrdersController@checkDeliveryBoyAvailability')->name('orders.checkDeliveryBoyAvailability');
    Route::resource('orders', 'OrdersController');

    // Purchase Form
    Route::delete('purchase_form/destroy', 'PurchaseFormController@massDestroy')->name('purchase_form.massDestroy');
    Route::resource('purchase_form', 'PurchaseFormController');

    // Reports
    Route::get('reports/login_logs', 'ReportsController@loginLogs')->name('reports.loginLogs');
    Route::get('reports/sales-itemwise', 'ReportsController@salesItemwise')->name('reports.salesItemwise');
    Route::post('reports/sales-itemwise/data', 'ReportsController@getSalesItemwiseData')->name('reports.getSalesItemwiseData');
    
    Route::resource('reports', 'ReportsController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
    }
});
