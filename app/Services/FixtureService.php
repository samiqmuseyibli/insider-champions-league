<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class FixtureService
{
    /**
     * @var Fixture
     */
    protected Fixture $fixture;

    /**
     * @param Fixture $fixture
     */
    public function __construct(Fixture $fixture)
    {
        $this->fixture = $fixture;
    }


    /**
     * @param Collection|array $fixtures
     *
     * @return void
     */
    public function playFixtures(Collection|array $fixtures): void
    {
        foreach ($fixtures as $fixture) {
            $homeGoals = $this->createScore($fixture->homeTeam->percent, 20, 1, 20);
            $awayGoals = $this->createScore($fixture->awayTeam->percent, 15, 1, 20);
            $this->updateFixtureResult($fixture, $homeGoals, $awayGoals);
        }
    }

    /**
     * @param $percent
     * @param $randMax
     * @param $randMin
     * @param $divideBy
     *
     * @return int
     */
    private function createScore($percent, $randMax, $randMin, $divideBy): int
    {
        $score = intval(($percent + rand($randMin, $randMax)) / $divideBy);
        return $score;
    }

    /**
     * @param Fixture $fixture
     * @param int $homeTeamGoalsCount
     * @param int $awayTeamGoalsCount
     *
     * @return void
     */
    private function updateFixtureResult(Fixture $fixture, int $homeTeamGoalsCount, int $awayTeamGoalsCount): void
    {
        $fixture->played_at = now();
        $fixture->home_team_goals = $homeTeamGoalsCount;
        $fixture->away_team_goals = $awayTeamGoalsCount;
        $fixture->save();
    }
}
