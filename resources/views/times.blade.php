@extends('layouts.app')

@section('title', 'Times')

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
    <div class="row mb-4">
        <div class="col-12">
            <div class="row align-items-end">
                <form method="get" id="formTeamFilters">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="selectOption" class="form-label">Campeonato:</label>
                            <select name="league" id="selectLeague" class="form-control">
                                <option value="">Selecione...</option>
                                {{-- Carregamento por ajax --}}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="selectOption" class="form-label">Temporada (Ano):</label>
                            <input name="season" type="number" class="form-control" id="seasonYear" min="1900" max="2100" step="1" placeholder="2025">
                        </div>
                        <div class="col-md-4">
                            <label for="selectOption" class="form-label">Time:</label>
                            <input name="name" type="text" class="form-control" id="teamName">
                        </div>
                        <div class="col-md-2 text-center">
                            <a href="#" id="filterInfos" class="btn btn-primary w-100">Filtrar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 my-2">
            <div class="card card-custom">
                <h5 class="text-center mb-3">Times</h5>
                <div class="row" id="cardTimes">
                    {{-- Carregamento por ajax --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var TeamsPageScript = {
                init: function() {
                    this.utils();
                    TeamsPageScript.loadLeagues();
                    TeamsPageScript.loadTeams();
                    
                },
                utils: function() {
                    $('#filterInfos').on('click', function() {
                        TeamsPageScript.loadTeams();
                    });

                    $(document).on('click', '.btnTime', function(event) {
                        event.preventDefault();

                        var teamId = $(this).data('team-id');
                        var teamName = $(this).data('team-name');

                        if (teamId && teamName) {
                            var url = '/times/detalhes/' + encodeURIComponent(teamId) + '/' + encodeURIComponent(teamName);
                            window.location.href = url;
                        } else {
                            toastr.error('Erro ao obter detalhes do time.');
                        }
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
                        error: function(xhr) {
                            if (xhr.status === 429) {
                                toastr.error('Muitas requisições! Aguarde um momento antes de tentar novamente.');
                            } else {
                                toastr.error('Erro ao carregar os campeonatos. Tente novamente mais tarde.');
                            }
                        }
                    });
                },
                loadTeams: function(){
                    var formData = $('#formTeamFilters').serialize();
                    $.ajax({
                        url: '{{ route("getTeams") }}',
                        type: 'GET',
                        data: formData,
                        success: function(response) {
                            if (response.teams && Object.keys(response.teams.errors).length > 0) {
                                var errorMessage = response.teams.errors.plan || "Ocorreu um erro desconhecido, por favor, recarregue a página e tente novamente.";
                                toastr.error(errorMessage);

                                $('#cardTimes').html('<div style="text-align: center; padding: 20px; font-weight: bold;">'+errorMessage+'</div>');

                            } else if (response.teams && Object.keys(response.teams.response).length > 0) {
                                var teams = response.teams.response;

                                $('#cardTimes').empty();

                                $.each(teams, function(index, team) {
                                    $('#cardTimes').append(
                                        '<div class="col-md-4">' +
                                            '<a data-team-name="' + team.team.name + '" href="#" data-team-id="' + team.team.id + '" class="btnTime btn btn-team">' +
                                                '<img src="' + team.team.logo + '" alt="' + team.team.name + '" style="width: 30px; height: 30px; margin-right: 10px;">' +
                                                team.team.name + ' (' + (team.team.code ? team.team.code : 'N/A') + ')' +
                                            '</a>' +
                                        '</div>'
                                    );
                                });
                            } else {
                                toastr.error('Nenhum time foi encontrado nesta liga.');

                                $('#cardTimes').html('<div style="text-align: center; padding: 20px; font-weight: bold;">Nenhum time foi encontrado nesta liga.</div>');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 429) {
                                toastr.error('Muitas requisições! Aguarde um momento antes de tentar novamente.');
                            } else {
                                toastr.error('Erro ao carregar os times. Tente novamente mais tarde.');
                            }
                        }
                    });
                },
            };

            TeamsPageScript.init();
        });
    </script>
@endsection
