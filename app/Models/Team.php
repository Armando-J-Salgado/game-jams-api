<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Handover;
use App\Models\User;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;


    public function competitions() : BelongsToMany
    {
        return $this->belongsToMany(Competition::class);
    }

    public function admin() : BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users() : HasMany
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function handovers() : HasMany
    {
        return $this->hasMany(Handover::class);
    }
}
