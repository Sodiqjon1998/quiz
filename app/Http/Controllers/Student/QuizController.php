<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Models\QuizTime;
use App\Models\Student\Quiz;
use App\Models\Subjects;
use Carbon\Carbon;
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
        return redirect()->route('student.quiz.result', $examId);

    }

    /**
     * Display the specified resource.
     */

    public function show(string $id, string $subjectId)
    {
        $subject = Subjects::findOrFail($subjectId);
        $quiz = Quiz::findOrFail($id);
//        $examAttachmentCount = Exam::where('quiz_id', $id)
//            ->where('user_id', \Auth::user()->id)
//            ->where('subject_id', $subjectId)
//            ->count();
//
//        $attachment = Attachment::getAttamptById($id);
//
//        if (!$attachment) {
//            return view('student.quiz.error', [
//                'message' => "Imtihon ma'lumoti topilmadi.",
//            ]);
//        }
//
//        if ($attachment->number <= $examAttachmentCount) {
//            return view('student.quiz.error', [
//                'message' => "Urunishlar qolmadi",
//                'date' => $attachment->date
//            ]);
//        }
//
//        $examDate = Carbon::parse($attachment->date);
//        $today = Carbon::today();
//
//        if ($examDate->isToday()) {
            $questions = Question::where('quiz_id', '=', $id)
                ->where('status', '=', Question::STATUS_ACTIVE)
                ->get();

            return view('student.quiz.show', [
                'questions' => $questions,
                'quiz' => $quiz,
                'subject' => $subject
            ]);
//        } else if ($examDate->isFuture()) {
//            return view('student.quiz.error', [
//                'message' => "Qo'yilgan imtihon vaqti kelmadi",
//                'date' => $attachment->date
//            ]);
//        } else {
//            return view('student.quiz.error', [
//                'message' => "Qo'yilgan imtihon vaqti tugadi!",
//                'date' => $attachment->date
//            ]);
//        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function result(string $id)
    {
        $exam = Exam::findOrFail($id);
        $examAnswers = ExamAnswer::where('exam_id', '=', $id)->get();

        return view('student.quiz.result', [
            'exam' => $exam,
            'examAnswers' => $examAnswers,
        ]);
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
        $quizId = $request->input('quizId');
        $userId = $request->input('userId');
        $subjectId = $request->input('subjectId');
        $hours = (int)$request->input('hours');
        $minutes = (int)$request->input('minutes');
        $seconds = (int)$request->input('seconds');

        if ($hours < 0 || $minutes < 0 || $seconds < 0) {
            return response()->json(['error' => 'Vaqt noto\'g\'ri!']);
        }

        DB::table('quiz_time')
            ->updateOrInsert(
                ['quiz_id' => $quizId, 'user_id' => $userId, 'subject_id' => $subjectId],
                ['hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds]
            );

        return response()->json(['message' => 'Vaqt muvaffaqiyatli saqlandi']);
    }


    public function getTime(Request $request)
    {
        $request->validate([
            'quizId' => 'required|integer',
            'userId' => 'required|integer',
        ]);

        // Fetch the saved time for the quiz and user
        $quizTime = QuizTime::where('quiz_id', $request->quizId)
            ->where('user_id', $request->userId)
            ->first();

        // Check if the time data exists and return it
        if ($quizTime) {
            return response()->json([
                'hours' => $quizTime->hours,
                'minutes' => $quizTime->minutes,
                'seconds' => $quizTime->seconds,
            ]);
        }

        return response()->json(['message' => 'Quiz topilmadi'], 404);
    }

    public function clearTime(Request $request)
    {
        // Kiritilgan maʼlumotlarni tekshirish
        $quizId = $request->input('quizId');
        $userId = \Auth::user()->id;

        if (!$quizId || !$userId) {
            return response()->json(['status' => 'error', 'message' => 'Kerakli maʼlumotlar yetarli emas.']);
        }

        try {
            // Vaqtni o'chirish
            $deleted = DB::table('quiz_time')
                ->where('quiz_id', $quizId)
                ->where('user_id', $userId)
                ->delete();

            if ($deleted) {
                return response()->json(['status' => 'success', 'message' => 'Vaqt muvaffaqiyatli tozalandi.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Vaqt topilmadi yoki tozalashda xatolik yuz berdi.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Xatolik yuz berdi: ' . $e->getMessage()]);
        }
    }

}
