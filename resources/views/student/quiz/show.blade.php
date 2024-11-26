@php use App\Models\Student\Quiz; @endphp
@extends('student.layouts.main')


@section('content')
    @php
        $date = Quiz::getAttachmentById($quiz->id)->date;
        $time = Quiz::getAttachmentById($quiz->id)->time;
        $dateTime = strtotime($date. " " .$time);
        $dateTimeFormat = date('H:i:s', $dateTime);

    @endphp
    <div class="card">
        <h6 id="timer" class="text-md-end p-3">
            <i class="ri-time-fill"></i> {{$dateTimeFormat}}
        </h6>
        <form action="{{route('student.quiz.store')}}" id="quizForm" method="post">
            @csrf
            @php $qCount = 0 @endphp
            @foreach($questions as $key => $question)
                <div class="card-header">
                    <strong>{{++$key}})</strong> {{$question->name}}
                </div>
                    <?php
                    $options = Quiz::getOptionById($question->id)
                    ?>
                Subject<input type="text" name="subjectId" id="" value="{{$subject->id}}">
                Quiz <input type="text" name="quizId" id="" value="{{$quiz->id}}">
                Question <input type="text" name="question[]" id="" value="{{$question->id}}">
                Answer <input type="text" name="option[]" id="ans_{{$key}}" value="">

                <ol type="A">
                    @foreach($options as $k => $option)
                        <div class="card-body">
                            <li>
                                &nbsp;<input type="radio" class="selectOption" id="" name="options{{$key}}" value="{{$option->id}}"
                                             data-id="{{$key}}">
                                <label for="ans_{{$key}}">{{$option->name}}</label>
                            </li>
                        </div>
                    @endforeach
                </ol>
                <hr>
                @php ++$qCount @endphp
            @endforeach
            <button id="submitBtn" class="badge bg-label-hover-success text-md-end" type="submit">Testni yakunlash</button>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".selectOption").click(function () {
                let no = $(this).attr('data-id');
                $("#ans_" + no).val($(this).val());
            })

           let timeParts = @json(explode(":", $dateTimeFormat));
           let hours = parseInt(timeParts[0]);
           let minutes = parseInt(timeParts[1]);
           let second = parseInt(timeParts[2]);

           let quizId = @json($quiz->id);
           let subjectId = @json($subject->id);
           let userId = @json(Auth::user()->id);




            // Testni yakunlash tugmasiga confirm dialogini qo'shish
            $('#submitBtn').click(function() {
                if (confirm("Rostdan ham testni yakunlamoqchimisiz?")) {
                    $('#quizForm').submit();
                }
            });
        });
    </script>
@endsection
