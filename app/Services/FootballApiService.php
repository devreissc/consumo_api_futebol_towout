<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FootballApiService{
    protected $apiUrl; 
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('FOOTBALL_API_URL', 'https://v3.football.api-sports.io/');
        $this->apiKey = env('FOOTBALL_API_KEY');
    }

    public function getLeagues()
    {
        return $this->makeRequest('leagues', ['country' => 'Brazil']);
    }

    public function getTeamsByLeague($leagueId, $seasonYear)
    {
        return $this->makeRequest('teams', ['league' => $leagueId, 'season' => $seasonYear]);
    }

    public function getTeams($params){
        return $this->makeRequest('teams', $params);
    }

    public function getLatestMatchesByLeague($leagueId, $seasonYear, $status, $seasonDateFrom, $seasonDate){
        $params = [
            'league' => $leagueId, 
            'season' => $seasonYear,
            'status' => $status, // Partidas finalizadas
            'from' => $seasonDateFrom,
            'to' => $seasonDate,
        ];
    
        return $this->makeRequest('fixtures', $params);
    }

    public function getNextMatchesByLeague($leagueId, $season, $status, $initialDate, $seasonDateTo){
        $params = [
            'league' => $leagueId,
            'season' => $season,
            'status' => $status, // Partidas agendadas, não iniciadas
            'from' => $initialDate,
            'to' => $seasonDateTo,
        ];
    
        return $this->makeRequest('fixtures', $params);
    }

    private function makeRequest($endpoint, $params = [])
    {
        $response = Http::withHeaders([
            'x-apisports-key' => $this->apiKey,
        ])->withoutVerifying()->get($this->apiUrl . $endpoint, $params);

        return $response->json();
    }
}