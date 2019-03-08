<?php


Route::group(['middleware' => ['web']], function () {

    Route::any('auth/login', "Kyaroslav\FirebaseAuth\Http\FirebaseAuthController@loginFirebase")->name('auth.login');

});