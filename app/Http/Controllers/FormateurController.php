<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use App\Models\Formateur;

class FormateurController extends Controller
{
    public function index()
    {
        $formateurs = Formateur::paginate(5);

        return view('admin.gestion_formateur', compact('formateurs'));
    }
    public function add(Request $request)
    {
        Formateur::create([
            'name' => $request->name,
            'groupe' => $request->groupe,
            'module' => $request->module,
            'type_seances' => $request->type_seances,
        ]);
        $nm = $request->name;
        return back()->with("add_frm_success", "ajouter $nm avec success!!");
    }
    public function import(Request $request)
    {

        $request->validate([
            'data' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);


        $file = $request->file('data');


        $filePath = $file->storeAs('uploads', $file->getClientOriginalName());


        $fullPath = storage_path("app/" . $filePath);


        $reader = ReaderEntityFactory::createReaderFromFile($fullPath);
        $reader->open($fullPath);

        $data = [];
        $firstRow = true;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $cells = $row->getCells();

                if (count($cells) >= 5) {
                    $formateur = Formateur::create([
                        'name' => $cells[1]->getValue(),
                        'groupe' => $cells[2]->getValue(),
                        'module' => $cells[3]->getValue(),
                        'type_seances' => $cells[4]->getValue(),
                    ]);

                    $data[] = $formateur;
                }
            }
        }


        $reader->close();


        return back()->with('import_success', 'Les donnees va inserter avec success!');

    }
}
