<?php

Route::get('/',         function() {
    return Redirect::to('home');
});

Route::get('home',      'PageController@home');
Route::get('contacts',  'PageController@contacts');
Route::get('netblocks', 'PageController@netblocks');
Route::get('domains',   'PageController@domains');
Route::get('reports',   'PageController@reports');
Route::get('search',    'PageController@search');
Route::get('analytics', 'PageController@analytics');
