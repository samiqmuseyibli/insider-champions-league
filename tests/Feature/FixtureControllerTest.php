<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\League;
use App\Services\FixtureService;
use App\Services\LeagueService;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FixtureControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    private FixtureService $fixtureService;
    private LeagueService $leagueService;
    private TeamService $teamService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureService = $this->mock(FixtureService::class);
        $this->leagueService = $this->mock(LeagueService::class);
        $this->teamService = $this->mock(TeamService::class);
    }

    public function test_fixtures()
    {
        // Arrange
        $this->leagueService->shouldReceive('createLeague')->andReturnUsing(function ($name) {
            $league = new League();
            $league->name = $name;
            $league->save();

            return $league;
        });
        $league = $this->leagueService->createLeague("TTT");
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->orderBy('week')
            ->get();

        // Act
        $response = $this->get('/fixtures');

        // Assert
        $response->assertOk();
        $response->assertViewIs('league.standings');
        $response->assertViewHas('fixtures', $fixtures);
        $response->assertViewHas('league', $league);
        $response->assertSeeText($league->name);
        $response->assertSeeText("Play all");
        $response->assertSeeText("Next week");
    }

    public function test_fixtures_empty_league()
    {
        // Arrange

        // Act
        $response = $this->get('/fixtures');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
    public function test_play_next_week()
    {
        // Arrange
        $week = 0;
        $this->leagueService->shouldReceive('createLeague')->andReturnUsing(function ($name) {
            $league = new League();
            $league->name = $name;
            $league->save();

            return $league;
        });
        $league = $this->leagueService->createLeague("TTT");
        $league->load('teams');
        $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $league->id)
            ->where('week', $week+1)
            ->whereNull('played_at')
            ->get();
        $this->fixtureService->shouldReceive('playFixtures')->with($fixtures);
        $this->teamService->shouldReceive('updateTeamStatistics')->with($league);

        // Act
        $response = $this->get('/fixtures/play?week='.$week);


        // Assert
        $response->assertRedirectToRoute('fixtures', ['week' => $week + 1]);
    }

    public function test_play_all_weeks()
    {

    }

    public function test_play_with_null_league()
    {

    }

    public function test_change_fixture()
    {


    }
}
