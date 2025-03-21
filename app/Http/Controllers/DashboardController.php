<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Groupe;
use App\Models\Teaching;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $formateur = auth()->user();

    //     // Get all teachings for the formateur with related data
    //     $teachings = Teaching::with(['group', 'module', 'progress', 'fillier'])
    //         ->where('id_user', $formateur->id)
    //         ->get()
    //         ->groupBy('id_group');

    //     // Get unique groups with their modules and progress
    //     $groups = collect();

    //     foreach ($teachings as $groupId => $groupTeachings) {
    //         $group = $groupTeachings->first()->group;
    //         $modules = $groupTeachings->map(function ($teaching) {
    //             $module = $teaching->module;
    //             $progress = $teaching->progress;

    //             return [
    //                 'id' => $module->id_module,
    //                 'name' => $module->name,
    //                 'code' => $module->code_module,

    //                 // Raw hours data from module
    //                 'total_hours' => $module->hours ?? 0,
    //                 'presentiel_hours' => $module->mh_presentiel ?? 0,
    //                 'distanciel_hours' => $module->mh_distanciel ?? 0,

    //                 // Raw progress data
    //                 'completed_hours' => $progress ? ($progress->hours_completed ?? 0) : 0,
    //                 'remaining_hours' => $progress ? ($progress->remaining_hours ?? 0) : 0,

    //                 // Dates
    //                 'start_date' => $progress ? $progress->module_start_date : null,
    //                 'exam_date' => $progress ? $progress->final_exam_date : null,

    //                 // Additional data
    //                 'weekly_hours' => $progress ? $progress->weekly_hours : null,
    //                 'teaching_id' => $teaching->id_teaching
    //             ];
    //         });

    //         $groups->push([
    //             'id' => $group->id_group,
    //             'name' => $group->name,
    //             'fillier' => $groupTeachings->first()->fillier->name ?? '',
    //             'niveau' => $group->niveau,
    //             'modules' => $modules
    //         ]);
    //     }

    //     return view('formateur.dashboard', compact('groups'));
    // }

    // public function index()
    // {
    //     $formateur = auth()->user();

    //     // Get all teachings for the formateur with related data
    //     $teachings = Teaching::with(['group', 'module', 'progress', 'fillier'])
    //         ->where('id_user', $formateur->id)
    //         ->get()
    //         ->groupBy('id_group');

    //     // Get unique groups with their modules and progress
    //     $groups = collect();

    //     foreach ($teachings as $groupId => $groupTeachings) {
    //         $group = $groupTeachings->first()->group;
    //         $modules = $groupTeachings->map(function ($teaching) {
    //             $module = $teaching->module;
    //             $progress = $teaching->progress;

    //             // Get total hours and split between presentiel and distanciel
    //             $totalHours = $module->hours ?? 0;
    //             $presentielHours = $module->mh_presentiel ?? 0;
    //             $distancielHours = $module->mh_distanciel ?? 0;

    //             // Initialize completed hours
    //             $completedHours = 0;
    //             $remainingHours = $totalHours;

    //             // Calculate progress percentage
    //             $progressPercentage = 0;

    //             if ($progress) {
    //                 $completedHours = $progress->hours_completed ?? 0;
    //                 $remainingHours = $progress->remaining_hours ?? ($totalHours - $completedHours);
    //                 $progressPercentage = ($totalHours > 0) ? 
    //                     round(($completedHours / $totalHours) * 100) : 0;
    //             }

    //             // Calculate presentiel and distanciel proportions
    //             // Assuming completed hours are distributed proportionally
    //             $presentielRatio = ($totalHours > 0) ? ($presentielHours / $totalHours) : 0;
    //             $distancielRatio = ($totalHours > 0) ? ($distancielHours / $totalHours) : 0;

    //             $presentielCompleted = round($completedHours * $presentielRatio, 1);
    //             $distancielCompleted = round($completedHours * $distancielRatio, 1);

    //             $presentielRemaining = round($presentielHours - $presentielCompleted, 1);
    //             $distancielRemaining = round($distancielHours - $distancielCompleted, 1);

    //             // Calculate individual progress percentages
    //             $presentielProgress = ($presentielHours > 0) ? 
    //                 round(($presentielCompleted / $presentielHours) * 100) : 0;
    //             $distancielProgress = ($distancielHours > 0) ? 
    //                 round(($distancielCompleted / $distancielHours) * 100) : 0;

    //             // Get start and exam dates
    //             $startDate = $progress ? $progress->module_start_date : null;
    //             $examDate = $progress ? $progress->final_exam_date : null;

    //             return [
    //                 'id' => $module->id_module,
    //                 'name' => $module->name,
    //                 'code' => $module->code_module,
    //                 'progress' => $progressPercentage,
    //                 'total_hours' => $totalHours,
    //                 'completed_hours' => $completedHours,
    //                 'remaining_hours' => $remainingHours,

    //                 // Presentiel data
    //                 'presentiel_hours' => $presentielHours,
    //                 'presentiel_completed' => $presentielCompleted,
    //                 'presentiel_remaining' => $presentielRemaining,
    //                 'presentiel_progress' => $presentielProgress,

    //                 // Distanciel data
    //                 'distanciel_hours' => $distancielHours,
    //                 'distanciel_completed' => $distancielCompleted,
    //                 'distanciel_remaining' => $distancielRemaining,
    //                 'distanciel_progress' => $distancielProgress,

    //                 // Dates
    //                 'start_date' => $startDate,
    //                 'exam_date' => $examDate,

    //                 // To track weekly progress if needed
    //                 'weekly_hours' => $progress ? $progress->weekly_hours : null,
    //                 'teaching_id' => $teaching->id_teaching
    //             ];
    //         });

    //         $groups->push([
    //             'id' => $group->id_group,
    //             'name' => $group->name,
    //             'fillier' => $groupTeachings->first()->fillier->name ?? '',
    //             'niveau' => $group->niveau,
    //             'modules' => $modules
    //         ]);
    //     }

    //     return view('formateur.dashboard', compact('groups'));
    // }

    public function index()
    {
        $formateur = auth()->user();

        // Get all teachings for the formateur with related data
        $teachings = Teaching::with(['group', 'module', 'progress', 'fillier'])
            ->where('id_user', $formateur->id)
            ->get()
            ->groupBy('id_group');

        // Get unique groups with their modules and progress
        $groups = collect();

        foreach ($teachings as $groupId => $groupTeachings) {
            $group = $groupTeachings->first()->group;
            $modules = $groupTeachings->map(function ($teaching) {
                $module = $teaching->module;
                $progress = $teaching->progress;

                // Get total hours and split between presentiel and distanciel
                $totalHours = $module->hours ?? 0;
                $presentielHours = $module->mh_presentiel ?? 0;
                $distancielHours = $module->mh_distanciel ?? 0;

                // Initialize completed hours
                $completedHours = $progress ? ($progress->hours_completed ?? 0) : 0;

                // Calculate presentiel and distanciel completed hours proportionally
                // Assuming completed hours are distributed proportionally
                $presentielRatio = ($totalHours > 0) ? ($presentielHours / $totalHours) : 0;
                $distancielRatio = ($totalHours > 0) ? ($distancielHours / $totalHours) : 0;

                $presentielCompleted = round($completedHours * $presentielRatio, 1);
                $distancielCompleted = round($completedHours * $distancielRatio, 1);

                return [
                    'id' => $module->id_module,
                    'name' => $module->name,
                    'code' => $module->code_module,

                    // Raw hours data from module
                    'total_hours' => $totalHours,
                    'presentiel_hours' => $presentielHours,
                    'distanciel_hours' => $distancielHours,

                    // Raw progress data
                    'completed_hours' => $completedHours,
                    'remaining_hours' => $progress ? ($progress->remaining_hours ?? 0) : 0,

                    // Calculated presentiel and distanciel completed hours
                    'presentiel_completed' => $presentielCompleted,
                    'distanciel_completed' => $distancielCompleted,

                    // Dates
                    'start_date' => $progress ? $progress->module_start_date : null,
                    'exam_date' => $progress ? $progress->final_exam_date : null,

                    // Additional data
                    'weekly_hours' => $progress ? $progress->weekly_hours : null,
                    'teaching_id' => $teaching->id_teaching
                ];
            });

            $groups->push([
                'id' => $group->id_group,
                'name' => $group->name,
                'fillier' => $groupTeachings->first()->fillier->name ?? '',
                'niveau' => $group->niveau,
                'modules' => $modules
            ]);
        }

        return view('formateur.dashboard', compact('groups'));
    }

}
