<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cars;
class CarsController extends Controller
{
     public function index(){
        $Cars = Cars::latest()->get();
        $text = "Список машин\r\n\r\n";
        foreach ($Cars as $car){
            $text .= $car->number. "\r\n";
            $text .= $car->type. "\r\n";
            $text .= $car->wt. "\r\n";
            $text .= $car->color. "\r\n";
            $text .= $car->production_date. "\r\n";
            $text .=  "\r\n";
        }

        return response($text)->header('Content-Type','text/plain');
    }
}
