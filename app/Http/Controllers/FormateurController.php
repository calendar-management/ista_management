<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



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
        $request->validate([
            'email' => ['required', 'unique:users,email']
        ]);
        $auth = auth()->user();
        User::create([
            'name' => $request->name,
            'matricule' => $request->matricule,
            'email' => $request->email,
            'password' => '$2y$10$PD1ETbkuMGMIiLg6e8fJZ.XfiEkZJwFAMLcdWxLxvGlVd1g5YlQ0m',
            'etablissement' => $auth->etablissement,
        ]);
        $nm = $request->name;
        return back()->with("add_frm_success", "ajouter $nm avec success!!");
    }



    public function import(Request $request)
    {
        set_time_limit(600); // Increased to 10 minutes for larger imports
        $auth = auth()->user();

        // Validate the request
        $request->validate([
            'data' => 'required|mimes:xlsx,xls,csv|max:5120', // Increased max size to 5MB
        ]);

        $file = $request->file('data');
        $filePath = $file->storeAs('uploads/temp', $file->getClientOriginalName());
        $fullPath = storage_path("app/" . $filePath);

        // Initialize counters and arrays for reporting
        $stats = [
            'users_created' => 0,
            'users_existing' => 0,
            'teachings_created' => 0,
            'rows_processed' => 0,
            'rows_skipped' => 0,
            'errors' => []
        ];

        // Initialize cache arrays to reduce database queries
        $userCache = [];
        $fillierCache = [];
        $groupeCache = [];
        $moduleCache = [];
        $teachingCache = [];

        try {
            $reader = ReaderEntityFactory::createReaderFromFile($fullPath);
            $reader->open($fullPath);

            DB::beginTransaction(); // Start transaction to ensure data integrity

            // First pass: collect all data
            $rowData = [];
            $firstRow = true;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    try {
                        // Skip header row
                        if ($firstRow) {
                            $firstRow = false;
                            continue;
                        }

                        $stats['rows_processed']++;
                        $cells = $row->getCells();

                        // Check if we have enough cells
                        if (count($cells) < 16) {
                            $stats['rows_skipped']++;
                            $stats['errors'][] = "Row {$rowIndex} has insufficient data";
                            continue;
                        }

                        // Extract values with proper trim and null handling
                        $niveau = trim($cells[0]->getValue() ?? '');
                        $codeFillier = trim($cells[1]->getValue() ?? '');
                        $nameFillier = trim($cells[2]->getValue() ?? '');
                        $creneau = trim($cells[3]->getValue() ?? '');
                        $nameGroupe = trim($cells[4]->getValue() ?? '');
                        $effectif = intval($cells[5]->getValue() ?? 0);
                        $codeModule = trim($cells[6]->getValue() ?? '');
                        $nameModule = trim($cells[7]->getValue() ?? '');
                        $regional = trim($cells[8]->getValue() ?? '');
                        $emailPres = trim($cells[9]->getValue() ?? '');
                        $namePres = trim($cells[10]->getValue() ?? '');
                        $emailSyn = trim($cells[11]->getValue() ?? '');
                        $nameSyn = trim($cells[12]->getValue() ?? '');
                        $mhPresentiel = floatval($cells[13]->getValue() ?? 0);
                        $mhDistanciel = floatval($cells[14]->getValue() ?? 0);
                        $totalHours = floatval($cells[15]->getValue() ?? ($mhPresentiel + $mhDistanciel));

                        // Validate essential data
                        if (
                            empty($emailPres) || empty($namePres) || empty($codeFillier) ||
                            empty($nameGroupe) || empty($codeModule) || empty($nameModule)
                        ) {
                            $stats['rows_skipped']++;
                            $stats['errors'][] = "Row {$rowIndex} is missing essential data";
                            continue;
                        }

                        // Store all valid row data for processing
                        $rowData[] = [
                            'niveau' => $niveau,
                            'codeFillier' => $codeFillier,
                            'nameFillier' => $nameFillier,
                            'creneau' => $creneau,
                            'nameGroupe' => $nameGroupe,
                            'effectif' => $effectif,
                            'codeModule' => $codeModule,
                            'nameModule' => $nameModule,
                            'regional' => $regional,
                            'emailPres' => $emailPres,
                            'namePres' => $namePres,
                            'emailSyn' => $emailSyn,
                            'nameSyn' => $nameSyn,
                            'mhPresentiel' => $mhPresentiel,
                            'mhDistanciel' => $mhDistanciel,
                            'totalHours' => $totalHours,
                        ];

                        // Collect unique emails for users
                        if (!empty($emailPres)) {
                            if (!isset($userCache[$emailPres])) {
                                $userCache[$emailPres] = [
                                    'email' => $emailPres,
                                    'name' => $namePres
                                ];
                            }
                        }

                        if (!empty($emailSyn) && $emailSyn != $emailPres) {
                            if (!isset($userCache[$emailSyn])) {
                                $userCache[$emailSyn] = [
                                    'email' => $emailSyn,
                                    'name' => $nameSyn
                                ];
                            }
                        }

                        // Collect unique filliers for THIS etablissement
                        $fillierKey = $codeFillier . '_' . $auth->etablissement;
                        if (!empty($codeFillier)) {
                            if (!isset($fillierCache[$fillierKey])) {
                                $fillierCache[$fillierKey] = [
                                    'code' => $codeFillier,
                                    'name' => $nameFillier
                                ];
                            }
                        }

                        // Collect unique modules for THIS etablissement
                        $moduleKey = $codeModule . '_' . $auth->etablissement;
                        if (!empty($codeModule)) {
                            if (!isset($moduleCache[$moduleKey])) {
                                $moduleCache[$moduleKey] = [
                                    'code' => $codeModule,
                                    'name' => $nameModule,
                                    'hours' => $totalHours,
                                    'mh_presentiel' => $mhPresentiel,
                                    'mh_distanciel' => $mhDistanciel,
                                    'regional' => $regional
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        $stats['errors'][] = "Error in row {$rowIndex}: " . $e->getMessage();
                    }
                }
            }

            // If we have no valid rows, abort early
            if (empty($rowData)) {
                throw new \Exception("No valid data rows found in the file.");
            }

            Log::info("Processing import with " . count($rowData) . " valid rows");

            // Batch find existing users within THIS establishment only
            $emails = array_keys($userCache);

            $existingUsers = User::whereIn('email', $emails)
                ->where('etablissement', $auth->etablissement)
                ->get();

            // Update the cache with existing users
            foreach ($existingUsers as $user) {
                $userCache[$user->email] = $user;
                $stats['users_existing']++;
            }

            // Create all missing users in a single batch for THIS etablissement
            $usersToCreate = [];
            foreach ($userCache as $email => $userData) {
                if (!is_object($userData)) {
                    $usersToCreate[] = [
                        'name' => $userData['name'],
                        'email' => $email,
                        'matricule' => $email, // Using email as matricule (IMPORTANT: same value)
                        'etablissement' => $auth->etablissement,
                        'password' => bcrypt('password'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($usersToCreate)) {
                Log::info("Creating " . count($usersToCreate) . " new users");
                User::insert($usersToCreate);
                $stats['users_created'] = count($usersToCreate);

                // Update cache with newly created users
                $newUsers = User::whereIn('email', array_column($usersToCreate, 'email'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();

                foreach ($newUsers as $user) {
                    $userCache[$user->email] = $user;
                }
            }

            // Extract fillier codes for THIS etablissement
            $fillierCodes = array_map(function ($data) {
                return $data['code'] ?? null;
            }, $fillierCache);
            $fillierCodes = array_filter($fillierCodes);

            // Batch find existing filliers IN THIS ETABLISSEMENT
            $existingFilliers = Fillier::whereIn('code_fillier', $fillierCodes)
                ->where('etablissement', $auth->etablissement)
                ->get();

            // Map filliers back to keys that include etablissement
            foreach ($existingFilliers as $fillier) {
                $fillierKey = $fillier->code_fillier . '_' . $auth->etablissement;
                $fillierCache[$fillierKey] = $fillier;
            }

            // Create missing filliers for THIS etablissement
            $filliersToCreate = [];
            foreach ($fillierCache as $key => $data) {
                if (!is_object($data)) {
                    $filliersToCreate[] = [
                        'code_fillier' => $data['code'],
                        'name' => $data['name'],
                        'etablissement' => $auth->etablissement,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($filliersToCreate)) {
                Log::info("Creating " . count($filliersToCreate) . " new filliers");
                Fillier::insert($filliersToCreate);

                // Update cache with newly created filliers
                $newFilliers = Fillier::whereIn('code_fillier', array_column($filliersToCreate, 'code_fillier'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();

                foreach ($newFilliers as $fillier) {
                    $fillierKey = $fillier->code_fillier . '_' . $auth->etablissement;
                    $fillierCache[$fillierKey] = $fillier;
                }
            }

            // Extract module codes for THIS etablissement
            $moduleCodes = array_map(function ($data) {
                return $data['code'] ?? null;
            }, $moduleCache);
            $moduleCodes = array_filter($moduleCodes);

            // Similarly batch process modules IN THIS ETABLISSEMENT
            $existingModules = Module::whereIn('code_module', $moduleCodes)
                ->where('etablissement', $auth->etablissement)
                ->get();

            // Map modules back to keys that include etablissement
            foreach ($existingModules as $module) {
                $moduleKey = $module->code_module . '_' . $auth->etablissement;
                $moduleCache[$moduleKey] = $module;
            }

            $modulesToCreate = [];
            foreach ($moduleCache as $key => $data) {
                if (!is_object($data)) {
                    $modulesToCreate[] = [
                        'code_module' => $data['code'],
                        'name' => $data['name'],
                        'hours' => $data['hours'],
                        'mh_presentiel' => $data['mh_presentiel'],
                        'mh_distanciel' => $data['mh_distanciel'],
                        'regional' => $data['regional'],
                        'etablissement' => $auth->etablissement,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($modulesToCreate)) {
                Log::info("Creating " . count($modulesToCreate) . " new modules");
                Module::insert($modulesToCreate);

                // Update cache with newly created modules
                $newModules = Module::whereIn('code_module', array_column($modulesToCreate, 'code_module'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();

                foreach ($newModules as $module) {
                    $moduleKey = $module->code_module . '_' . $auth->etablissement;
                    $moduleCache[$moduleKey] = $module;
                }
            }

            // Now process each row with our cached data
            $teachingsCreated = 0;
            foreach ($rowData as $rowIndex => $data) {
                try {
                    // Get fillier from cache with proper key including etablissement
                    $fillierKey = $data['codeFillier'] . '_' . $auth->etablissement;

                    if (!isset($fillierCache[$fillierKey]) || !is_object($fillierCache[$fillierKey])) {
                        throw new \Exception("Fillier object not found for code: " . $data['codeFillier'] . " in etablissement: " . $auth->etablissement);
                    }

                    $fillier = $fillierCache[$fillierKey];
                    $fillierId = $fillier->id_fillier;

                    // Create or find groupe using our optimized approach
                    $groupeKey = $data['nameGroupe'] . '_' . $fillierId . '_' . $data['niveau'] . '_' . $auth->etablissement;

                    if (!isset($groupeCache[$groupeKey])) {
                        $groupe = Groupe::firstOrCreate(
                            [
                                'name' => $data['nameGroupe'],
                                'id_fillier' => $fillierId,
                                'niveau' => $data['niveau'],
                                'etablissement' => $auth->etablissement,
                            ],
                            [
                                'effectif' => $data['effectif'],
                            ]
                        );
                        $groupeCache[$groupeKey] = $groupe;
                    } else {
                        $groupe = $groupeCache[$groupeKey];
                    }

                    // Get module from cache with proper key including etablissement
                    $moduleKey = $data['codeModule'] . '_' . $auth->etablissement;

                    if (!isset($moduleCache[$moduleKey]) || !is_object($moduleCache[$moduleKey])) {
                        throw new \Exception("Module object not found for code: " . $data['codeModule'] . " in etablissement: " . $auth->etablissement);
                    }

                    $module = $moduleCache[$moduleKey];
                    $moduleId = $module->id_module;

                    // Check for user in this etablissement
                    if (!isset($userCache[$data['emailPres']]) || !is_object($userCache[$data['emailPres']])) {
                        throw new \Exception("User object not found for email: " . $data['emailPres'] . " in etablissement: " . $auth->etablissement);
                    }

                    $formateurId = $userCache[$data['emailPres']]->id;

                    // Handle teaching assignments based on presence of synchronous teacher
                    $teachingKey = $formateurId . '_' . $groupe->id_group . '_' . $moduleId . '_' . $fillierId . '_' . $data['creneau'] . '_' . $auth->etablissement;

                    if (empty($data['nameSyn']) || $data['nameSyn'] === $data['namePres']) {
                        // Case 1: No synchronous teacher or same as presentiel teacher
                        if (!isset($teachingCache[$teachingKey . '_totale'])) {
                            $teaching = Teaching::firstOrCreate(
                                [
                                    'id_user' => $formateurId,
                                    'id_group' => $groupe->id_group,
                                    'id_module' => $moduleId,
                                    'id_fillier' => $fillierId,
                                    'creneau' => $data['creneau'],
                                    'type_seance' => "totale",
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $teachingCache[$teachingKey . '_totale'] = $teaching;
                            $teachingsCreated++;
                        } else {
                            $teaching = $teachingCache[$teachingKey . '_totale'];
                        }
                    } else {
                        // Case 2: Different synchronous teacher
                        if (!isset($teachingCache[$teachingKey . '_presentiel'])) {
                            $teaching = Teaching::firstOrCreate(
                                [
                                    'id_user' => $formateurId,
                                    'id_group' => $groupe->id_group,
                                    'id_module' => $moduleId,
                                    'id_fillier' => $fillierId,
                                    'creneau' => $data['creneau'],
                                    'type_seance' => "presentiel",
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $teachingCache[$teachingKey . '_presentiel'] = $teaching;
                            $teachingsCreated++;
                        } else {
                            $teaching = $teachingCache[$teachingKey . '_presentiel'];
                        }

                        // Create synchronous teacher if email and name provided
                        if (!empty($data['emailSyn']) && !empty($data['nameSyn'])) {
                            // Check for synchronous teacher in this etablissement
                            if (!isset($userCache[$data['emailSyn']]) || !is_object($userCache[$data['emailSyn']])) {
                                throw new \Exception("User (synchronous) object not found for email: " . $data['emailSyn'] . " in etablissement: " . $auth->etablissement);
                            }

                            $formateur2Id = $userCache[$data['emailSyn']]->id;
                            $teachingKey2 = $formateur2Id . '_' . $groupe->id_group . '_' . $moduleId . '_' . $fillierId . '_' . $data['creneau'] . '_' . $auth->etablissement;

                            if (!isset($teachingCache[$teachingKey2 . '_distanciel'])) {
                                $teaching2 = Teaching::firstOrCreate(
                                    [
                                        'id_user' => $formateur2Id,
                                        'id_group' => $groupe->id_group,
                                        'id_module' => $moduleId,
                                        'id_fillier' => $fillierId,
                                        'creneau' => $data['creneau'],
                                        'type_seance' => "distanciel",
                                        'etablissement' => $auth->etablissement,
                                    ]
                                );
                                $teachingCache[$teachingKey2 . '_distanciel'] = $teaching2;
                                $teachingsCreated++;
                            }
                        }
                    }

                    // Optionally, create Progress record if needed
                    // This part can be uncommented if you want to create progress records during import
                    /*
                if (!Progress::where('id_teaching', $teaching->id_teaching)->exists()) {
                    Progress::create([
                        'id_teaching' => $teaching->id_teaching,
                        'hours_completed' => 0,
                        'remaining_hours' => $module->hours,
                        'hours_affected' => $module->hours,
                        'weekly_hours' => 0, // Set a default value
                        'etablissement' => $auth->etablissement
                    ]);
                }
                */
                } catch (\Exception $e) {
                    $stats['errors'][] = "Error processing row {$rowIndex}: " . $e->getMessage();
                    Log::error("Import error at row {$rowIndex}: " . $e->getMessage());
                }
            }

            $stats['teachings_created'] = $teachingsCreated;

            // Log final stats before commit
            Log::info("Import completed. Stats: " . json_encode($stats));

            DB::commit(); // Commit transaction if everything was successful
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back on error
            Log::error("Import failed with error: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        } finally {
            if (isset($reader)) {
                $reader->close(); // Ensure file is closed
            }

            // Clean up the temp file
            if (file_exists($fullPath)) {
                Storage::delete($filePath);
            }
        }

        // Generate response message
        $message = "Import successful. Processed {$stats['rows_processed']} rows: " .
            "Created {$stats['users_created']} new users, " .
            "Used {$stats['users_existing']} existing users, " .
            "Created {$stats['teachings_created']} teaching assignments.";

        if (!empty($stats['errors'])) {
            $message .= " Encountered " . count($stats['errors']) . " errors.";
        }

        // Return appropriate response based on request type
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => $stats
            ]);
        }

        // For regular form submission
        return back()->with('import_success', $message);
    }




    public function downloadFile($filename)
    {
        $filePath = storage_path("app/public/$filename");

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
            'email' => 'required|string|max:255|unique:users,email',
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

        $formateur->delete();

        return back()->with('success', 'Formateur deleted avec succès');
    }


    public function progress($id)
    {
        $teacher = User::findOrFail($id);

        $teachings = Teaching::with(['module', 'group', 'fillier', 'progress', 'progress.customSessionDates'])
            ->where('id_user', $id)
            ->get();

        $modules = [];

        foreach ($teachings as $teaching) {
            if (!$teaching->progress) {
                continue;
            }

            $progress = $teaching->progress;
            $module = $teaching->module;
            $group = $teaching->group;

            $totalHours = $module->hours;
            $completedHours = $progress->hours_completed;
            $completionPercentage = $totalHours > 0 ? round(($completedHours / $totalHours) * 100, 1) : 0;

            $today = Carbon::now();
            $examDate = $progress->final_exam_date ? Carbon::parse($progress->final_exam_date) : null;
            $remainingWeeks = $examDate ? max(0, $today->diffInWeeks($examDate)) : null;

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
                'start_date' => $progress->module_start_date === '1970-01-01' ? 'Non défini' : $progress->module_start_date,
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
        $teachings = Teaching::where('id_user', $id)->get();
        $user = User::find($id);

        $progressData = Progress::whereIn('id_teaching', $teachings->pluck('id_teaching'))->get();

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
                'module_start_date' => $progress && $progress->module_start_date != '1970-01-01' ? $progress->module_start_date : '',
                'final_exam_date' => $progress ? $progress->final_exam_date : 'N/A',
                'weeks' => $hoursAffected,
                'total_hours' => $totalHours,
            ];
        }

        $filePath = storage_path('app/public/' . $user->name . '.xlsx');
        $writer = WriterEntityFactory::createWriter(Type::XLSX);
        $writer->openToFile($filePath);

        $writer->addRow(WriterEntityFactory::createRow([
            WriterEntityFactory::createCell('Formateur Name: ' . $user->name),
        ], (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setBackgroundColor('FFFF00') // Yellow background
            ->build()));

        $header = [
            WriterEntityFactory::createCell('Group Name'),
            WriterEntityFactory::createCell('Module Name'),
            WriterEntityFactory::createCell('Module Start Date'),
            WriterEntityFactory::createCell('Final Exam Date'),
        ];

        $maxWeeks = max(array_map(fn($data) => count($data['weeks']), $groupModuleData));

        for ($i = 1; $i <= $maxWeeks; $i++) {
            $header[] = WriterEntityFactory::createCell("Week $i");
        }

        $writer->addRow(WriterEntityFactory::createRow($header, (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->setBackgroundColor('FFFF00') // Yellow background
            ->build()));

        foreach ($groupModuleData as $data) {
            $row = [
                WriterEntityFactory::createCell($data['group_name']),
                WriterEntityFactory::createCell($data['module_name']),
                WriterEntityFactory::createCell($data['module_start_date']),
                WriterEntityFactory::createCell($data['final_exam_date']),
            ];

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

        $writer->close();

        return response()->download($filePath);
    }
}
