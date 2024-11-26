<?php
?>

<script>
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
</script>
