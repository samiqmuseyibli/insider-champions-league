<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\League;
use App\Services\FixtureService;
use App\Services\LeagueService;
use App\Services\TeamService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FixtureController extends Controller
{
    /**
     * @var LeagueService
     */
    private LeagueService $leagueService;

    /**
     * @var FixtureService
     */
    private FixtureService $fixtureService;

    /**
     * @var TeamService
     */
    private TeamService $teamService;

    /**
     * @param LeagueService $leagueService
     */
    public function __construct(LeagueService $leagueService, FixtureService $fixtureService, TeamService $teamService)
    {
        $this->leagueService = $leagueService;
        $this->fixtureService = $fixtureService;
        $this->teamService = $teamService;
    }

    /**
     * @param Request $request
     * @return View|Application|Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function fixtures(Request $request): View|Application|Factory|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $week = 0;
        if ($request->get('week')) {
            $week = $request->get('week');
        }

        $league = League::with([
            'teams' => function ($query) {
                return $query->orderByDesc('points')->orderByDesc('gd');
            }
        ])
            ->latest()
            ->first();

        if ($league == null) {
            return redirect()->route('app');
        }

        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->orderBy('week')
            ->get();

        return view('league.standings', compact('fixtures', 'week', 'league'));
    }

    /**
     * @param Request $request
     *
     * @return bool|RedirectResponse
     */
    public function play(Request $request): bool|RedirectResponse
    {
        $league = League::with('teams')->latest()->first();

        if ($league == null) {
            return redirect()->route('app');
        }

        $week = $request->week;
        if ($week === "all") {
            $this->playAllFixtures($league);
        } else {
            $week = intval($week);

            $this->playNextWeekFixtures($league, ++$week);
        }
//        $this->teamService->updateTeamStatistics($league);

        return redirect()->route('fixtures', ['week' => $week ?? '']);
    }

    /**
     * @param League $league
     * @return void
     */
    protected function playAllFixtures(League $league): void
    {
        for ($week = 1; $week <= 6; $week++) {
            $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
                ->where('league_id', $league->id)
                ->where('week', $week)
                ->whereNull('played_at')
                ->get();

            $this->fixtureService->playFixtures($fixtures);
        }
    }

    /**
     * @param League $league
     * @param int $week
     * @return void
     */
    protected function playNextWeekFixtures(League $league, int $week): void
    {
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->where('week', $week)
            ->whereNull('played_at')
            ->get();

        $this->fixtureService->playFixtures($fixtures);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeFixture(Request $request): JsonResponse
    {
        $fixture = Fixture::find($request->input('fixture'));

        if ($fixture->home_team_goals !== null && $fixture->away_team_goals !== null) {
            $fixture->home_team_goals = $request->input('home');
            $fixture->away_team_goals = $request->input('away');
            $fixture->save();

            $this->teamService->updateTeamStatistics($fixture->league);

            return response()->json(['success' => true, 'message' => 'Fixture updated successfully'], Response::HTTP_ACCEPTED);
        }

        return response()->json(['success' => false, 'message' => 'Game not played yet'], Response::HTTP_BAD_GATEWAY);
    }

}
