<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Teaching;
use App\Models\Fillier;
use App\Models\CustomSessionDate;
use App\Models\Progress;
use App\Models\Vacance;
use App\Models\Groupe;
use App\Models\ProgressWeekly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleController extends Controller
{
 

    public function updateWeeklyProgress(Request $request)
    {
        $request->validate([
            'moduleId' => 'required|integer',
            'weekIndex' => 'required|integer',
            'hoursCompleted' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:completed,absent,pending'
        ]);

        $teachingId = $request->moduleId;
        $weekIndex = $request->weekIndex;
        $hoursCompleted = $request->hoursCompleted;
        $status = $request->status ?? 'completed';

        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $totalModuleHours = $teaching->hours;
        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            [
                'hours_completed' => 0,
                'hours_affected' => json_encode([])
            ]
        );

        $hoursAffected = json_decode($progress->hours_affected, true) ?: [];

        $currentTotal = array_sum($hoursAffected);
        if (isset($hoursAffected[$weekIndex])) {
            $currentTotal -= $hoursAffected[$weekIndex];
        }

        if ($currentTotal + $hoursCompleted > $totalModuleHours) {
            return response()->json([
                'success' => false,
                'message' => "Cannot exceed total module hours. Maximum hours you can enter is " .
                    ($totalModuleHours - $currentTotal)
            ], 422);
        }

        $hoursAffected[$weekIndex] = $hoursCompleted;

        $totalHours = array_sum($hoursAffected);

        $progress->hours_affected = json_encode($hoursAffected);
        $progress->hours_completed = $totalHours;
        $progress->save();

        return $this->getModuleDetails($teachingId);
    }

    

    public function updateModuleDate(Request $request)
    {
        $request->validate([
            'moduleId' => 'required|integer',
            'dateType' => 'required|string|in:module-start,module-exam',
            'newDate' => 'required|date_format:Y-m-d'
        ]);
        $teachingId = $request->moduleId;
        $dateType = $request->dateType;
        $newDate = $request->newDate;

        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($dateType === 'module-start') {
            $teaching->module_start_date = $newDate;
            $teaching->save();
        } else if ($dateType === 'module-exam') {
            $teaching->final_exam_date = $newDate;
            $teaching->save();
        }


        return $this->getModuleDetails($teachingId);
    }

 
    public function updateProgressSessionDate(Request $request)
    {
        $request->validate([
            'moduleId' => 'required|integer',
            'weekIndex' => 'required|integer',
            'newDate' => 'required|date_format:Y-m-d'
        ]);

        $teachingId = $request->moduleId;
        $weekIndex = $request->weekIndex;
        $newDate = $request->newDate;

        $dateObj = Carbon::parse($newDate);
        if ($dateObj->dayOfWeek === 0) { // 0 = Sunday
            return response()->json([
                'error' => 'Progress sessions cannot be scheduled on Sundays.'
            ], 422);
        }

        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            ['hours_progress' => 0]
        );

        DB::table('custom_session_dates')->updateOrInsert(
            [
                'id_progress' => $progress->id_progress,
                'week_index' => $weekIndex
            ],
            ['session_date' => $newDate]
        );

        return $this->getModuleDetails($teachingId);
    }

   
    public function updateSessionStatus(Request $request)
    {
        $request->validate([
            'moduleId' => 'required|integer',
            'weekIndex' => 'required|integer',
            'status' => 'required|string|in:completed,absent,pending',
            'hoursCompleted' => 'required_if:status,completed|numeric'
        ]);

        $teachingId = $request->moduleId;
        $weekIndex = $request->weekIndex;
        $status = $request->status;
        $hoursCompleted = $request->hoursCompleted ?? 0;

        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            ['hours_completed' => 0]
        );


        $totalHours = Progress::where('id_progress', $progress->id_progress)
            ->sum('hours_affected');

        $progress->hours_completed = $totalHours;
        $progress->save();

        return $this->getModuleDetails($teachingId);
    }

    public function saveCalendarData(Request $request)
    {
        $moduleData = json_decode($request->input('moduleData'), true);

        if (!$moduleData || !is_array($moduleData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data received'
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($moduleData as $module) {
                $progress = Progress::updateOrCreate(
                    ['id_teaching' => $module['moduleId']],
                    [
                        'hours_completed' => $module['completedHours'],
                        'hours_affected' => json_encode($module['weeklyProgress']), // Save weeklyProgress as JSON
                        'remaining_hours' => $module['remainingHours'],
                        'module_start_date' => $module['startDate'],
                        'final_exam_date' => $module['examDate'],
                        'weekly_hours' => $module['weeklyHours']
                    ]
                );

            

                if (!empty($module['customSessionDates'])) {
                    CustomSessionDate::where('id_progress', $progress->id_progress)->delete();

                    foreach ($module['customSessionDates'] as $weekIndex => $sessionDate) {
                        if (empty($sessionDate)) {
                            continue;
                        }

                        CustomSessionDate::create([
                            'id_progress' => $progress->id_progress,
                            'week_index' => $weekIndex,
                            'session_date' => $sessionDate
                        ]);
                    }
                }
            }

            DB::commit();

            $updatedTeachings = Teaching::with([
                'progress.customSessionDates',
                'module',
                'group'
            ])
                ->where('id_user', auth()->id())
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Changes saved successfully',
                'updatedData' => $updatedTeachings,
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Calendar data update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }






    
    public function getModuleDetails($teachingId)
    {
        $teaching = Teaching::with(['module', 'progress.weeklyProgress'])
            ->where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        return $this->formatModuleData($teaching);
    }

 
    private function formatModuleData($teaching)
    {
        $progress = $teaching->progress;
        $weeklyProgressData = [];
        $weeklyStatusData = [];
        $completedHours = 0;
        if ($progress) {
            $hoursAffected = json_decode($progress->hours_affected, true) ?? [];

            foreach ($hoursAffected as $weekIndex => $hours) {
                $weeklyProgressData[$weekIndex] = $hours;
                $completedHours += $hours ?? 0;
            }
        }

        $weeklyHours = $progress->weekly_hours ?? 5; 

        $totalWeeks = ceil($teaching->module->hours / $weeklyHours);

        $formattedWeeklyProgress = [];
        for ($i = 0; $i < $totalWeeks; $i++) {
            $formattedWeeklyProgress[$i] = $weeklyProgressData[$i] ?? null;
        }

        $customSessionDates = [];
        if ($progress) {
            $customDates = $progress->customSessionDates; 
            foreach ($customDates as $date) {
                $customSessionDates[$date->week_index] = $date->session_date;
            }
        }

        $moduleStartDate = $progress && $progress->module_start_date ? \Carbon\Carbon::parse($progress->module_start_date) : null;
        $finalExamDate = $progress && $progress->final_exam_date ? \Carbon\Carbon::parse($progress->final_exam_date) : null;

        return [
            'id' => $teaching->id_teaching,
            'name' => $teaching->module->name . '/' . $teaching->group->name,
            'id_group' => $teaching->group->id_group,
            'totalHours' => $teaching->module->hours,
            'weeklyHours' => $weeklyHours,
            'startDate' => $moduleStartDate ? $moduleStartDate->format('Y-m-d') : null,
            'completedHours' => $completedHours,
            'weeklyProgress' => $formattedWeeklyProgress,
            'examDate' => $finalExamDate ? $finalExamDate->format('Y-m-d') : null,
            'customSessionDates' => $customSessionDates
        ];
    }
    private function formatHolidayData($holiday)
    {
        return [
            'id' => $holiday->id_vacance,
            'name' => $holiday->description_vacance,
            'startDate' => $holiday->date_debut,
            'endDate' => $holiday->date_fin,
        ];
    }

    private function getHolidaysForUser($userId)
    {
        $userGroups = Teaching::where('id_user', $userId)
            ->with('group.fillier') 
            ->get();

        $groupIds = $userGroups->pluck('group.id_group')->toArray();
        $filliereIds = $userGroups->pluck('group.fillier.id_fillier')->unique()->toArray();

        $globalHolidays = Vacance::where('type', 'vacance')->get();

        $groupHolidays = Vacance::where('type', 'stage')
            ->whereIn('id_group', $groupIds)
            ->get();

        $examHolidays = Vacance::where('type', 'regional')
            ->whereIn('id_fillier', $filliereIds)
            ->get();

        $holidays = $globalHolidays->merge($groupHolidays)->merge($examHolidays);

        $formattedHolidays = [];
        foreach ($holidays as $holiday) {
            $formattedHolidays[] = $this->formatHolidayData($holiday);
        }

        return $formattedHolidays;
    }



    public function showCalendar()
    {
        $userId = Auth::id();


        $teachings = Teaching::with(['module', 'group'])
            ->where('id_user', $userId)
            ->get();


        $modules = [];

        foreach ($teachings as $teaching) {
            $modules[] = $this->formatModuleData($teaching);
        }
        $holidays = $this->getHolidaysForUser($userId);

        return view('formateur.calendar', compact('modules', 'holidays'));
    }
}
