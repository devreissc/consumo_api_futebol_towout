<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FootballApiService;

class MainController extends Controller
{
    protected $footballService;

    public function __construct(FootballApiService $footballService)
    {
        $this->footballService = $footballService;
    }

    public function index()
    {   
        return view('index');
    }

    public function times()
    {
        return view('times');
    }

    public function getLeagues(){
        $leagues = $this->footballService->getLeagues();

        if (!$leagues) {
            return response()->json(['leagues' => []]);
        }

        return response()->json([
            'leagues' => $leagues
        ]);
    }

    public function getTeamsByLeague(Request $request)
    {
        $leagueId = $request->input('leagueId');
        $season = $request->input('seasonYear');

        if (!$leagueId) {
            return response()->json(['teams' => []]);
        }

        $teams = $this->footballService->getTeamsByLeague($leagueId, $season);

        return response()->json([
            'teams' => $teams
        ]);
    }

    public function getLatestMatchesByLeague(Request $request)
    {
        $leagueId = $request->input('leagueId');
        $season = $request->input('seasonYear');

        if (!$leagueId) {
            return response()->json(['matches' => []]);
        }

        $matches = $this->footballService->getLatestMatchesByLeague($leagueId, $season);

        return response()->json([
            'matches' => $matches
        ]);
    }

    public function getNextMatchesByLeague(Request $request)
    {
        $leagueId = $request->input('leagueId');
        $season = $request->input('seasonYear');

        if (!$leagueId) {
            return response()->json(['matches' => []]);
        }

        $matches = $this->footballService->getNextMatchesByLeague($leagueId, $season);

        return response()->json([
            'matches' => $matches
        ]);
    }
}
