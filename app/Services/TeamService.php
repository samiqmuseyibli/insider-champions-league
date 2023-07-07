<?php

namespace App\Services;

use App\Models\League;
use App\Models\Team;

class TeamService
{
    /**
     * @param League $league
     * @return void
     */
    public function updateTeamStatistics(League $league): void
    {
        $league->load(['matches']);
        foreach ($league->teams as $team) {
            $team->win = $this->getTotalWin($team->id, $league);
            $team->draw = $this->getTotalDraw($team->id, $league);
            $team->lost = $this->getTotalLost($team->id, $league);
            $team->gf = $this->getTotalGF($team->id, $league);
            $team->ga = $this->getTotalGA($team->id, $league);
            $team->gd = $this->getTotalGD($team->id, $league);
            $team->points = $this->getTotalPoint($team->id, $league);
            $team->save();
        }
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalWin($teamId, League $league): int
    {
        $result = 0;
        $homeTeamMatches = $league->matches->where('home_team_id', $teamId)->whereNotNull('played_at');
        $awayTeamMatches = $league->matches->where('away_team_id', $teamId)->whereNotNull('played_at');
        foreach ($homeTeamMatches as $match) {
            if ($match->home_team_goals > $match->away_team_goals) {
                $result++;
            }
        }
        foreach ($awayTeamMatches as $match) {
            if ($match->away_team_goals > $match->home_team_goals) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalDraw($teamId, League $league): int
    {
        $result = 0;
        $homeTeamMatches = $league->matches->where('home_team_id', $teamId);
        $awayTeamMatches = $league->matches->where('away_team_id', $teamId);

        foreach ($homeTeamMatches as $match) {
            if ($match->home_team_goals == $match->away_team_goals && $match->played_at) {
                $result++;
            }
        }
        foreach ($awayTeamMatches as $match) {
            if ($match->away_team_goals == $match->home_team_goals && $match->played_at) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalLost($teamId, League $league): int
    {
        $result = 0;
        $homeTeamMatches = $league->matches->where('home_team_id', $teamId);
        $awayTeamMatches = $league->matches->where('away_team_id', $teamId);

        foreach ($homeTeamMatches as $match) {
            if ($match->home_team_goals < $match->away_team_goals) {
                $result++;
            }
        }
        foreach ($awayTeamMatches as $match) {
            if ($match->away_team_goals < $match->home_team_goals) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return float|int
     */
    private function getTotalPoint($teamId, League $league): float|int
    {
        return $this->getTotalWin($teamId, $league) * 3 + $this->getTotalDraw($teamId, $league) * 1;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalGF($teamId, League $league): int
    {
        $result = 0;
        $homeTeamMatches = $league->matches->where('home_team_id', $teamId);
        $awayTeamMatches = $league->matches->where('away_team_id', $teamId);

        foreach ($homeTeamMatches as $match) {
            $result += $match->home_team_goals;
        }
        foreach ($awayTeamMatches as $match) {
            $result += $match->away_team_goals;
        }

        return $result;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalGA($teamId, League $league): int
    {
        $result = 0;
        $homeTeamMatches = $league->matches->where('home_team_id', $teamId);
        $awayTeamMatches = $league->matches->where('away_team_id', $teamId);

        foreach ($homeTeamMatches as $match) {
            $result += $match->away_team_goals;
        }
        foreach ($awayTeamMatches as $match) {
            $result += $match->home_team_goals;
        }

        return $result;
    }

    /**
     * @param $teamId
     * @param League $league
     * @return int
     */
    private function getTotalGD($teamId, League $league): int
    {
        $totalGF = $this->getTotalGF($teamId, $league);
        $totalGA = $this->getTotalGA($teamId, $league);

        return $totalGF - $totalGA;
    }

    /**
     * @param League $league
     * @return float|int
     */
    public function getTotalPoints(League $league): float|int
    {
        $points = 0;
        foreach ($league->teams as $club) {
            $points += $this->getTotalPoint($club->id, $league);
        }
        return $points;
    }

    /**
     * @param League $league
     * @return mixed
     */
    public function getChampionshipPercentages(League $league): mixed
    {
        $teams = $league->teams;
        $teamWinningPercentages = [];

        foreach ($teams as $team) {
            $wins = $team->win;
            $matches = $team->fixtures->count();

            $winningPercentage = ($matches > 0) ? ($wins / $matches) * 100 : 0;

            $teamWinningPercentages[$team->id] =  round($winningPercentage, 2);
        }

        // Check the sum does not exceed 100
        return $this->normalizePercentages($teamWinningPercentages);
    }

    /**
     * @param $teamWinningPercentages
     * @return mixed
     */
    private function normalizePercentages($teamWinningPercentages): mixed
    {
        $totalPercentage = 0;

        foreach ($teamWinningPercentages as $teamWinningPercentage) {
            $totalPercentage += $teamWinningPercentage;
        }

        if ($totalPercentage > 100) {
            $factor = 100 / $totalPercentage;

            foreach ($teamWinningPercentages as &$teamWinningPercentage) {
                $teamWinningPercentage *= $factor;
                $teamWinningPercentage = round($teamWinningPercentage, 2);
            }
        }

        return $teamWinningPercentages;
    }

}
