<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Competition;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];

    public function competitions() : HasMany
    {
        return $this->hasMany(Competition::class);
    }
}
