<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StatusMembership\Controllers\StatusMembershipController;

Route::controller(StatusMembershipController::class)->middleware(['web','auth'])->name('statusmembership.')->group(function(){
	Route::get('/statusmembership', 'index')->name('index');
	Route::get('/statusmembership/data', 'data')->name('data.index');
	Route::get('/statusmembership/create', 'create')->name('create');
	Route::post('/statusmembership', 'store')->name('store');
	Route::get('/statusmembership/{statusmembership}', 'show')->name('show');
	Route::get('/statusmembership/{statusmembership}/edit', 'edit')->name('edit');
	Route::patch('/statusmembership/{statusmembership}', 'update')->name('update');
	Route::get('/statusmembership/{statusmembership}/delete', 'destroy')->name('destroy');
});