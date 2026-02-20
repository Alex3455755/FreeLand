<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(){
        $context = ['sales' => Sale::latest()->get()];
        return view('index',$context);
    }

    public function detail(Sale $sale){
        return view('detail', ['sale' => $sale]);
    }
}
