<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Teaching;
use App\Models\Fillier;
use App\Models\CustomSessionDate;
// use App\Models\Data;
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



    /**
     * Update weekly progress for a module
     */
    // public function updateWeeklyProgress(Request $request)
    // {
    //     $request->validate([
    //         'moduleId' => 'required|integer',
    //         'weekIndex' => 'required|integer',
    //         'hoursCompleted' => 'required|numeric',
    //         'status' => 'nullable|string|in:completed,absent,pending' // Added status field
    //     ]);

    //     $teachingId = $request->moduleId;
    //     $weekIndex = $request->weekIndex;
    //     $hoursCompleted = $request->hoursCompleted;
    //     $status = $request->status ?? 'completed'; // Default to completed if not provided

    //     // Verify the teaching belongs to the authenticated user
    //     $teaching = Teaching::where('id_teaching', $teachingId)
    //         ->where('id_user', Auth::id())
    //         ->firstOrFail();

    //     // Find or create progress record
    //     $progress = Progress::firstOrCreate(
    //         ['id_teaching' => $teachingId], // Match against this
    //         [
    //             'hours_completed' => 0, // Default value if creating
    //             'hours_affected' => json_encode([]) // Default empty JSON if creating
    //         ]
    //     );

    //     // Decode the hours_affected JSON
    //     $hoursAffected = json_decode($progress->hours_affected, true);

    //     // Update the hours for the specific week
    //     $hoursAffected[$weekIndex] = $hoursCompleted;
    //     dd($hoursAffected);

    //     // Recalculate total hours completed
    //     $totalHours = array_sum($hoursAffected);

    //     // Update the progress record
    //     $progress->hours_affected = json_encode($hoursAffected);
    //     $progress->hours_completed = $totalHours;
    //     $progress->save();


    //     // Return updated module data
    //     return $this->getModuleDetails($teachingId);
    // }

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

        // Verify the teaching belongs to the authenticated user
        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Get the total hours for this module
        $totalModuleHours = $teaching->hours; // Assuming 'hours' is the field storing total hours

        // Find or create progress record
        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            [
                'hours_completed' => 0,
                'hours_affected' => json_encode([])
            ]
        );

        // Decode the hours_affected JSON
        $hoursAffected = json_decode($progress->hours_affected, true) ?: [];

        // Calculate current total excluding the week we're updating
        $currentTotal = array_sum($hoursAffected);
        if (isset($hoursAffected[$weekIndex])) {
            $currentTotal -= $hoursAffected[$weekIndex];
        }

        // Check if the new hours would exceed total module hours
        if ($currentTotal + $hoursCompleted > $totalModuleHours) {
            return response()->json([
                'success' => false,
                'message' => "Cannot exceed total module hours. Maximum hours you can enter is " .
                    ($totalModuleHours - $currentTotal)
            ], 422);
        }

        // Update the hours for the specific week
        $hoursAffected[$weekIndex] = $hoursCompleted;

        // Recalculate total hours completed
        $totalHours = array_sum($hoursAffected);

        // Update the progress record
        $progress->hours_affected = json_encode($hoursAffected);
        $progress->hours_completed = $totalHours;
        $progress->save();

        // Return updated module details
        return $this->getModuleDetails($teachingId);
    }

    /**
     * Update a module's dates (start date or exam date)
     */
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

        // Verify the teaching belongs to the authenticated user
        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($dateType === 'module-start') {
            // Update start date in Teaching record
            $teaching->module_start_date = $newDate;
            $teaching->save();
        } else if ($dateType === 'module-exam') {
            // Update exam date in Teaching record
            $teaching->final_exam_date = $newDate;
            $teaching->save();
        }

        // Save changes

        return $this->getModuleDetails($teachingId);
    }

    /**
     * Update a progress session date (for custom session scheduling)
     */
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

        // Check if the new date is a Sunday
        $dateObj = Carbon::parse($newDate);
        if ($dateObj->dayOfWeek === 0) { // 0 = Sunday
            return response()->json([
                'error' => 'Progress sessions cannot be scheduled on Sundays.'
            ], 422);
        }

        // Verify the teaching belongs to the authenticated user
        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Find or create progress record
        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            ['hours_progress' => 0]
        );

        // Update or create custom session date
        DB::table('custom_session_dates')->updateOrInsert(
            [
                'id_progress' => $progress->id_progress,
                'week_index' => $weekIndex
            ],
            ['session_date' => $newDate]
        );

        return $this->getModuleDetails($teachingId);
    }

    /**
     * Mark a session as completed or absent
     */
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

        // Verify the teaching belongs to the authenticated user
        $teaching = Teaching::where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        // Find or create progress record
        $progress = Progress::firstOrCreate(
            ['id_teaching' => $teachingId],
            ['hours_completed' => 0]
        );


        // Update total hours in progress
        $totalHours = Progress::where('id_progress', $progress->id_progress)
            ->sum('hours_affected');

        $progress->hours_completed = $totalHours;
        $progress->save();

        return $this->getModuleDetails($teachingId);
    }

    /**
     * Save all changes to the database
     */
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
                // 1. Insert or update in `progress` table
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

                // $teaching = ProgressWeekly::updateOrCreate(
                //     [
                //         'id_progress' => $progress->id_progress,

                //     ]
                // );

                // 3. Insert or update Custom Session Dates
                if (!empty($module['customSessionDates'])) {
                    // Remove existing session dates for this progress
                    CustomSessionDate::where('id_progress', $progress->id_progress)->delete();

                    foreach ($module['customSessionDates'] as $weekIndex => $sessionDate) {
                        // Skip if session_date is null or invalid
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

            // Fetch updated data with custom session dates
            $updatedTeachings = Teaching::with([
                'progress.customSessionDates', // Fetch custom session dates
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






    /**
     * Get detailed information for a specific module/teaching
     */
    public function getModuleDetails($teachingId)
    {
        $teaching = Teaching::with(['module', 'progress.weeklyProgress'])
            ->where('id_teaching', $teachingId)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        return $this->formatModuleData($teaching);
    }

    /**
     * Format module data for consistent output
     */
    private function formatModuleData($teaching)
    {
        $progress = $teaching->progress;
        $weeklyProgressData = [];
        $weeklyStatusData = [];
        $completedHours = 0;
        // dd($progress);
        // Process weekly progress data if it exists
        if ($progress) {
            // Decode hours_affected JSON
            $hoursAffected = json_decode($progress->hours_affected, true) ?? [];

            // Format weekly progress data for frontend
            foreach ($hoursAffected as $weekIndex => $hours) {
                $weeklyProgressData[$weekIndex] = $hours;
                $completedHours += $hours ?? 0;
            }
        }

        // Get weekly hours from the database (assuming it's stored in the module or teaching table)
        $weeklyHours = $progress->weekly_hours ?? 5; // Fallback to 5 if not found

        // Calculate total weeks needed
        $totalWeeks = ceil($teaching->module->hours / $weeklyHours);

        // Format weekly progress for frontend
        $formattedWeeklyProgress = [];
        for ($i = 0; $i < $totalWeeks; $i++) {
            $formattedWeeklyProgress[$i] = $weeklyProgressData[$i] ?? null;
        }

        // Get custom session dates
        $customSessionDates = [];
        if ($progress) {
            $customDates = $progress->customSessionDates; // Fetch from relationship
            foreach ($customDates as $date) {
                $customSessionDates[$date->week_index] = $date->session_date;
            }
        }

        // Ensure dates are Carbon instances before formatting
        $moduleStartDate = $progress && $progress->module_start_date ? \Carbon\Carbon::parse($progress->module_start_date) : null;
        $finalExamDate = $progress && $progress->exam_date ? \Carbon\Carbon::parse($progress->exam_date) : null;

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
        // Fetch the user's groups and their associated fillieres
        $userGroups = Teaching::where('id_user', $userId)
            ->with('group.fillier') // Eager load the group and filliere relationships
            ->get();

        // Extract group IDs and filliere IDs
        $groupIds = $userGroups->pluck('group.id_group')->toArray();
        $filliereIds = $userGroups->pluck('group.fillier.id_fillier')->unique()->toArray();

        // Fetch holidays that are visible to all users
        $globalHolidays = Vacance::where('type', 'vacance')->get();

        // Fetch holidays that are specific to the user's groups 
        $groupHolidays = Vacance::where('type', 'stage')
            ->whereIn('id_group', $groupIds)
            ->get();

        // Fetch holidays that are specific to the user's fillieres 
        $examHolidays = Vacance::where('type', 'regional')
            ->whereIn('id_fillier', $filliereIds)
            ->get();

        // Combine all holidays
        $holidays = $globalHolidays->merge($groupHolidays)->merge($examHolidays);
        // dd($holidays);
        // Format the holiday data for the frontend
        $formattedHolidays = [];
        foreach ($holidays as $holiday) {
            $formattedHolidays[] = $this->formatHolidayData($holiday);
        }

        return $formattedHolidays;
    }



    /**
     * Show calendar view with module data
     */
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

        // Pass both modules and holidays to the view
        return view('formateur.calendar', compact('modules', 'holidays'));
    }
}
