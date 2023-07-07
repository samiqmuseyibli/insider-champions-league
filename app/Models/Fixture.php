<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fixture extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_goals',
        'away_team_goals',
        'played_at'
    ];

    /**
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }
}
