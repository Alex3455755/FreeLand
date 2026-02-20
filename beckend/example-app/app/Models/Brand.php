<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class brand extends Model
{
    protected $fillable = ['name','country','web_site'];


    public function clouthes(){
        return $this->hasMany(Clothe::class);
    }
}
