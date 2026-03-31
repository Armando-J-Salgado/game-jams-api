<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Team;
use App\Models\Module;

class Handover extends Model
{
    /** @use HasFactory<\Database\Factories\HandoverFactory> */
    use HasFactory;
    protected $fillable =[
        'title',
        'attachment',
        'is_delivered',
        'module_id',
        'team_id',
        'created_at',
        'updated_at'
    ];

    public function module() : BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function team() : BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
