<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Handover;
use App\Models\Competition;

class Module extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachments',
        'due_date',
        'competition_id',
        'created_at',
        'updated_at'
    ];

    public function competition() : BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function handovers() : HasMany
    {
        return $this->hasMany(Handover::class);
    }
}
