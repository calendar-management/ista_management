<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fillier;
use App\Models\Teaching;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Models\Formateur;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Progress;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;



class FormateurController extends Controller
{
    public function index()
    {
        $auth = auth()->user();
        $formateurs = User::where('role', 'formateur')->where('etablissement', $auth->etablissement)->paginate(10);

        return view('admin.gestion_formateur', compact('formateurs'));
    }
    public function search(Request $request)
{
    $search = $request->input('search');
    $auth = auth()->user();

    $formateurs = User::where('role', 'formateur')
        ->where('etablissement', $auth->etablissement)
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->paginate(10);

    return view('admin.gestion_formateur', compact('formateurs'));
}

    public function add(Request $request)
    {
        $auth = auth()->user();
        User::create([
            'name' => $request->name,
            'matricule' => $request->matricule,
            'email' => $request->email . 'hello',
            'password' => bcrypt("12345678"),
            'etablissement' => $auth->etablissement,
        ]);
        $nm = $request->name;
        return back()->with("add_frm_success", "ajouter $nm avec success!!");
    }

    public function import(Request $request)
    {
        set_time_limit(300);
        $auth = auth()->user();
        $request->validate([
            'data' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('data');
        $filePath = $file->storeAs('uploads', $file->getClientOriginalName());
        $fullPath = storage_path("app/" . $filePath);

        $reader = ReaderEntityFactory::createReaderFromFile($fullPath);
        $reader->open($fullPath);

        $data = [];
        $data2 = []; // Added this line to prevent undefined variable error
        $firstRow = true;
        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($firstRow) {
                        $firstRow = false;
                        continue;
                    }

                    $cells = $row->getCells();

                    $emailPres = trim($cells[9]->getValue() ?? '');
                    $namePres = trim($cells[10]->getValue() ?? '');
                    $emailSyn = trim($cells[11]->getValue() ?? '');
                    $nameSyn = trim($cells[12]->getValue() ?? '');

                    if (empty($emailPres) || empty($namePres)) {
                        continue;
                    }

                    switch (true) {
                        // Case 1: The "Formateur Syn" is empty or is the same as "Formateur Présentiel"
                        case (empty($nameSyn) || $nameSyn === $namePres):
                            $formateur = User::firstOrCreate(
                                [
                                    'email' => $emailPres . "test",
                                    'etablissement' => $auth->etablissement,
                                ], // Find by email
                                [
                                    'matricule' => $emailPres,
                                    'name' => $namePres,
                                    'password' => bcrypt("12345678"),
                                ]
                            );
                            $fillier = Fillier::firstOrCreate(
                                [
                                    'code_fillier' => $cells[1]->getValue(),
                                    'name' => $cells[2]->getValue(),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $groupe = Groupe::firstOrCreate(
                                [
                                    'name' => $cells[4]->getValue(),
                                    'id_fillier' => $fillier->id_fillier,
                                    'niveau' => $cells[0]->getValue(),
                                    'effectif' => $cells[5]->getValue(),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $module = Module::firstOrCreate(
                                [
                                    'code_module' => $cells[6]->getValue(),
                                    'name' => $cells[7]->getValue(),
                                    'etablissement' => $auth->etablissement,

                                ], // Check both values
                                [
                                    'hours' => $cells[15]->getValue(),
                                    'mh_presentiel' => $cells[13]->getValue(),
                                    'mh_distanciel' => $cells[14]->getValue(),
                                    'regional' => $cells[8]->getValue(),
                                ]
                            );

                            $teaching = Teaching::firstOrCreate(
                                [
                                    'id_user' => $formateur->id,
                                    'id_group' => $groupe->id_group,
                                    'id_module' => $module->id_module,
                                    'id_fillier' => $fillier->id_fillier,
                                    'creneau' => $cells[3]->getValue(),
                                    'type_seance' => "totale",
                                ]
                            );
                            $data[] = $formateur;
                            $data2[] = $teaching;
                            break;

                        // Case 2: The "Formateur Syn" is different from the "Formateur Présentiel"
                        case (!empty($nameSyn) && $nameSyn !== $namePres):
                            $formateur = User::firstOrCreate(
                                [
                                    'email' => $emailPres . 'test',
                                    'etablissement' => $auth->etablissement,
                                ], // Find by email
                                [
                                    'matricule' => $emailPres,
                                    'name' => $namePres,
                                    'password' => bcrypt("12345678"),
                                ]
                            );

                            $fillier = Fillier::firstOrCreate(
                                [
                                    'code_fillier' => $cells[1]->getValue(),
                                    'name' => $cells[2]->getValue(),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $groupe = Groupe::firstOrCreate(
                                [
                                    'name' => $cells[4]->getValue(),
                                    'id_fillier' => $fillier->id_fillier,
                                    'niveau' => $cells[0]->getValue(),
                                    'effectif' => $cells[5]->getValue(),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $module = Module::firstOrCreate(
                                [
                                    'code_module' => $cells[6]->getValue(),
                                    'name' => $cells[7]->getValue(),
                                    'etablissement' => $auth->etablissement,

                                ], // Check both values
                                [
                                    'mh_presentiel' => $cells[13]->getValue(),
                                    'mh_distanciel' => $cells[14]->getValue(),
                                    'hours' => $cells[13]->getValue() + $cells[14]->getValue(),
                                    'regional' => $cells[8]->getValue(),
                                ]
                            );

                            $teaching = Teaching::firstOrCreate(
                                [
                                    'id_user' => $formateur->id,
                                    'id_group' => $groupe->id_group,
                                    'id_module' => $module->id_module,
                                    'id_fillier' => $fillier->id_fillier,
                                    'creneau' => $cells[3]->getValue(),
                                    'type_seance' => "presentiel",
                                ]
                            );
                            // Insert "Formateur Syn" only if their email and name exist
                            if (!empty($emailSyn) && !empty($nameSyn)) {
                                $formateur2 = User::firstOrCreate(
                                    [
                                        'email' => $emailSyn . 'test',
                                        'etablissement' => $auth->etablissement,
                                    ], // Find by email
                                    [
                                        'matricule' => $emailSyn,
                                        'name' => $nameSyn,
                                        'password' => bcrypt("12345678"),
                                        'etablissement' => $auth->etablissement,
                                    ]
                                );
                                $teaching2 = Teaching::firstOrCreate(
                                    [
                                        'id_user' => $formateur2->id,
                                        'id_group' => $groupe->id_group,
                                        'id_module' => $module->id_module,
                                        'id_fillier' => $fillier->id_fillier,
                                        'creneau' => $cells[3]->getValue(),
                                        'type_seance' => "distanciel",
                                    ]
                                );
                                $data[] = $formateur2;
                                $data2[] = $teaching2;
                            }

                            $data[] = $formateur;
                            break;
                    }
                }
            }
        } finally {
            $reader->close(); // Ensures the file is always closed
        }

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Les données ont été insérées avec succès!',
                'count' => count($data)
            ]);
        }

        // For regular form submission
        return back()->with('import_success', 'Les données ont été insérées avec succès!');
    }


    public function downloadFile($filename)
    {
        $filePath = storage_path("app/public/$filename"); // Correct path

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // XLSX MIME type
        ]);
    }

    public function dashboard()
    {
        return view('formateur.dashboard');
    }

    public function edit($id)
    {
        $formateur = User::find($id);
        return view('admin.edit_formateur', compact('formateur'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
        ]);

        $formateur = User::findOrFail($id);
        $formateur->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Formateur mis à jour avec succès');
    }

    public function destroy($id)
    {
        $formateur = User::findOrFail($id);

        // Supprimer le formateur
        $formateur->delete();

        // Rediriger avec un message de succès
        return back()->with('success', 'Formateur deleted avec succès');
    }


    public function progress($id)
    {
        // Find the teacher
        $teacher = User::findOrFail($id);

        // Get all teachings for this teacher with related models
        $teachings = Teaching::with(['module', 'group', 'fillier', 'progress', 'progress.customSessionDates'])
            ->where('id_user', $id)
            ->get();

        // Format data for the view
        $modules = [];

        foreach ($teachings as $teaching) {
            // Skip if no progress record exists
            if (!$teaching->progress) {
                continue;
            }

            $progress = $teaching->progress;
            $module = $teaching->module;
            $group = $teaching->group;

            // Calculate completion percentage
            $totalHours = $module->hours;
            $completedHours = $progress->hours_completed;
            $completionPercentage = $totalHours > 0 ? round(($completedHours / $totalHours) * 100, 1) : 0;

            // Calculate remaining weeks
            $today = Carbon::now();
            $examDate = $progress->final_exam_date ? Carbon::parse($progress->final_exam_date) : null;
            $remainingWeeks = $examDate ? max(0, $today->diffInWeeks($examDate)) : null;

            // Determine status
            $status = 'À jour';
            if ($examDate) {
                if ($remainingWeeks <= 0) {
                    $status = 'Terminé';
                } elseif ($completionPercentage < 50 && $remainingWeeks < 4) {
                    $status = 'En retard';
                } elseif ($completedHours === 0) {
                    $status = 'Non commencé';
                }
            }

            // Custom session dates if any
            $customDates = $progress->customSessionDates()->orderBy('week_index')->get();

            $modules[] = [
                'id_teaching' => $teaching->id_teaching,
                'module_name' => $module->name,
                'module_code' => $module->code_module,
                'group_name' => $group->name,
                'fillier_name' => $teaching->fillier->name ?? 'N/A',
                'type_seance' => $teaching->type_seance,
                'total_hours' => $totalHours,
                'completed_hours' => $completedHours,
                'remaining_hours' => $progress->remaining_hours,
                'completion_percentage' => $completionPercentage,
                'start_date' => $progress->module_start_date ? Carbon::parse($progress->module_start_date)->format('d/m/Y') : 'Non défini',
                'exam_date' => $examDate ? $examDate->format('d/m/Y') : 'Non défini',
                'weekly_hours' => $progress->weekly_hours,
                'remaining_weeks' => $remainingWeeks,
                'status' => $status,
                'custom_session_dates' => $customDates
            ];
        }

        return view('admin.progress', compact('teacher', 'modules'));
    }

    public function exportExcel($id)
    {
        // Fetch the teaching data associated with the user
        $teachings = Teaching::where('id_user', $id)->get();
        $user = User::find($id);

        // Fetch progress data
        $progressData = Progress::whereIn('id_teaching', $teachings->pluck('id_teaching'))->get();

        // Prepare group and module data
        $groupModuleData = [];
        foreach ($teachings as $teaching) {
            $group = Groupe::find($teaching->id_group);
            $module = Module::find($teaching->id_module);
            $progress = $progressData->firstWhere('id_teaching', $teaching->id_teaching);

            $hoursAffected = $progress ? json_decode($progress->hours_affected, true) ?? [] : [];
            $numWeeks = count($hoursAffected);
            $totalHours = $progress ? $progress->remaining_hours + $progress->hours_completed : 0;
            $groupModuleData[] = [
                'group_name' => $group ? $group->name : 'N/A',
                'module_name' => $module ? $module->name : 'N/A',
                'module_start_date' => $progress&&$progress->module_start_date!='1970-01-01' ? $progress->module_start_date : '',
                'final_exam_date' => $progress ? $progress->final_exam_date : 'N/A',
                'weeks' => $hoursAffected,
                'total_hours' => $totalHours,
            ];
        }

        // Now, generate the Excel file
        $filePath = storage_path('app/public/' . $user->name . '.xlsx');
        $writer = WriterEntityFactory::createWriter(Type::XLSX);
        $writer->openToFile($filePath);

        // Add first row for "Formateur Name"
        $writer->addRow(WriterEntityFactory::createRow([
            WriterEntityFactory::createCell('Formateur Name: ' . $user->name),
        ], (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setBackgroundColor('FFFF00') // Yellow background
            ->build()));

        // Create the header row dynamically based on the number of weeks
        $header = [
            WriterEntityFactory::createCell('Group Name'),
            WriterEntityFactory::createCell('Module Name'),
            WriterEntityFactory::createCell('Module Start Date'),
            WriterEntityFactory::createCell('Final Exam Date'),
        ];

        // Determine max number of weeks
        $maxWeeks = max(array_map(fn($data) => count($data['weeks']), $groupModuleData));

        for ($i = 1; $i <= $maxWeeks; $i++) {
            $header[] = WriterEntityFactory::createCell("Week $i");
        }

        // Add the header row
        $writer->addRow(WriterEntityFactory::createRow($header, (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setBackgroundColor('FFFF00') // Yellow background
            ->build()));

        // Add progress rows dynamically
        foreach ($groupModuleData as $data) {
            $row = [
                WriterEntityFactory::createCell($data['group_name']),
                WriterEntityFactory::createCell($data['module_name']),
                WriterEntityFactory::createCell($data['module_start_date']),
                WriterEntityFactory::createCell($data['final_exam_date']),
            ];

            // Add weekly progress dynamically
            for ($i = 0; $i < $maxWeeks; $i++) {
                if ($i < count($data['weeks'])) {
                    $status = $data['weeks'][$i] > 0 ? 'Terminé' : ($data['weeks'][$i] === 0 ? 'Absent' : 'En attente');
                } else {
                    $status = ($i * $data['weeks'][0] < $data['total_hours']) ? '-' : '---'; // Fill remaining weeks correctly
                }

                $row[] = WriterEntityFactory::createCell($status);
            }

            $writer->addRow(WriterEntityFactory::createRow($row));
        }

        // Close the writer (this saves the file)
        $writer->close();

        // Return the Excel file for download
        return response()->download($filePath);
    }
}
