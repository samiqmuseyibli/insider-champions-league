<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;

class LeagueService
{
    /**
     * @var Team
     */
    protected Team $team;

    /**
     * @var Fixture
     */
    protected Fixture $fixture;

    /**
     * @param Team $team
     * @param Fixture $fixture
     */
    public function __construct(Team $team, Fixture $fixture)
    {
        $this->team = $team;
        $this->fixture = $fixture;
    }

    /**
     * @return array
     */
    public function getAllTeams(): array
    {
        return $this->team->getAllTeams();
    }

    /**
     * @param $leagueName
     *
     * @return League
     */
    public function createLeague($leagueName): League
    {
        $league = new League();
        $league->name = $leagueName;
        $league->save();

        $this->setTeams($league);
        $this->setFixtures($league);

        return $league;
    }

    /**
     * @param League $league
     *
     * @return void
     */
    private function setTeams(League $league): void
    {
        $teams = $this->getAllTeams();
        shuffle($teams);
        $teams = array_slice($teams, 0, 4);

        $teamData = [];
        foreach ($teams as $teamName) {
            $teamData[] = [
                'league_id' => $league->id,
                'name' => $teamName,
                'percent' => rand(0, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->team->insert($teamData);
    }

    /**
     * @param League $league
     *
     * @return void
     */
    private function setFixtures(League $league): void
    {
        $teamIds = $this->getTeamIdsByLeague($league);
        $rounds = $this->generateFixturesRounds($teamIds);
        $week = 1;

        foreach ($rounds as $round) {
            foreach ($round as $fixture) {
                $this->saveFixture($fixture[0], $fixture[1], $week, $league);
            }

            foreach ($round as $fixture) {
                $this->saveFixture($fixture[1], $fixture[0], $week + 3, $league);
            }

            $week++;
        }
    }

    /**
     * @param League $league
     *
     * @return array
     */
    protected function getTeamIdsByLeague(League $league): array
    {
        return $this->team->where('league_id', $league->id)->pluck('id')->toArray();
    }

    /**
     * @param array $teamIds
     *
     * @return array
     */
    protected function generateFixturesRounds(array $teamIds): array
    {
        $numberOfTeams = count($teamIds);
        $rounds = [];

        for ($i = 0; $i < $numberOfTeams - 1; $i++) {
            $round = [];

            for ($j = 0; $j < $numberOfTeams / 2; $j++) {
                $round[] = [$teamIds[$j], $teamIds[$numberOfTeams - 1 - $j]];
            }

            $teamIds[] = array_splice($teamIds, 1, 1)[0];
            $rounds[$i] = $round;
        }

        return $rounds;
    }

    /**
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @param int $week
     * @param League $league
     *
     * @return void
     */
    protected function saveFixture(int $homeTeamId, int $awayTeamId, int $week, League $league): void
    {
        $this->fixture->insert([
            'league_id' => $league->id,
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
            'week' => $week,
        ]);
    }
}
