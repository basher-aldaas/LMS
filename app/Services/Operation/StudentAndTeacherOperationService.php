<?php

namespace App\Services\Operation;

use App\Models\Course;
use App\Models\User;
use App\Notifications\SendReportAboutStudentMail;
use App\Notifications\SendReportAboutTeacherMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class StudentAndTeacherOperationService
{

    //function to get The most baying courses
    public function best_seller($subject_id)
    {
        $courses = Course::query()
            ->where('subject_id' , $subject_id)
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();

        $message = __('strings.getting The most baying courses');
        $code = 200;
        return [
            'courses' => $courses,
            'message' => $message,
            'code' => $code,
        ];
    }

    //function to send a report about a student
    public function send_report_student($request , $student_id)
    {
        $admin = User::query()->where('id' , 1)->first();
        $data = [];
        //المستخدم يلي عم يشتكي
        $data['user_id'] = Auth::id();
        //الطالب يلي اشتكو عليه
        $data['student_id'] = $student_id;
        //سبب الشكوى
        $data['reason'] = $request->reason;

        Notification::send($admin, new SendReportAboutStudentMail($data));

        $message = __('strings.Sending report successfully');
        $code = 200;
        return [
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ];
    }

    //function to send a report about a teacher
    public function send_report_teacher($request , $teacher_id)
    {

        $admin = User::query()->where('id' , 1)->first();
        $data = [];
        //المستخدم يلي عم يشتكي
        $data['user_id'] = Auth::id();
        //الطالب يلي اشتكو عليه
        $data['teacher_id'] = $teacher_id;
        //سبب الشكوى
        $data['reason'] = $request->reason;

        Notification::send($admin, new SendReportAboutTeacherMail($data));

        $message = __('strings.Sending report successfully');
        $code = 200;
        return [
            'data' => $data,
            'message' => $message,
            'code' => $code,
        ];
    }

    //function to get all reports
    public function get_reports() {
        if (Auth::id() == 1) {
            $notifications = \App\Models\Notification::query()
                ->where('data->reason', '!=', null)
                ->get();

            $reportsAboutStudents = [];
            $reportsAboutTeachers = [];

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true); // فك ترميز JSON

                // استخلاص المعرفات والأسباب
                $userId = isset($data['user_id']) ? $data['user_id'] : null;
                $studentId = isset($data['student_id']) ? $data['student_id'] : null;
                $teacherId = isset($data['teacher_id']) ? $data['teacher_id'] : null;
                $reason = isset($data['reason']) ? $data['reason'] : null;

                // جلب الأسماء بناءً على المعرفات
                $userName = $userId ? \App\Models\User::find($userId)->full_name : null;
                $studentName = $studentId ? \App\Models\User::find($studentId)->full_name : null;
                $teacherName = $teacherId ? \App\Models\User::find($teacherId)->full_name : null;

                // الفرز بناءً على الشخص المبلغ عنه
                if ($studentId) {
                    $reportsAboutStudents[] = [
                        'user_name' => $userName,
                        'student_name' => $studentName,
                        'reason' => $reason,
                    ];
                }

                if ($teacherId) {
                    $reportsAboutTeachers[] = [
                        'user_name' => $userName,
                        'teacher_name' => $teacherName,
                        'reason' => $reason,
                    ];
                }
            }

            $message = 'Getting all reports successfully';
            $code = 200;
        } else {
            $reportsAboutStudents = [];
            $reportsAboutTeachers = [];
            $message = 'You do not have permission';
            $code = 403;
        }

        return [
            'reports_about_students' => $reportsAboutStudents,
            'reports_about_teachers' => $reportsAboutTeachers,
            'message' => $message,
            'code' => $code,
        ];
    }



}

