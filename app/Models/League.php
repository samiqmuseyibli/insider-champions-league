<?php

namespace App\Models;

use App\Services\TeamService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $fillable = [];


    /**
     * @return HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * @return float|int
     */
    public function getTotalPointsAttribute(): float|int
    {
        return (new TeamService())->getTotalPoints($this->find($this->id));
    }


    public function getChampionshipPercentage($league)
    {
        return (new TeamService())->getChampionshipPercentages($league);
    }
}
