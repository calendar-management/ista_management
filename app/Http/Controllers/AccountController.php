<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function redirectUser(){
        if (auth()->user()){
            if (auth()->user()->role == 'administrateur'){
                return redirect()->route('adm_dashboard');
            }
            elseif (auth()->user()->role == 'formateur'){
                return redirect()->route('formateur.dashboard');
            }
            else return redirect()->route('sup_adm_dashboard');
        }
        else{
            return redirect()->route('login');
        }
    }
}
