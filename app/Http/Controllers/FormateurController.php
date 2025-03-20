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
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->paginate(10);

        return view('admin.gestion_formateur', compact('formateurs'));
    }
    public function add(Request $request)
    {
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
    //     set_time_limit(300);
    //     $auth = auth()->user();
    //     $request->validate([
    //         'data' => 'required|mimes:xlsx,xls,csv|max:2048',
    //     ]);


    //     $file = $request->file('data');


    //     $filePath = $file->storeAs('uploads', $file->getClientOriginalName());


    //     $fullPath = storage_path("app/" . $filePath);


    //     $reader = ReaderEntityFactory::createReaderFromFile($fullPath);
    //     $reader->open($fullPath);

    //     $data = [];
    //     $firstRow = true;
    //     try {

    //         foreach ($reader->getSheetIterator() as $sheet) {
    //             foreach ($sheet->getRowIterator() as $row) {
    //                 if ($firstRow) {
    //                     $firstRow = false;
    //                     continue;
    //                 }

    //                 $cells = $row->getCells();


    //                 $emailPres = trim($cells[9]->getValue() ?? '');
    //                 $namePres = trim($cells[10]->getValue() ?? '');
    //                 $emailSyn = trim($cells[11]->getValue() ?? '');
    //                 $nameSyn = trim($cells[12]->getValue() ?? '');


    //                 if (empty($emailPres) || empty($namePres)) {
    //                     continue;
    //                 }

    //                 switch (true) {
    //                     // Case 1: The "Formateur Syn" is empty or is the same as "Formateur Présentiel"
    //                     case (empty($nameSyn) || $nameSyn === $namePres):
    //                         $formateur = User::firstOrCreate(
    //                             ['email' => $emailPres], // Find by email
    //                             [
    //                                 'matricule' => $emailPres,
    //                                 'name' => $namePres,
    //                                 'password' => bcrypt("12345678"),
    //                                 'etablissement' => $auth->etablissement,
    //                             ]
    //                         );
    //                         $fillier = Fillier::firstOrCreate(
    //                             [
    //                                 'code_fillier' => $cells[1]->getValue(),
    //                                 'name' => $cells[2]->getValue(),
    //                             ]
    //                         );
    //                         $groupe = Groupe::firstOrCreate(
    //                             [
    //                                 'name' => $cells[4]->getValue(),
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'niveau' => $cells[0]->getValue(),
    //                                 'effectif' => $cells[5]->getValue(),
    //                             ]
    //                         );
    //                         $module = Module::firstOrCreate(
    //                             ['code_module' => $cells[6]->getValue(), 'name' => $cells[7]->getValue()], // Check both values
    //                             [
    //                                 'hours' => $cells[15]->getValue(),
    //                                 'mh_presentiel' => $cells[13]->getValue(),
    //                                 'mh_distanciel' => $cells[14]->getValue(),
    //                                 'regional' => $cells[8]->getValue(),
    //                             ]
    //                         );

    //                         $teaching = Teaching::firstOrCreate(
    //                             [
    //                                 'id_user' => $formateur->id,
    //                                 'id_group' => $groupe->id_group,
    //                                 'id_module' => $module->id_module,
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'creneau' => $cells[3]->getValue(),
    //                                 'type_seance' => "totale",
    //                             ]
    //                         );
    //                         $data[] = $formateur;
    //                         $data2[] = $teaching;
    //                         break;

    //                     // Case 2: The "Formateur Syn" is different from the "Formateur Présentiel"
    //                     case (!empty($nameSyn) && $nameSyn !== $namePres):
    //                         $formateur = User::firstOrCreate(
    //                             ['email' => $emailPres],
    //                             [
    //                                 'matricule' => $emailPres,
    //                                 'name' => $namePres,
    //                                 'password' => bcrypt("12345678"),
    //                                 'etablissement' => $auth->etablissement,
    //                             ]
    //                         );

    //                         $fillier = Fillier::firstOrCreate(
    //                             [
    //                                 'code_fillier' => $cells[1]->getValue(),
    //                                 'name' => $cells[2]->getValue(),
    //                             ]
    //                         );
    //                         $groupe = Groupe::firstOrCreate(
    //                             [
    //                                 'name' => $cells[4]->getValue(),
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'niveau' => $cells[0]->getValue(),
    //                                 'effectif' => $cells[5]->getValue(),
    //                             ]
    //                         );
    //                         $module = Module::firstOrCreate(
    //                             ['code_module' => $cells[6]->getValue(), 'name' => $cells[7]->getValue()], // Check both values
    //                             [
    //                                 'hours' => $cells[15]->getValue(),
    //                                 'mh_presentiel' => $cells[13]->getValue(),
    //                                 'mh_distanciel' => $cells[14]->getValue(),
    //                                 'regional' => $cells[8]->getValue(),
    //                             ]
    //                         );

    //                         $teaching = Teaching::firstOrCreate(
    //                             [
    //                                 'id_user' => $formateur->id,
    //                                 'id_group' => $groupe->id_group,
    //                                 'id_module' => $module->id_module,
    //                                 'id_fillier' => $fillier->id_fillier,
    //                                 'creneau' => $cells[3]->getValue(),
    //                                 'type_seance' => "presentiel",
    //                             ]
    //                         );
    //                         // Insert "Formateur Syn" only if their email and name exist
    //                         if (!empty($emailSyn) && !empty($nameSyn)) {
    //                             $formateur2 = User::firstOrCreate(
    //                                 ['email' => $emailSyn],
    //                                 [
    //                                     'matricule' => $emailSyn,
    //                                     'name' => $nameSyn,
    //                                     'password' => bcrypt("12345678"),
    //                                     'etablissement' => $auth->etablissement,
    //                                 ]
    //                             );
    //                             $teaching2 = Teaching::firstOrCreate(
    //                                 [
    //                                     'id_user' => $formateur2->id,
    //                                     'id_group' => $groupe->id_group,
    //                                     'id_module' => $module->id_module,
    //                                     'id_fillier' => $fillier->id_fillier,
    //                                     'creneau' => $cells[3]->getValue(),
    //                                     'type_seance' => "distanciel",
    //                                 ]
    //                             );
    //                             $data[] = $formateur2;
    //                             $data2[] = $teaching2;
    //                         }


    //                         $data[] = $formateur;
    //                         break;
    //                 }
    //             }
    //         }



    //     } finally {
    //         $reader->close(); // Ensures the file is always closed
    //     }



    //     return back()->with('import_success', 'Les donnees va inserter avec success!');

    // }

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
                                ['email' => $emailPres], // Find by email
                                [
                                    'matricule' => $emailPres,
                                    'name' => $namePres,
                                    'password' => bcrypt("12345678"),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );
                            $fillier = Fillier::firstOrCreate(
                                [
                                    'code_fillier' => $cells[1]->getValue(),
                                    'name' => $cells[2]->getValue(),
                                ]
                            );
                            $groupe = Groupe::firstOrCreate(
                                [
                                    'name' => $cells[4]->getValue(),
                                    'id_fillier' => $fillier->id_fillier,
                                    'niveau' => $cells[0]->getValue(),
                                    'effectif' => $cells[5]->getValue(),
                                ]
                            );
                            $module = Module::firstOrCreate(
                                ['code_module' => $cells[6]->getValue(), 'name' => $cells[7]->getValue()], // Check both values
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
                                ['email' => $emailPres],
                                [
                                    'matricule' => $emailPres,
                                    'name' => $namePres,
                                    'password' => bcrypt("12345678"),
                                    'etablissement' => $auth->etablissement,
                                ]
                            );

                            $fillier = Fillier::firstOrCreate(
                                [
                                    'code_fillier' => $cells[1]->getValue(),
                                    'name' => $cells[2]->getValue(),
                                ]
                            );
                            $groupe = Groupe::firstOrCreate(
                                [
                                    'name' => $cells[4]->getValue(),
                                    'id_fillier' => $fillier->id_fillier,
                                    'niveau' => $cells[0]->getValue(),
                                    'effectif' => $cells[5]->getValue(),
                                ]
                            );
                            $module = Module::firstOrCreate(
                                ['code_module' => $cells[6]->getValue(), 'name' => $cells[7]->getValue()], // Check both values
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
                                    'type_seance' => "presentiel",
                                ]
                            );
                            // Insert "Formateur Syn" only if their email and name exist
                            if (!empty($emailSyn) && !empty($nameSyn)) {
                                $formateur2 = User::firstOrCreate(
                                    ['email' => $emailSyn],
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
}
