<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'freelancer_id',
        'project_id',
        'amount',
        'commission',
        'status',
        'type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

     public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
