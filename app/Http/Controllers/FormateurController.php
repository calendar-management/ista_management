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
            'password' => bcrypt("12345678"),
            'etablissement' => $auth->etablissement,
        ]);
        $nm = $request->name;
        return back()->with("add_frm_success", "ajouter $nm avec success!!");
    }

    // public function import(Request $request)
    // {
    //     set_time_limit(600); // Increased to 10 minutes for larger imports
    //     $auth = auth()->user();

    //     // Validate the request
    //     $request->validate([
    //         'data' => 'required|mimes:xlsx,xls,csv|max:5120', // Increased max size to 5MB
    //     ]);

    //     $file = $request->file('data');
    //     $filePath = $file->storeAs('uploads/temp', $file->getClientOriginalName());
    //     $fullPath = storage_path("app/" . $filePath);

    //     // Initialize counters and arrays for reporting
    //     $stats = [
    //         'users_created' => 0,
    //         'users_existing' => 0,
    //         'teachings_created' => 0,
    //         'rows_processed' => 0,
    //         'rows_skipped' => 0,
    //         'errors' => []
    //     ];

    //     try {
    //         $reader = ReaderEntityFactory::createReaderFromFile($fullPath);
    //         $reader->open($fullPath);

    //         DB::beginTransaction(); // Start transaction to ensure data integrity

    //         $firstRow = true;
    //         foreach ($reader->getSheetIterator() as $sheet) {
    //             foreach ($sheet->getRowIterator() as $rowIndex => $row) {
    //                 try {
    //                     // Skip header row
    //                     if ($firstRow) {
    //                         $firstRow = false;
    //                         continue;
    //                     }

    //                     $stats['rows_processed']++;
    //                     $cells = $row->getCells();

    //                     // Extract values with proper trim and null handling
    //                     $niveau = trim($cells[0]->getValue() ?? '');
    //                     $codeFillier = trim($cells[1]->getValue() ?? '');
    //                     $nameFillier = trim($cells[2]->getValue() ?? '');
    //                     $creneau = trim($cells[3]->getValue() ?? '');
    //                     $nameGroupe = trim($cells[4]->getValue() ?? '');
    //                     $effectif = intval($cells[5]->getValue() ?? 0);
    //                     $codeModule = trim($cells[6]->getValue() ?? '');
    //                     $nameModule = trim($cells[7]->getValue() ?? '');
    //                     $regional = trim($cells[8]->getValue() ?? '');
    //                     $emailPres = trim($cells[9]->getValue() ?? '');
    //                     $namePres = trim($cells[10]->getValue() ?? '');
    //                     $emailSyn = trim($cells[11]->getValue() ?? '');
    //                     $nameSyn = trim($cells[12]->getValue() ?? '');
    //                     $mhPresentiel = floatval($cells[13]->getValue() ?? 0);
    //                     $mhDistanciel = floatval($cells[14]->getValue() ?? 0);
    //                     $totalHours = floatval($cells[15]->getValue() ?? ($mhPresentiel + $mhDistanciel));

    //                     // Validate essential data
    //                     if (
    //                         empty($emailPres) || empty($namePres) || empty($codeFillier) ||
    //                         empty($nameGroupe) || empty($codeModule) || empty($nameModule)
    //                     ) {
    //                         $stats['rows_skipped']++;
    //                         continue;
    //                     }

    //                     // Find or create formateur using email+etablissement as the unique pair
    //                     $formateur = $this->findOrCreateUser($emailPres, $namePres, $auth->etablissement, $stats);

    //                     // Create or find fillier
    //                     $fillier = Fillier::firstOrCreate(
    //                         [
    //                             'code_fillier' => $codeFillier,
    //                             'etablissement' => $auth->etablissement,
    //                         ],
    //                         [
    //                             'name' => $nameFillier,
    //                         ]
    //                     );

    //                     // Create or find groupe
    //                     $groupe = Groupe::firstOrCreate(
    //                         [
    //                             'name' => $nameGroupe,
    //                             'id_fillier' => $fillier->id_fillier,
    //                             'niveau' => $niveau,
    //                             'etablissement' => $auth->etablissement,
    //                         ],
    //                         [
    //                             'effectif' => $effectif,
    //                         ]
    //                     );

    //                     // Create or find module
    //                     $module = Module::firstOrCreate(
    //                         [
    //                             'code_module' => $codeModule,
    //                             'etablissement' => $auth->etablissement,
    //                         ],
    //                         [
    //                             'name' => $nameModule,
    //                             'hours' => $totalHours,
    //                             'mh_presentiel' => $mhPresentiel,
    //                             'mh_distanciel' => $mhDistanciel,
    //                             'regional' => $regional,
    //                         ]
    //                     );

    //                     // Handle teaching assignments based on presence of synchronous teacher
    //                     if (empty($nameSyn) || $nameSyn === $namePres) {
    //                         // Case 1: No synchronous teacher or same as presentiel teacher
    //                         $teaching = Teaching::firstOrCreate(
    //                             [
    //                                 'id_user' => $formateur->id,
    //                                 'id_group' => $groupe->id_group,
    //                                 'id_module' => $module->id_module,
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'creneau' => $creneau,
    //                                 'type_seance' => "totale",
    //                                 'etablissement' => $auth->etablissement,
    //                             ]
    //                         );
    //                         $stats['teachings_created']++;
    //                     } else {
    //                         // Case 2: Different synchronous teacher
    //                         $teaching = Teaching::firstOrCreate(
    //                             [
    //                                 'id_user' => $formateur->id,
    //                                 'id_group' => $groupe->id_group,
    //                                 'id_module' => $module->id_module,
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'creneau' => $creneau,
    //                                 'type_seance' => "presentiel",
    //                                 'etablissement' => $auth->etablissement,
    //                             ]
    //                         );
    //                         $stats['teachings_created']++;

    //                         // Create synchronous teacher if email and name provided
    //                         if (!empty($emailSyn) && !empty($nameSyn)) {
    //                             $formateur2 = $this->findOrCreateUser($emailSyn, $nameSyn, $auth->etablissement, $stats);

    //                             $teaching2 = Teaching::firstOrCreate(
    //                                 [
    //                                     'id_user' => $formateur2->id,
    //                                     'id_group' => $groupe->id_group,
    //                                     'id_module' => $module->id_module,
    //                                     'id_fillier' => $fillier->id_fillier,
    //                                     'creneau' => $creneau,
    //                                     'type_seance' => "distanciel",
    //                                     'etablissement' => $auth->etablissement,
    //                                 ]
    //                             );
    //                             $stats['teachings_created']++;
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     $stats['errors'][] = "Error in row {$rowIndex}: " . $e->getMessage();
    //                 }
    //             }
    //         }

    //         DB::commit(); // Commit transaction if everything was successful
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Roll back on error
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Import failed: ' . $e->getMessage()
    //             ], 500);
    //         }
    //         return back()->with('error', 'Import failed: ' . $e->getMessage());
    //     } finally {
    //         if (isset($reader)) {
    //             $reader->close(); // Ensure file is closed
    //         }

    //         // Clean up the temp file
    //         // if (file_exists($fullPath)) {
    //         //     \Storage::delete($filePath);
    //         // }
    //     }

    //     // Generate response message
    //     $message = "Import successful. Processed {$stats['rows_processed']} rows: " .
    //         "Created {$stats['users_created']} new users, " .
    //         "Used {$stats['users_existing']} existing users, " .
    //         "Created {$stats['teachings_created']} teaching assignments.";

    //     if (!empty($stats['errors'])) {
    //         $message .= " Encountered " . count($stats['errors']) . " errors.";
    //     }

    //     // Return appropriate response based on request type
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => $message,
    //             'stats' => $stats
    //         ]);
    //     }

    //     // For regular form submission
    //     return back()->with('import_success', $message);
    // }

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

        // Arrays to batch insert
        $filliersToCreate = [];
        $groupesToCreate = [];
        $modulesToCreate = [];
        $teachingsToCreate = [];
        $usersToCreate = [];

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

                        // Collect unique emails, filliers, groups, modules for batch processing
                        $this->collectUniqueEmails($emailPres, $emailSyn, $userCache);
                        $this->collectUniqueFilliers($codeFillier, $nameFillier, $auth->etablissement, $fillierCache);
                        $this->collectUniqueModules($codeModule, $nameModule, $totalHours, $mhPresentiel, $mhDistanciel, $regional, $auth->etablissement, $moduleCache);
                    } catch (\Exception $e) {
                        $stats['errors'][] = "Error in row {$rowIndex}: " . $e->getMessage();
                    }
                }
            }

            // Batch process all unique emails to find existing users
            $existingUsers = $this->batchFindUsers(array_keys($userCache), $auth->etablissement);
            foreach ($existingUsers as $user) {
                $userCache[$user->email] = $user;
                $stats['users_existing']++;
            }

            // Create all missing users in a single batch
            $usersToCreate = [];
            foreach ($userCache as $email => $userData) {
                if (!is_object($userData)) {
                    $usersToCreate[] = [
                        'name' => $userData['name'],
                        'email' => $email,
                        'etablissement' => $auth->etablissement,
                        'password' => Hash::make(Str::random(12)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($usersToCreate)) {
                $stats['users_created'] = count($usersToCreate);
                User::insert($usersToCreate);

                // Update cache with newly created users
                $newUsers = User::whereIn('email', array_column($usersToCreate, 'email'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();
                foreach ($newUsers as $user) {
                    $userCache[$user->email] = $user;
                }
            }

            // Batch find existing filliers
            $existingFilliers = $this->batchFindFilliers(array_keys($fillierCache), $auth->etablissement);
            foreach ($existingFilliers as $fillier) {
                $fillierCache[$fillier->code_fillier] = $fillier;
            }

            // Create missing filliers
            $filliersToCreate = [];
            foreach ($fillierCache as $code => $data) {
                if (!is_object($data)) {
                    $filliersToCreate[] = [
                        'code_fillier' => $code,
                        'name' => $data['name'],
                        'etablissement' => $auth->etablissement,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($filliersToCreate)) {
                Fillier::insert($filliersToCreate);

                // Update cache with newly created filliers
                $newFilliers = Fillier::whereIn('code_fillier', array_column($filliersToCreate, 'code_fillier'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();
                foreach ($newFilliers as $fillier) {
                    $fillierCache[$fillier->code_fillier] = $fillier;
                }
            }

            // Similarly batch process modules
            $existingModules = $this->batchFindModules(array_keys($moduleCache), $auth->etablissement);
            foreach ($existingModules as $module) {
                $moduleCache[$module->code_module] = $module;
            }

            $modulesToCreate = [];
            foreach ($moduleCache as $code => $data) {
                if (!is_object($data)) {
                    $modulesToCreate[] = [
                        'code_module' => $code,
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
                Module::insert($modulesToCreate);

                // Update cache with newly created modules
                $newModules = Module::whereIn('code_module', array_column($modulesToCreate, 'code_module'))
                    ->where('etablissement', $auth->etablissement)
                    ->get();
                foreach ($newModules as $module) {
                    $moduleCache[$module->code_module] = $module;
                }
            }

            // Now process each row with our cached data
            foreach ($rowData as $rowIndex => $data) {
                try {
                    $fillierId = $fillierCache[$data['codeFillier']]->id_fillier;

                    // Create or find groupe using our optimized approach
                    $groupeKey = $data['nameGroupe'] . '_' . $fillierId . '_' . $data['niveau'];

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

                    $moduleId = $moduleCache[$data['codeModule']]->id_module;
                    $formateurId = $userCache[$data['emailPres']]->id;

                    // Handle teaching assignments based on presence of synchronous teacher
                    $teachingKey = $formateurId . '_' . $groupe->id_group . '_' . $moduleId . '_' . $fillierId . '_' . $data['creneau'];

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
                            $stats['teachings_created']++;
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
                            $stats['teachings_created']++;
                        }

                        // Create synchronous teacher if email and name provided
                        if (!empty($data['emailSyn']) && !empty($data['nameSyn'])) {
                            $formateur2Id = $userCache[$data['emailSyn']]->id;
                            $teachingKey2 = $formateur2Id . '_' . $groupe->id_group . '_' . $moduleId . '_' . $fillierId . '_' . $data['creneau'];

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
                                $stats['teachings_created']++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $stats['errors'][] = "Error processing row {$rowIndex}: " . $e->getMessage();
                }
            }

            DB::commit(); // Commit transaction if everything was successful
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back on error
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
            // if (file_exists($fullPath)) {
            //     \Storage::delete($filePath);
            // }
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

    /**
     * Helper function to collect unique emails for batch processing
     */
    private function collectUniqueEmails($emailPres, $emailSyn, &$userCache)
    {
        if (!empty($emailPres) && !isset($userCache[$emailPres])) {
            $userCache[$emailPres] = ['name' => $emailPres];
        }

        if (!empty($emailSyn) && !isset($userCache[$emailSyn])) {
            $userCache[$emailSyn] = ['name' => $emailSyn];
        }
    }

    /**
     * Helper function to collect unique filliers for batch processing
     */
    private function collectUniqueFilliers($codeFillier, $nameFillier, $etablissement, &$fillierCache)
    {
        if (!empty($codeFillier) && !isset($fillierCache[$codeFillier])) {
            $fillierCache[$codeFillier] = ['name' => $nameFillier];
        }
    }

    /**
     * Helper function to collect unique modules for batch processing
     */
    private function collectUniqueModules($codeModule, $nameModule, $totalHours, $mhPresentiel, $mhDistanciel, $regional, $etablissement, &$moduleCache)
    {
        if (!empty($codeModule) && !isset($moduleCache[$codeModule])) {
            $moduleCache[$codeModule] = [
                'name' => $nameModule,
                'hours' => $totalHours,
                'mh_presentiel' => $mhPresentiel,
                'mh_distanciel' => $mhDistanciel,
                'regional' => $regional
            ];
        }
    }

    /**
     * Batch find users by email
     */
    private function batchFindUsers($emails, $etablissement)
    {
        return User::whereIn('email', $emails)
            ->where('etablissement', $etablissement)
            ->get();
    }

    /**
     * Batch find filliers by code
     */
    private function batchFindFilliers($codes, $etablissement)
    {
        return Fillier::whereIn('code_fillier', $codes)
            ->where('etablissement', $etablissement)
            ->get();
    }

    /**
     * Batch find modules by code
     */
    private function batchFindModules($codes, $etablissement)
    {
        return Module::whereIn('code_module', $codes)
            ->where('etablissement', $etablissement)
            ->get();
    }

    /**
     * Find or create a user
     * Note: This is kept for compatibility but is no longer used directly in the optimized import
     */
    private function findOrCreateUser($email, $name, $etablissement, &$stats)
    {
        $user = User::firstOrCreate(
            [
                'email' => $email,
                'etablissement' => $etablissement,
            ],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(12)),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $stats['users_created']++;
        } else {
            $stats['users_existing']++;
        }

        return $user;
    }

    /**
     * Helper function to find or create a user
     */
    // private function findOrCreateUser($email, $name, $etablissement, &$stats)
    // {
    //     $existingUser = User::where('email', $email)
    //         ->where('etablissement', $etablissement)
    //         ->first();

    //     if ($existingUser) {
    //         $stats['users_existing']++;
    //         return $existingUser;
    //     }

    //     $user = User::create([
    //         'matricule' => $email, // Using email as matricule
    //         'name' => $name,
    //         'email' => $email,
    //         'password' => bcrypt("12345678"),
    //         'etablissement' => $etablissement,
    //         'role' => 'formateur', // Setting a default role for imported users
    //     ]);

    //     $stats['users_created']++;
    //     return $user;
    // }


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
