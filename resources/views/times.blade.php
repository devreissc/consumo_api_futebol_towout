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
    </div>
@endsection

@section('scripts')
    
@endsection
