<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function login(Request $request){
        return $request->test;
    }
}
