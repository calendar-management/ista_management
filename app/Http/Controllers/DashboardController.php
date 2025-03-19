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
        
        // Get all teachings for the formateur
        $teachings = Teaching::with(['group', 'module', 'progress'])
            ->where('id_user', $formateur->id)
            ->get()
            ->groupBy('id_group');

        // Get unique groups with their modules and progress
        $groups = collect();
        
        foreach ($teachings as $groupId => $groupTeachings) {
            $group = $groupTeachings->first()->group;
            $modules = $groupTeachings->map(function ($teaching) {
                $progress = $teaching->progress;
                $module = $teaching->module;
                
                // Calculate progress percentage
                $progressPercentage = 0;
                if ($progress) {
                    $totalHours = $module->hours;
                    $completedHours = $progress->hours_completed;
                    $progressPercentage = ($totalHours > 0) ? 
                        round(($completedHours / $totalHours) * 100) : 0;
                }
                
                return [
                    'name' => $module->name,
                    'progress' => $progressPercentage,
                    'id' => $module->id_module
                ];
            });
            
            $groups->push([
                'id' => $group->id_group,
                'name' => $group->name,
                'modules' => $modules
            ]);
        }

        return view('formateur.dashboard', compact('groups'));
    }

    public function groupDetail($groupId)
    {
        $formateur = auth()->user();
        
        $teachings = Teaching::with(['group', 'module', 'progress'])
            ->where('id_user', $formateur->id)
            ->where('id_group', $groupId)
            ->get();

        $group = $teachings->first()->group;
        
        return view('formateur.group_detail', compact('teachings', 'group'));
    }
}
