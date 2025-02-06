@extends('layouts.app')

@section('title', 'Times')

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
                    <input type="number" class="form-control" id="seasonYear" min="1900" max="2100" step="1" placeholder="2025">
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
            <div class="card card-custom">
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
            <div class="card card-custom">
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
            var TimesScripts = {
                init: function() {
                    FootballScripts.loadLeagues();
                    this.utils(); // Corrigido para chamar a função utils corretamente
                },
                utils: function() {
                    $('#filterInfos').on('click', function() {
                        var leagueId = $('#selectLeague').val();
                        var seasonYear = $('#seasonYear').val();

                        FootballScripts.loadTeams(leagueId, seasonYear);
                        FootballScripts.loadLatestMatches(leagueId, seasonYear);
                        FootballScripts.loadNextMatches(leagueId);
                    });
                },
                loadLeagues: function(){
                    $.ajax({
                        url: '{{ route("getLeagues") }}', // Verifique se esta rota existe no Laravel
                        type: 'GET',
                        success: function(response) {
                            if(response.leagues && response.leagues.response) {
                                var leagues = response.leagues.response;

                                $.each(leagues, function(index, league) {
                                    $('#selectLeague').append('<option value="'+league.league.id+'">'+league.league.name+'</option>');
                                });
                            } else {
                                alert('Nenhum time encontrado para esta liga.');
                                $('#latestResults tbody').empty();
                            }
                        },
                        error: function() {
                            alert('Erro ao carregar os times.');
                        }
                    });
                },
                loadTeams: function(leagueId, seasonYear){
                    if (leagueId && seasonYear) {
                        $.ajax({
                            url: '{{ route("getTeamsByLeague") }}', // Verifique se esta rota existe no Laravel
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear
                            },
                            success: function(response) {
                                if(response.teams && response.teams.response) {
                                    var teams = response.teams.response;
                                    // console.log(teams);

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
                                    alert('Nenhum time encontrado para esta liga.');
                                }
                            },
                            error: function() {
                                alert('Erro ao carregar os times.');
                            }
                        });
                    } else {
                        $('#cardTimes').empty();
                    }
                },
                loadLatestMatches: function(leagueId, seasonYear){
                    if (leagueId && seasonYear) {
                        $.ajax({
                            url: '{{ route("getLatestMatchesByLeague") }}', // Verifique se esta rota existe no Laravel
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear
                            },
                            success: function(response) {
                                console.log('---ultimas');
                                console.log(response);
                                if(response.matches && response.matches.response) {
                                    var matches = response.matches.response;
                                    // console.log(matches);

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
                                } else {
                                    alert('Nenhum time encontrado para esta liga.');
                                }
                            },
                            error: function() {
                                alert('Erro ao carregar os times.');
                            }
                        });
                    } else {
                        $('#latestResults tbody').empty();
                    }
                },
                loadNextMatches: function(leagueId, seasonYear){
                    if (leagueId && seasonYear) {
                        $.ajax({
                            url: '{{ route("getNextMatchesByLeague") }}', // Verifique se esta rota existe no Laravel
                            type: 'GET',
                            data: { 
                                leagueId: leagueId,
                                seasonYear: seasonYear
                            },
                            success: function(response) {
                                if(response.matches && response.matches.response) {
                                    var matches = response.matches.response;
                                    console.log(matches);

                                    $.each(matches, function(index, match) {
                                        var fixtureDate = match.fixture.date;
                                        var dateObj = new Date(fixtureDate);
                                        var formattedDate = dateObj.toLocaleString('pt-BR', { timeZone: 'UTC' });

                                        $('#latestResults tbody').append(
                                            '<tr>' +
                                                '<td>' + formattedDate + '</td>' +
                                                '<td>' + match.teams.home.name + '<img src="' + match.teams.home.logo + '" alt="' +  match.teams.home.name + '" style="width: 30px; height: 30px; margin-right: 10px;"></td>' +
                                                '<td><img src="' + match.teams.away.logo + '" alt="' +  match.teams.away.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' + match.teams.away.name + '</td>' +
                                                '<td>'+ match.fixture.venue.name+'</td>'+
                                            '</tr>'
                                        );
                                    });
                                } else {
                                    alert('Nenhum time encontrado para esta liga.');
                                }
                            },
                            error: function() {
                                alert('Erro ao carregar os times.');
                            }
                        });
                    } else {
                        $('#latestResults tbody').empty();
                    }
                }
            };

            TimesScripts.init(); // Agora a função é chamada corretamente
        });
    </script>
@endsection
