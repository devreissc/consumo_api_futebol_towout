@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
    <!-- Verificando se há algum erro -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row mb-4">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="selectOption" class="form-label">Campeonato:</label>
                    <select id="selectLeague" class="form-control">
                        <option value="">Selecione...</option>
                        
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
    
    <div class="row">
        <div class="col-md-12 my-2">
            <div class="card card-custom">
                <h5 class="text-center mb-3">Times</h5>
                <div class="row" id="cardTimes">
    
                </div>
            </div>
        </div>
        <div class="col-md-12 my-2">
            <div class="card card-custom" id="latestResultsCard">
                <h5 class="text-center">Últimos Resultados</h5>
                <table class="table table-striped" id="latestResults">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th class="text-right">Time da casa</th>
                            <th class="text-center">Placar</th>
                            <th class="text-left">Time visitante</th>
                            <th class="text-left">Local da partida</th>
                        </tr>
                    </thead>
                    <tbody>
                        
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
                            <th>Time da casa</th>
                            <th>Time visitante</th>
                            <th>Local da partida</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var FootballScripts = {
                init: function() {
                    FootballScripts.loadLeagues();
                    this.utils(); // Corrigido para chamar a função utils corretamente
                },
                utils: function() {
                    $('#filterInfos').on('click', function() {
                        var leagueId = $('#selectLeague').val();
                        var seasonDate = $('#filterDate').val();
                        var seasonYear = seasonDate ? new Date(seasonDate).getFullYear() : new Date().getFullYear();

                        FootballScripts.loadTeams(leagueId, seasonYear);
                        FootballScripts.loadLatestMatches(leagueId, seasonYear, seasonDate);
                        FootballScripts.loadNextMatches(leagueId, seasonYear, seasonDate);
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
                            } else if (response.leagues && response.leagues.response) {
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
                loadTeams: function(leagueId, seasonYear){
                    $.ajax({
                        url: '{{ route("getTeamsByLeague") }}',
                        type: 'GET',
                        data: { 
                            leagueId: leagueId,
                            seasonYear: seasonYear
                        },
                        success: function(response) {
                            if (response.teams && response.teams.errors) {
                                var errorMessage = response.teams.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                toastr.error(errorMessage);

                                $('#cardTimes').html('<div style="text-align: center; padding: 20px; font-weight: bold;">'+errorMessage+'</div>');

                            } else if (response.teams && response.teams.response) {
                                var teams = response.teams.response;
                                $('#cardTimes').empty();
                                
                                $.each(teams, function(index, team) {
                                    $('#cardTimes').append(`
                                        <div class="col-md-4">
                                            <a href="#" data-team-id="${team.team.id}" class="btn btn-team">
                                                <img src="${team.team.logo}" alt="${team.team.name}" style="width: 30px; height: 30px; margin-right: 10px;">
                                                ${team.team.name} (${team.team.code || 'N/A'})
                                            </a>
                                        </div>
                                    `);
                                });
                            } else {
                                toastr.error('Nenhum time foi encontrado nesta liga.');

                                $('#cardTimes').html('<div style="text-align: center; padding: 20px; font-weight: bold;">Nenhum time foi encontrado nesta liga.</div>');
                            }
                        },
                        error: function() {
                            toastr.error('Erro ao carregar os times. Por favor, recarregue a página e tente novamente.');
                        }
                    });
                },
                loadLatestMatches: function(leagueId, seasonYear, seasonDate){
                    if (leagueId && seasonYear) {
                        $.ajax({
                            url: '{{ route("getLatestMatchesByLeague") }}',
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear,
                                seasonDate: seasonDate
                            },
                            success: function(response) {
                                if (response.matches && response.matches.errors) {
                                    var errorMessage = response.matches.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                    toastr.error(errorMessage);
                                    
                                    $('#latestResults').hide();
                                    $('#latestResultsCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">' + errorMessage + '</div>');
                                } else if (response.matches && response.matches.response) {
                                    var matches = response.matches.response;

                                    $.each(matches, function(index, match) {
                                        var fixtureDate = match.fixture.date;
                                        var dateObj = new Date(fixtureDate);
                                        var formattedDate = dateObj.toLocaleString('pt-BR', { timeZone: 'UTC' });

                                        $('#latestResults tbody').append(
                                            '<tr>' +
                                                '<td class="text-left">' + formattedDate + '</td>' +
                                                '<td class="text-right">' + match.teams.home.name + '<img src="' + match.teams.home.logo + '" alt="' +  match.teams.home.name + '" style="width: 30px; height: 30px; margin-right: 10px;"></td>' +
                                                '<td class="text-center">'+ match.goals.home+' VS '+ match.goals.away+'</td>' +
                                                '<td class="text-left"><img src="' + match.teams.away.logo + '" alt="' +  match.teams.away.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' + match.teams.away.name + '</td>' +
                                                '<td class="text-left">'+ match.fixture.venue.name+'</td>'+
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
                    }
                },
                loadNextMatches: function(leagueId, seasonYear, seasonDate){
                    $.ajax({
                        url: '{{ route("getNextMatchesByLeague") }}',
                        type: 'GET',
                        data: { 
                            leagueId: leagueId,
                            seasonYear: seasonYear,
                            seasonDate: seasonDate
                        },
                        success: function(response) {
                            if (response.matches && response.matches.errors) {
                                var errorMessage = response.matches.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                toastr.error(errorMessage);
                                
                                $('#nextMatches').hide();
                                $('#nextMatchesCard').html('<div style="text-align: center; padding: 20px; font-weight: bold;">' + errorMessage + '</div>');
                            } else if (response.matches && response.matches.response) {
                                var matches = response.matches.response;

                                $.each(matches, function(index, match) {
                                    var fixtureDate = match.fixture.date;
                                    var dateObj = new Date(fixtureDate);
                                    var formattedDate = dateObj.toLocaleString('pt-BR', { timeZone: 'UTC' });

                                    $('#nextMatches tbody').append(
                                        '<tr>' +
                                            '<td class="text-left">' + formattedDate + '</td>' +
                                            '<td class="text-right">' + match.teams.home.name + '<img src="' + match.teams.home.logo + '" alt="' +  match.teams.home.name + '" style="width: 30px; height: 30px; margin-right: 10px;"></td>' +
                                            '<td class="text-left"><img src="' + match.teams.away.logo + '" alt="' +  match.teams.away.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' + match.teams.away.name + '</td>' +
                                            '<td class="text-left">'+ match.fixture.venue.name+'</td>'+
                                        '</tr>'
                                    );
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
                }
            };

            FootballScripts.init(); // Agora a função é chamada corretamente
        });
    </script>
@endsection
