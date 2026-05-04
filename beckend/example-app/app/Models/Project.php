<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Project extends Model
{
    use HasFactory;

    protected $appends = [
        'freelancer_id',
    ];

    protected $fillable = [
        'title',
        'description',
        'budget',
        'deadline',
        'status',
        'category_id',
        'freelancer_id',
        'frelancer_id',
        'customer_id',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'date',
    ];

    // Отношения
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function frelancer()
    {
        $foreignKey = Schema::hasColumn('projects', 'freelancer_id') ? 'freelancer_id' : 'frelancer_id';
        return $this->belongsTo(User::class, $foreignKey);
    }

    public function freelancer()
    {
        $foreignKey = Schema::hasColumn('projects', 'freelancer_id') ? 'freelancer_id' : 'frelancer_id';
        return $this->belongsTo(User::class, $foreignKey);
    }

    public function getFreelancerIdAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return $this->attributes['frelancer_id'] ?? null;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function payments()
    {
        return $this->hasOne(Payment::class);
    }

}
