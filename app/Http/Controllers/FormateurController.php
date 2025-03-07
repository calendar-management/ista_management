<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formateur;

class FormateurController extends Controller
{
    public function index()
    {
        $formateurs = Formateur::all(); 
        
        return view('admin.gestion_formateur', compact('formateurs')); 
    }
}
