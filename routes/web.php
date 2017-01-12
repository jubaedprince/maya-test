<?php

Route::get('/', 'LoginController@login');
Route::post('/login', 'LoginController@processLogin');