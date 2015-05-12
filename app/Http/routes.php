<?php
Route::get('/',         'HomeController@index');
Route::get('home',      'HomeController@index');
Route::get('contacts',  'ContactsController@index');
Route::get('netblocks', 'NetblocksController@index');
Route::get('domains',   'DomainsController@index');
Route::get('reports',   'ReportsController@index');
Route::get('search',    'SearchController@index');
Route::get('analytics', 'AnalyticsController@index');
