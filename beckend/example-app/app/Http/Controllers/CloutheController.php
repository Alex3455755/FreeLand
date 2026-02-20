<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clothe;

class CloutheController extends Controller
{
    public function index(){
        $clouthes = ['clouthes' => Clothe::latest()->get()];
        return view('clothes.list',$clouthes);
    }

    public function detail(Clothe $clouthe){
        return view('clothes.detail', ['clouthe' => $clouthe]);
    }
}
