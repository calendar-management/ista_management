<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;
use App\Models\User;

class FormateurController extends Controller
{
    public function index()
    {
        $formateurs = User::where('role', 'formateur')->paginate(5);
        return view('admin.gestion_formateur', compact('formateurs'));
    }

    public function add(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'formateur',
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

        $firstRow = true;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $cells = $row->getCells();

                if (count($cells) >= 3) {
                    User::create([
                        'name' => $cells[1]->getValue(),
                        'email' => $cells[2]->getValue(),
                        'password' => bcrypt('password123'), // default password
                        'role' => 'formateur',
                    ]);
                }
            }
        }

        $reader->close();

        return back()->with('import_success', 'Les données ont été insérées avec succès!');
    }

    public function edit($id)
    {
        $formateur = User::findOrFail($id);
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

        return redirect()->route('gestion_formateur')->with('success', 'Formateur mis à jour avec succès');
    }

    public function destroy($id)
{
    // Trouver le formateur avec l'ID
    $formateur = User::findOrFail($id);

    // Supprimer le formateur
    $formateur->delete();

    // Rediriger avec un message de succès
    return redirect()->route('gestion_formateur')->with('success', 'Formateur supprimé avec succès');
}

}
