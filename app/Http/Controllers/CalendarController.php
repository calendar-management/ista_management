<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fillier;
use App\Models\Groupe;
use App\Models\Vacance;
use Illuminate\Http\Request;

class CalendarController extends Controller
{

    public function fetchVacations()
    {
        $vacations = Vacance::all();
        $groupes = Groupe::all();
        $filieres = Fillier::all();
        return response()->json([
            'vacations' => $vacations,
            'groupes' => $groupes,
            'filieres' => $filieres
        ]);
    }

    public function add(Request $request){
            
    
        $validated = $request->validate([
            'holidayStartDate' => 'required|date',
            'holidayEndDate' => 'nullable|date|after_or_equal:holidayStartDate',
            'eventType' => 'required|string',
            'groupSelect' => 'nullable|string',
            'filiereSelect' => 'nullable|string',
            'holidayDescription' => 'nullable|string',
        ]);
        
        
        Vacance::create([
            'description_vacance' => $validated['holidayDescription'] ?? null,
            'type' => $validated['eventType'],
            'id_group' => $validated['groupSelect'] ?? null,
            'date_debut' => $validated['holidayStartDate'],
            'date_fin' => $validated['holidayEndDate'] ?? $validated['holidayStartDate'], 
            'id_fillier' => $validated['filiereSelect'] ?? null,
        ]);
        
    
        return back()->with('ajouter_succ', 'Ajouté avec succès!');
    }
    public function destroy($id)
{
    $vacation = Vacance::find($id);
    
    
    if (!$vacation) {
        return response()->json(['error' => 'Vacation not found'], 404);
    }

    $vacation->delete();
    
    return response()->json(['success' => 'Vacation deleted successfully']);
}

    
}