<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    /**
     * @var array|string[]
     */
    private array $allTeams =  [
        "Arsenal",
        "Aston Villa",
        "Blackburn Rovers",
        "Chelsea",
        "Coventry City",
        "Crystal Palace",
        "Everton",
        "Ipswich Town",
        "Leeds United",
        "Liverpool",
        "Manchester City",
        "Manchester United",
        "Middlesbrough",
        "Norwich City",
        "Nottingham Forest",
        "Oldham Athletic",
        "Queens Park Rangers",
        "Sheffield United",
        "Sheffield Wednesday",
        "Southampton",
        "Tottenham Hotspur",
        "Wimbledon",
    ];

    /**
     * @return array
     */
    public function getAllTeams(): array
    {
        return $this->allTeams;
    }

    /**
     * @return HasMany
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id')->orWhere('away_team_id', $this->id);
    }
}
