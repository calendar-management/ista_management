<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Groupe;
use App\Models\Teaching;

class DashboardController extends Controller
{
    public function index()
    {
        $formateur = auth()->user();

        $teachings = Teaching::with(['group', 'module', 'progress', 'fillier'])
            ->where('id_user', $formateur->id)
            ->get()
            ->groupBy('id_group');

        $groups = collect();

        foreach ($teachings as $groupId => $groupTeachings) {
            $group = $groupTeachings->first()->group;
            $modules = $groupTeachings->map(function ($teaching) {
                $module = $teaching->module;
                $progress = $teaching->progress;

                $totalHours = $module->hours ?? 0;
                $presentielHours = $module->mh_presentiel ?? 0;
                $distancielHours = $module->mh_distanciel ?? 0;

                $completedHours = $progress ? ($progress->hours_completed ?? 0) : 0;

                $presentielRatio = ($totalHours > 0) ? ($presentielHours / $totalHours) : 0;
                $distancielRatio = ($totalHours > 0) ? ($distancielHours / $totalHours) : 0;

                $presentielCompleted = round($completedHours * $presentielRatio, 1);
                $distancielCompleted = round($completedHours * $distancielRatio, 1);

                return [
                    'id' => $module->id_module,
                    'name' => $module->name,
                    'code' => $module->code_module,

                    'total_hours' => $totalHours,
                    'presentiel_hours' => $presentielHours,
                    'distanciel_hours' => $distancielHours,

                    'completed_hours' => $completedHours,
                    'remaining_hours' => $progress ? ($progress->remaining_hours ?? 0) : 0,

                    'presentiel_completed' => $presentielCompleted,
                    'distanciel_completed' => $distancielCompleted,

                    'start_date' => $progress ? $progress->module_start_date : null,
                    'exam_date' => $progress ? $progress->final_exam_date : null,

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
