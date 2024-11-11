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

            let timeParts = @json(explode(':', $dateTimeFormat));
            let hours = parseInt(timeParts[0]);
            let minutes = parseInt(timeParts[1]);
            let seconds = parseInt(timeParts[2]);

            let quizId = @json($quiz->id);
            let subjectId = @json($subject->id);
            let userId = @json(Auth::user()->id);

            let sessionKey = @json(session('quiz_time')['session_key']);

            function saveTimeToSession() {
                $.ajax({
                    url: "{{ route('student.quiz.saveTime') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        hours: hours,
                        minutes: minutes,
                        seconds: seconds,
                        quizId: quizId,
                        subjectId: subjectId,
                        userId: userId,
                        session_key: sessionKey
                    }
                });
            }

            // Sessiyadan vaqt va boshqa ma'lumotlarni olish
            function loadTimeFromSession() {
                $.ajax({
                    url: "{{ route('student.quiz.loadTime') }}", // Laravel ro‘yxati
                    method: 'GET',
                    success: function (data) {
                        if (data) {
                            hours = data.hours;
                            minutes = data.minutes;
                            seconds = data.seconds;
                            quizId = data.quizId;
                            subjectId = data.subjectId;
                            userId = data.userId;
                        }
                    }
                });
            }

            // Formani yuborish va sessiyani tozalash
            function submitFormAndClearSession() {
                $("form").submit(); // Formani yuborish

                // Sessiyani tozalash
                $.ajax({
                    url: "{{ route('student.quiz.clearSession') }}", // Laravel ro‘yxati
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        console.log('Sessiya tozalandi');
                    }
                });
            }

            // Vaqtni saqlash
            function startTimer() {
                loadTimeFromSession(); // Sahifa yuklanganda vaqtni tiklash

                let timer = setInterval(() => {
                    $("#timer").text(
                        hours.toString().padStart(2, "0") + ":" +
                        minutes.toString().padStart(2, "0") + ":" +
                        seconds.toString().padStart(2, "0")
                    );

                    if (hours === 0 && minutes === 0 && seconds === 0) {
                        clearInterval(timer);
                        alert("Vaqt tugadi!");
                        submitFormAndClearSession(); // Vaqt tugaganda formani yuborish va sessiyani tozalash
                    }

                    if (seconds === 0) {
                        if (minutes === 0) {
                            if (hours > 0) {
                                hours--;
                                minutes = 59;
                                seconds = 59;
                            }
                        } else {
                            minutes--;
                            seconds = 59;
                        }
                    } else {
                        seconds--;
                    }

                    saveTimeToSession(); // Har bir soniya o‘tganda vaqtni sessiyaga saqlash
                }, 1000);
            }

            startTimer();

            // Testni yakunlash tugmasiga confirm dialogini qo'shish
            $('#submitBtn').click(function() {
                if (confirm("Rostdan ham testni yakunlamoqchimisiz?")) {
                    $('#quizForm').submit();
                }
            });
        });
    </script>
@endsection
