<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;


class TestController extends Controller
{

    public function index()
{
    $data = Test::all();
    
    return view('calendar', compact('data'));
    
}

    public function store(Request $request)
    {
        Test::create($request->all());
        dd('ok');
    }
}
