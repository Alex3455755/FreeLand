<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
        public function index(){
        $brands = ['brands' => Brand::latest()->get()];
        return view('brands.list',$brands);
    }


        public function detail(Brand $brand){
        return view('brands.detail', ['brand' => $brand]);
    }
}
