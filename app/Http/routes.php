<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'MainController@home');
});

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
    Route::post('add-node', 'ApiController@addNode');
    Route::get('landmarks', 'ApiController@landmarks');
    Route::get('waypoints', 'ApiController@waypoints');
    Route::get('containers', 'ApiController@containers');
    Route::get('nodes/{server}', 'ApiController@nodes');
    Route::post('demote-node', 'ApiController@demoteNode');
    Route::get('node-info/{id}', 'ApiController@nodeInfo');
    Route::post('report-node', 'ApiController@reportNode');
    Route::post('promote-node', 'ApiController@promoteNode');
    Route::get('whichmap/{x}/{y}', 'ApiController@whichmap');
    Route::get('find-item/{id}/{token}', 'ApiController@findItem');
    Route::get('interesting-items', 'ApiController@itemsOfInterest');
});
