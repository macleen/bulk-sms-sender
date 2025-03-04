<?php use App\Routing\Route;

Route::get('/providers'        , 'HomeController@get_available_providers' );            
Route::get('/installed/plugins', 'HomeController@installed_plugins_tree'  );
Route::post('/install/plugin'  , 'HomeController@install_plugin'          );
Route::get('/users/{role}'     , 'HomeController@members_with_phone'      );

Route::group('/{provider}', [ ],  function( ){
    Route::get('/'             , 'ProviderController@get_packet_format'   );            
    Route::get('/fields'       , 'ProviderController@get_provider_fields' );
    Route::post('/account/info', 'ProviderController@get_account_info'    );
    Route::get('/get_balance'  , 'ProviderController@get_balance'         );
    Route::post('/send'        , 'ProviderController@trigger_send'        );    
});

Route::group('/log', [ ],  function( ){
    Route::get('/all'          , 'LoggingController@get_available_log_files');            
    Route::get('/date/{date}'  , 'LoggingController@get_log_by_date'       );
    Route::get('/delete/{date}', 'LoggingController@delete_log_by_date'    );
});

Route::group('/analytics', [ ],  function( ){
    Route::get('/all'          , 'AnalyticsController@get_available_files' );            
    Route::post('/file'        , 'AnalyticsController@get_file_by_path'    );
    Route::post('/delete'      , 'AnalyticsController@delete_file_by_path' );
});