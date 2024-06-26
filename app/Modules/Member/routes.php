<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Member\Controllers\MemberController;

Route::controller(MemberController::class)->middleware(['web','auth'])->name('member.')->group(function(){
	// route custom
	Route::get('/member/aktivasi/{id_member}', 'aktivasi')->name('aktivasi.update');
	Route::get('/member/list', 'list_member')->name('list.show');


	Route::get('/member', 'index')->name('index');
	Route::get('/member/data', 'data')->name('data.index');
	Route::get('/member/create', 'create')->name('create');
	Route::post('/member', 'store')->name('store');
	Route::get('/member/{member}', 'show')->name('show');
	Route::get('/member/{member}/edit', 'edit')->name('edit');
	Route::patch('/member/{member}', 'update')->name('update');
	Route::get('/member/{member}/delete', 'destroy')->name('destroy');
});