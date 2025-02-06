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

    public function getTeamsByLeague($leagueId, $season)
    {
        return $this->makeRequest('teams', ['league' => $leagueId, 'season' => $season]);
    }
    
    public function getLatestMatchesByLeague($leagueId, $season){
        $params = [
            'league' => $leagueId, 
            'season' => $season,
            'status' => 'FT',
            'from' => $season.'-12-01',
            'to' => $season.'-12-31',
        ];
    
        return $this->makeRequest('fixtures', $params);
    }

    public function getNextMatchesByLeague($leagueId, $season){
        $params = [
            'league' => $leagueId,
            'season' => $season,
            'status' => 'NS',
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