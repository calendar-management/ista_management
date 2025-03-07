<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrateur; 

class AdministrateurController extends Controller
{
    public function index()
    {
        $administrateurs = Administrateur::paginate(5); 
        return view('supadmin.gestion_admin', compact('administrateurs')); 
    }
    public function add(Request $request){
        Administrateur::create([
            'name'=> $request->name,
            'etablissement'=> $request->etablissement
        ]);
        
        return redirect('/gestion_adm');
    }
    public function edit($id){
        $administrateur = Administrateur::find($id);
        return view('supadmin.edit_adm', compact('administrateur'));
    }
    public function update(Request $request, $id){
        $fields = $request->validate([
            'name'=> ['required'],
            'etablissement'=> ['required']
        ]);
        $administrateur=Administrateur::findOrFail($id);
        $administrateur->update($fields);

        return back()->with('update_success','mis a jour avec success');
    }
    public function delete($id){
        $administrateur = Administrateur::find($id);
        $nom= $administrateur->name;
        $administrateur->delete();
        return back()->with('delete_success',"supprimer $nom avec success");
    }

}
