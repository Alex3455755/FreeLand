<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CloutheController;
use App\Http\Controllers\BrandController;



Route::get('/', [SaleController::class,'index'])->name("sale.index");
Route::get('/sale/{sale}', [SaleController::class,'detail'])->name("sale.detail");



Route::get('/clouthe', [CloutheController::class,'index'])->name("clothes.list");
Route::get('/clouthe/{clouthe}', [CloutheController::class,'detail'])->name("clothes.detail");

Route::get('/brand', [BrandController::class,'index'])->name("brand.list");
Route::get('/brand/{brand}', [BrandController::class,'detail'])->name("brand.detail");