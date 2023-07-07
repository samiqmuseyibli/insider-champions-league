<?php

namespace Tests\Feature;

use App\Http\Controllers\LeagueController;
use App\Services\LeagueService;
use App\Models\League;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class LeagueControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private LeagueService $leagueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leagueService = $this->mock(LeagueService::class);
    }

    public function test_index()
    {
        // Arrange
        $this->leagueService->shouldReceive('getAllTeams')->once()->andReturn([]);

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertOk();
        $response->assertViewIs('league.index');
        $response->assertSeeText("Create new League");
    }

    public function test_store()
    {
        // Arrange
        $this->leagueService->shouldReceive('createLeague')->once()->andReturn(new League());

        // Act
        $response = $this->post('/league');

        // Assert
        $response->assertCreated();
        $response->assertJson(['success' => true]);
    }
}
