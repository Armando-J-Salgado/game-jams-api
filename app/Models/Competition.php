<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Team;
use App\Models\Category;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    /** @use HasFactory<\Database\Factories\CompetitionFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'prize_information',
        'tools_information',
        'max_teams',
        'total_teams',
        'start_date',
        'end_date',
        'is_finished',
        'category_id',
        'admin_id',
        'created_at',
        'updated_at'
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules() : HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
