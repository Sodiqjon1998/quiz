@extends('backend.layouts.main')


@section('content')
    <style>
        table,
        tr,
        th,
        td {
            padding: 7px !important;
            font-size: 12px;
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.student.index') }}">O'quvchilar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Batafsil</li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-header">
            {{$model->name}} sinfi
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <tr>
                    <th>Id</th>
                    <th>{{$model->id}}</th>
                </tr>
                <tr>
                    <th>Sinf nomi</th>
                    <th>{{$model->name}}</th>
                </tr>
                <tr>
                    <th>Kordinator</th>
                    <th>{{\App\Models\Classes::getKoordinator($model->koordinator_id)}}</th>
                </tr>
            </table>
        </div>
    </div>

@endsection
