<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teaching;
use Illuminate\Http\Request;

class FormateurController extends Controller
{
    public function dashboard()
    {
        $teachings = Teaching::where('id_user', auth()->id())
            ->with([
                'groupe.fillier',
                'module',
                'progress'
            ])
            ->get();

        // Group by groupe ID directly from the relationship
        $groupedTeachings = $teachings->groupBy(function($teaching) {
            return $teaching->groupe->id_group;
        });

        return view('formateur.dashboard', [
            'groupedTeachings' => $groupedTeachings
        ]);
    }
}
