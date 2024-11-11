@php use App\Models\Student\Quiz;use Carbon\Carbon; @endphp
@extends('student.layouts.main')


@section('content')

    <div class="row">
        @foreach($model as $item)

            @php
                $currentDate = Carbon::now()->format('Y-m-d');
                $dateToCompare = Carbon::parse(Quiz::getAttachmentById($item->quizId)->date)->format('Y-m-d');
//                            dd($currentDate . " == " . $dateToCompare)
            @endphp


            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary">
                                    <i class="ri-hand-coin-fill ri-24px"></i>
                                </span>
                            </div>
                            <h6 class="mb-0">{{$item->subjectName}}</h6>
                        </div>
                        <h6 class="mb-0 fw-normal">
                            @if($dateToCompare >= $currentDate)
                                <a href="{{route('student.quiz.show', ['id' => $item->quizId, 'subjectId' => $item->subjectId])}}" class="badge bg-label-info">
                                    {{$item->quizName}}
                                </a>
                            @else
                                {{$item->quizName}}
                            @endif
                        </h6>
                        <p class="mb-0">

                        @if($dateToCompare >= $currentDate)
                            <div class="sk-fold sk-primary" style="width: 20px; float: right;">
                                <div class="sk-fold-cube" style="font-size: 2px"></div>
                                <div class="sk-fold-cube" style="font-size: 2px"></div>
                                <div class="sk-fold-cube" style="font-size: 2px"></div>
                                <div class="sk-fold-cube" style="font-size: 2px"></div>
                            </div>
                        @endif

                        <span class="me-1 fw-medium">{{$dateToCompare}}</span>
                        <span class="text-muted"><i class="ri-attachment-2"></i> {{Quiz::getAttachmentById($item->quizId)->number}}</span>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
