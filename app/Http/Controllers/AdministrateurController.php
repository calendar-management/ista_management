<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class AdministrateurController extends Controller
{
    public function index()
    {
        $administrateurs = User::where('role', 'administrateur')->paginate(5); 
        return view('supadmin.gestion_admin', compact('administrateurs')); 
    }
    public function add(Request $request){
        $request->validate([
            'name'=> ['required'],
            'etablissement'=> ['required'],
            'email'=> ['required','unique:users,email'],
        ]);
        user::firstOrCreate([
            'name'=> $request->name,
            'etablissement'=> $request->etablissement,
            'email'=> $request->email,
            'role'=> 'administrateur',
            'password'=> bcrypt("12345678")
        ]);
        
        return redirect('/gestion_adm');
    }
    public function edit($id){
        $administrateur = User::find($id);
        return view('supadmin.edit_adm', compact('administrateur'));
    }
    public function update(Request $request, $id){
        $fields = $request->validate([
            'name'=> ['required'],
            'etablissement'=> ['required'],
            'email'=> ['required'],
        ]);
        $administrateur=User::findOrFail($id);
        $administrateur->update($fields);

        return back()->with('update_success','mis a jour avec success');
    }
    public function delete($id){
        $administrateur = User::find($id);
        $nom= $administrateur->name;
        $administrateur->delete();
        return back()->with('delete_success',"supprimer $nom avec success");
    }

}