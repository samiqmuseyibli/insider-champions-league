<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\LeagueService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LeagueController extends Controller
{
    /**
     * @var LeagueService
     */
    protected LeagueService $leagueService;

    /**
     * @param LeagueService $leagueService
     */
    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function index()
    {
        $teams = $this->leagueService->getAllTeams();
        return view('league.index', compact('teams'));
    }

    public function store(Request $request)
    {
        $league = $this->leagueService->createLeague('England Premier League');

        return response()->json(['league' => $league, 'success' => true], ResponseAlias::HTTP_CREATED);
    }


}
