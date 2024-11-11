@php use App\Models\Teacher\Question;use App\Models\Teacher\Quiz; @endphp
@extends('teacher.layouts.main')

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
            <li class="breadcrumb-item"><a href="{{route('teacher')}}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Savollar</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            Savollar
            <a href="{{ route('teacher.question.create') }}" class="badge bg-label-success badge-lg rounded-pill">
                <i style="font-size: 16px" class="ri-add-circle-line" style="font-size: 15px"></i>
            </a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                <tr>
                    <th style="width: 30px">T/R</th>
                    <th>Savol</th>
                    <th>Quiz nomi</th>
                    <th>Kiritilgan vaqti</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                {{ $item->name }}
                            </td>
                            <td>
                                {{ Question::getQuizById($item->quiz_id)->name }}
                            </td>
                            <td>
                                {{$item->created_at}}
                            </td>
                            <td>
                                @if ($item->status == Quiz::STATUS_ACTIVE)
                                    <small class="badge bg-label-success badge-sm rounded-pill">
                                        {{ Question::getStatus($item->status) }}
                                    </small>
                                @else
                                    <small class="badge bg-label-danger badge-sm rounded-pill">
                                        {{ Question::getStatus($item->status) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('teacher.question.edit', $item->id)}}"
                                   class="badge bg-label-info badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </a>
                                <a href="{{ route('teacher.question.show', $item->id) }}"
                                   class="badge bg-label-primary badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-eye-2-line"></i>
                                </a>
                                <form id="deleteForm" action="{{ route('teacher.question.destroy', $item->id) }}"
                                      method="POST" style="display: inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border: none"
                                            class="badge bg-label-danger badge-lg rounded-pill">
                                        <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">
                            <h5>
                                Savollar mavjud emas!
                            </h5>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>


    <script>
        document.getElementById('deleteForm').addEventListener('submit', function (event) {
            event.preventDefault();

            if (confirm('Haqiqatan ham ma\'lumotni o\'chirmoqchimisiz?')) {
                this.submit();
            }
        });
    </script>
@endsection


