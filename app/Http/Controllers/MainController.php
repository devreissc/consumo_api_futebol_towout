<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FootballApiService;
use Carbon\Carbon;

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
        $seasonYear = $request->input('seasonYear');

        if (!$leagueId) {
            return response()->json(['teams' => []]);
        }

        $teams = $this->footballService->getTeamsByLeague($leagueId, $seasonYear);

        return response()->json([
            'teams' => $teams
        ]);
    }

    public function getLatestMatchesByLeague(Request $request)
    {
        $leagueId = $request->input('leagueId');
        $seasonYear = $request->input('seasonYear');
        $seasonDate = $request->input('seasonDate');
        $status = 'FT-AET-PEN';
        $teamId = $request->input('teamId');
        
        if (!$seasonDate) {
            return response()->json(['error' => 'Data não fornecida'], 400);
        }

        $date = Carbon::createFromFormat('Y-m-d', $seasonDate);

        $seasonDateFrom = $date->subMonth()->format('Y-m-d'); // Data inicial da pesquisa

        if (!$leagueId) {
            return response()->json(['matches' => []]);
        }

        $matches = $this->footballService->getLatestMatchesByLeague($leagueId, $seasonYear, $status, $seasonDateFrom, $seasonDate, $teamId);

        return response()->json([
            'matches' => $matches
        ]);
    }

    public function getNextMatchesByLeague(Request $request)
    {
        $leagueId = $request->input('leagueId');
        $seasonYear = $request->input('seasonYear');
        $initialDate = $request->input('seasonDate');
        $teamId = $request->input('teamId');

        $status = 'TBD-NS';
        $anoAtual = date('Y');
        
        if($seasonYear < $anoAtual){
            $status = 'FT-AET-PEN';
        }

        if (!$leagueId) {
            return response()->json(['matches' => []]);
        }

        if (!$initialDate) {
            return response()->json(['error' => 'Data não fornecida'], 400);
        }

        $date = Carbon::createFromFormat('Y-m-d', $initialDate);

        $seasonDateTo = $date->addMonths()->format('Y-m-d');

        $matches = $this->footballService->getNextMatchesByLeague($leagueId, $seasonYear, $status, $initialDate, $seasonDateTo, $teamId);

        return response()->json([
            'matches' => $matches
        ]);
    }

    public function getTeams(Request $request){
        $requestData = $request->all();

        $params = array_merge([
            'country' => 'Brazil' 
        ], $requestData);

        $teams = $this->footballService->getTeams($params);

        return response()->json([
            'teams' => $teams
        ]);
    }

    public function detalhes($id, $name){

        if ($id > 0 && !empty($name)) {
            $params = [
                'country' => 'Brazil',
                'id' => $id
            ];
    
            $team = $this->footballService->getTeams($params);
    
            return view('detalhes', [
                'team' => $team
            ]);
        } else {
            return redirect()->route('football.times')->with('error', 'ID inválido ou nome vazio.');
        }
    }
}
