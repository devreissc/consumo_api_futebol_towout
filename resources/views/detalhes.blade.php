@extends('layouts.app')

@section('title', 'Detalhes')

@section('content')
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    @if(isset($team) && count($team['response']) > 0)
        @php
            $estadio = $team['response'][0]['venue'];

            $team = $team['response'][0]['team'];
        @endphp

        <input type="hidden" value="{{ $team['id'] }}" id="teamId">

        <div class="row mb-4">
            <div class="col-12">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="selectOption" class="form-label">Campeonato:</label>
                        <select id="selectLeague" class="form-control">
                            <option value="">Selecione...</option>
                            {{-- Carregamento por ajax --}}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="selectOption" class="form-label">Temporada (Ano):</label>
                        <input type="date" class="form-control" id="filterDate" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 text-center">
                        <a href="#" id="filterInfos" class="btn btn-primary w-100">Filtrar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h2>Detalhes do Time: {{ $team['name'] }}</h2>
                <p><strong>Logo:</strong> <img src="{{ $team['logo'] }}" alt="{{ $team['name'] }}" style="width: 50px; height: 50px;"></p>
                <p><strong>Nome:</strong> {{ $team['name'] }}</p>
                <p><strong>Código:</strong> {{ $team['code'] }}</p>
                <p><strong>País:</strong> {{ $team['country'] }}</p>
                <p><strong>Ano de Fundação:</strong> {{ $team['founded'] }}</p>
            </div>
            <div class="col-6">
                <h2>Detalhes do Estádio: {{ $estadio['name'] }}</h2>
                <p><strong>Foto:</strong> <img src="{{ $estadio['image'] }}" alt="{{ $estadio['image'] }}" style="width: 50px; height: 50px;"></p>
                <p><strong>Nome:</strong> {{ $estadio['name'] }}</p>
                <p><strong>Cidade:</strong> {{ $estadio['city'] }}</p>
                <p><strong>Capacidade:</strong> {{ $estadio['capacity'] . ' pessoas' }}</p>
                <p><strong>Endereço:</strong> {{ $estadio['address'] }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 my-2">
                <div class="card card-custom" id="latestResultsCard">
                    <h5 class="text-center">Últimos Jogos</h5>
                    <table class="table table-striped" id="latestResults">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th class="text-end">Time da casa</th>
                                <th></th>
                                <th class="text-start">Time visitante</th>
                                <th class="text-start">Local da partida</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Carregamento por ajax --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-12 my-2">
                <div class="card card-custom" id="nextMatchesCard">
                    <h5 class="text-center">Próximos Jogos</h5>
                    <table class="table table-striped" id="nextMatches">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th class="text-end">Time da casa</th>
                                <th></th>
                                <th class="text-start">Time visitante</th>
                                <th class="text-start">Local da partida</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Carregamento por ajax --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <p>Nenhum time encontrado.</p>
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var FootballScripts = {
                init: function(){
                    var teamId = $('#teamId').val();
                    FootballScripts.loadLeagues();
                    FootballScripts.utils(teamId);
                },
                utils: function(teamId){
                    $('#filterInfos').on('click', function() {
                        var leagueId = $('#selectLeague').val();
                        var seasonDate = $('#filterDate').val();
                        var seasonYear = seasonDate ? new Date(seasonDate).getFullYear() : new Date().getFullYear();
                        
                        FootballScripts.loadLatestMatches(leagueId, seasonYear, seasonDate, teamId);
                        FootballScripts.loadNextMatches(leagueId, seasonYear, seasonDate, teamId);
                    });
                },
                loadLeagues: function(){
                    $.ajax({
                        url: '{{ route("getLeagues") }}',
                        type: 'GET',
                        success: function(response) {
                            if (response.leagues && Object.keys(response.leagues.errors).length > 0) {
                                var errorMessage = response.leagues.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                toastr.error(errorMessage);
                            } else if (response.leagues && Object.keys(response.leagues.response).length > 0) {
                                var leagues = response.leagues.response;
                                console.log(leagues);
                                $.each(leagues, function(index, league) {
                                    $('#selectLeague').append('<option value="'+league.league.id+'">'+league.league.name+'</option>');
                                });
                            } else {
                                toastr.error('Erro ao carregar os campeonatos, por favor, recarregue a página e tente novamente..');
                            }
                        },
                        error: function() {
                            toastr.error('Erro ao carregar os campeonatos.');
                        }
                    });
                },
                loadLatestMatches: function(leagueId, seasonYear, seasonDate, teamId){
                    if (leagueId && seasonYear && seasonDate && teamId) {
                        $.ajax({
                            url: '{{ route("getLatestMatchesByLeague") }}',
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear,
                                seasonDate: seasonDate,
                                teamId: teamId
                            },
                            success: function(response) {
                                if (response.matches && Object.keys(response.matches.errors).length > 0) {
                                    var errorMessage = response.matches.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                    toastr.error(errorMessage);
                                    
                                    $('#latestResults').hide();
                                    $('#latestResultsCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">' + errorMessage + '</div>');
                                } else if (response.matches && Object.keys(response.matches.response).length > 0) {
                                    var matches = response.matches.response;

                                    $.each(matches, function(index, match) {
                                        var fixtureDate = match.fixture.date;
                                        var dateObj = new Date(fixtureDate);
                                        var formattedDate = dateObj.toLocaleString('pt-BR', { timeZone: 'UTC' });

                                        $('#latestResults tbody').append(
                                            '<tr>' +
                                                '<td class="text-start">' + formattedDate + '</td>' +
                                                '<td class="text-end">' + match.teams.home.name + '<img src="' + match.teams.home.logo + '" alt="' +  match.teams.home.name + '" style="width: 30px; height: 30px; margin-left: 10px;"></td>' +
                                                '<td class="text-center">'+ match.goals.home+' VS '+ match.goals.away+'</td>' +
                                                '<td class="text-start"><img src="' + match.teams.away.logo + '" alt="' +  match.teams.away.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' + match.teams.away.name + '</td>' +
                                                '<td class="text-start">'+ match.fixture.venue.name+'</td>'+
                                            '</tr>'
                                        );
                                    });

                                    $('#latestResults').show();
                                } else {
                                    toastr.error('Nenhuma partida localizada encontrado para este campeonato.');
                                    $('#latestResults').hide();
                                    $('#latestResultsCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">Nenhuma partida localizada encontrado para este campeonato.</div>');
                                }
                            },
                            error: function() {
                                toastr.error('Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.');
                            }
                        });
                    } else {
                        $('#latestResults tbody').empty();
                        toastr.error('Por favor, preencha todos os campos do filtro de pesquisa.');
                    }
                },
                loadNextMatches: function(leagueId, seasonYear, seasonDate, teamId){
                    if (leagueId && seasonYear && seasonDate && teamId) {
                        $.ajax({
                            url: '{{ route("getNextMatchesByLeague") }}',
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear,
                                seasonDate: seasonDate,
                                teamId: teamId
                            },
                            success: function(response) {
                                if (response.matches && Object.keys(response.matches.errors).length > 0) {
                                    var errorMessage = response.matches.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                    toastr.error(errorMessage);

                                    $('#nextMatches').hide();
                                    $('#nextMatchesCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">' + errorMessage + '</div>');
                                } else if (response.matches && Object.keys(response.matches.response).length > 0) {
                                    var matches = response.matches.response;
                                    var dataAtual = new Date();
                                    var mesAtual = dataAtual.getMonth();
                                    var anoAtual = dataAtual.getFullYear();

                                    $.each(matches, function(index, match) {
                                        var fixtureDate = match.fixture.date;
                                        var dateObj = new Date(fixtureDate);
                                        var formattedDate = dateObj.toLocaleString('pt-BR', { timeZone: 'UTC' });

                                        var rowAtual = '<tr>' +
                                            '<td class="text-start">' + formattedDate + '</td>' +
                                            '<td class="text-end">' + match.teams.home.name + '<img src="' + match.teams.home.logo + '" alt="' + match.teams.home.name + '" style="width: 30px; height: 30px; margin-left: 10px;"></td>';

                                        if (seasonYear < anoAtual || dateObj.getMonth() < mesAtual) { //Significa que os próximos jogos ainda não aconteceram
                                            rowAtual += '<td class="text-center">' + match.goals.home + ' VS ' + match.goals.away + '</td>';
                                        } else {
                                            rowAtual += '<td class="text-center"> VS </td>';
                                        }

                                        rowAtual += '<td class="text-start"><img src="' + match.teams.away.logo + '" alt="' + match.teams.away.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' + match.teams.away.name + '</td>' +
                                            '<td class="text-start">' + match.fixture.venue.name + '</td>' +
                                            '</tr>';

                                        $('#nextMatches tbody').append(rowAtual);
                                    });

                                    $('#nextMatches').show();
                                } else {
                                    toastr.error('Nenhuma partida localizada encontrado para este campeonato.');
                                    $('#nextMatches').hide();
                                    $('#nextMatchesCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">Nenhuma partida localizada encontrado para este campeonato.</div>');
                                }
                            },
                            error: function() {
                                toastr.error('Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.');
                            }
                        });
                    }else{
                        toastr.error('Por favor, preencha todos os campos do filtro de pesquisa.');
                    }
                }
            }

            FootballScripts.init();
        });
    </script>
@endsection