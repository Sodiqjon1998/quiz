<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Models\Student\Quiz;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = DB::table('quiz')
            ->select([
                'quiz.name as quizName',
                'quiz.id as quizId',
                'quiz.subject_id as quizSubjectId',
                'quiz.classes_id as quizClassesId',
                'quiz.status as quizStatus',
                'subjects.id as subjectId',
                'subjects.name as subjectName',
                'classes.id as classesId',
                'classes.name as classesName',
            ])
            ->leftJoin('subjects', 'subjects.id', '=', 'quiz.subject_id')
            ->leftJoin('classes', 'classes.id', '=', 'quiz.classes_id')
//            ->leftJoin('users', 'users.id', '=', 'classes.id')
            ->where('classes.id', '=', \Auth::user()->classes_id)
            ->paginate(20);
        return view('student.quiz.index', compact('model'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teacher.quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $questionCount = count($request->input('question', []));

        $examId = Exam::insertGetId([
            'subject_id' => $request->input('subjectId'),
            'quiz_id' => $request->input('quizId'),
            'user_id' => \Auth::user()->id,
            'created_by' => \Auth::user()->id,
            'updated_by' => \Auth::user()->id,
        ]);
        for ($i = 0; $i < $questionCount; $i++) {
            if (!empty($request->input('option')[$i])) {
                ExamAnswer::insert([
                    'exam_id' => $examId,
                    'question_id' => $request->input('question')[$i],
                    'option_id' => $request->input('option')[$i],
                    'created_by' => \Auth::user()->id,
                    'updated_by' => \Auth::user()->id,
                ]);
            }
        }
        return redirect()->route('student');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $subjectId)
    {
        $subject = Subjects::findOrFail($subjectId);
        $quiz = Quiz::findOrFail($id);
        $questions = Question::where('quiz_id', '=', $id)->where('status', '=', Question::STATUS_ACTIVE)->get();
        return view('student.quiz.show', [
            'questions' => $questions,
            'quiz' => $quiz,
            'subject' => $subject
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }



    public function saveTime(Request $request)
    {
        session([
            'quiz_time' => [
                'hours' => $request->hours,
                'minutes' => $request->minutes,
                'seconds' => $request->seconds,
                'quizId' => $request->quizId,
                'subjectId' => $request->subjectId,
                'userId' => $request->userId
            ]
        ]);

        return response()->json(['status' => 'success']);
    }

    // Vaqt va boshqa ma'lumotlarni sessiyadan olish
    public function loadTime(Request $request)
    {
        $sessionKey = $request->session_key; // Sessiya kaliti so‘rovdan olish
        $time = session('quiz_time');

        if ($time && $time['session_key'] == $sessionKey) {
            return response()->json($time);
        } else {
            return response()->json([
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'quizId' => null,
                'subjectId' => null,
                'userId' => null
            ]);
        }
    }

    // Sessiyani tozalash
    public function clearSession(Request $request)
    {
        session()->forget('quiz_time');
        return response()->json(['status' => 'success']);
    }
}
