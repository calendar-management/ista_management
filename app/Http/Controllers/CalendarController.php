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

    
    // public function add(Request $request){
    //     $validated = $request->validate([
    //         'holidayStartDate' => 'required|date',
    //         'holidayEndDate' => 'nullable|date|after_or_equal:holidayStartDate',
    //         'eventType' => 'required|string',
    //         'groupSelect' => 'nullable|array',  // Changed to array
    //         'groupSelect.*' => 'nullable|string',  // Validate each array element
    //         'filiereSelect' => 'nullable|string',
    //         'holidayDescription' => 'nullable|string',
    //     ]);
        
    //     // If stage type and groups are selected, create multiple records
    //     if ($validated['eventType'] === 'stage' && !empty($validated['groupSelect'])) {
    //         foreach ($validated['groupSelect'] as $groupId) {
    //             Vacance::create([
    //                 'description_vacance' => $validated['holidayDescription'] ?? null,
    //                 'type' => $validated['eventType'],
    //                 'id_group' => $groupId,
    //                 'date_debut' => $validated['holidayStartDate'],
    //                 'etablissement' => 'test',
    //                 'date_fin' => $validated['holidayEndDate'] ?? $validated['holidayStartDate'], 
    //                 'id_fillier' => null,
    //             ]);
    //         }
    //     } else {
    //         // Original logic for other event types
    //         Vacance::create([
    //             'description_vacance' => $validated['holidayDescription'] ?? null,
    //             'type' => $validated['eventType'],
    //             'id_group' => is_array($validated['groupSelect'] ?? null) ? null : ($validated['groupSelect'] ?? null),
    //             'date_debut' => $validated['holidayStartDate'],
    //             'etablissement' => 'test',
    //             'date_fin' => $validated['holidayEndDate'] ?? $validated['holidayStartDate'], 
    //             'id_fillier' => $validated['filiereSelect'] ?? null,
    //         ]);
    //     }
    
    //     return back()->with('ajouter_succ', 'Ajouté avec succès!');
    // }

    public function add(Request $request){
        $validated = $request->validate([
            'holidayStartDate' => 'required|date',
            'holidayEndDate' => 'nullable|date|after_or_equal:holidayStartDate',
            'eventType' => 'required|string',
            'groupSelect' => 'nullable|array',
            'groupSelect.*' => 'nullable|integer',
            'filiereSelect' => 'nullable|string',
            'holidayDescription' => 'nullable|string',
        ]);
        
        // If it's a stage event and groups are selected
        if ($validated['eventType'] === 'stage' && !empty($request->groupSelect)) {
            foreach ($request->groupSelect as $groupId) {
                Vacance::create([
                    'description_vacance' => $validated['holidayDescription'] ?? null,
                    'type' => $validated['eventType'],
                    'id_group' => $groupId,
                    'date_debut' => $validated['holidayStartDate'],
                    'etablissement' => 'test',
                    'date_fin' => $validated['holidayEndDate'] ?? $validated['holidayStartDate'],
                    'id_fillier' => null,
                ]);
            }
        } else {
            // For other event types
            Vacance::create([
                'description_vacance' => $validated['holidayDescription'] ?? null,
                'type' => $validated['eventType'],
                'id_group' => $request->has('groupSelect') && !is_array($request->groupSelect) ? $request->groupSelect : null,
                'date_debut' => $validated['holidayStartDate'],
                'etablissement' => 'test',
                'date_fin' => $validated['holidayEndDate'] ?? $validated['holidayStartDate'],
                'id_fillier' => $validated['filiereSelect'] ?? null,
            ]);
        }
    
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