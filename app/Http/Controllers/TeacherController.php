<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function showProgress($id)
    {
        $teacher = User::with([
            'teaching.module',
            'teaching.group',
            'teaching.progress'
        ])->findOrFail($id);

        return view('admin.progress', compact('teacher'));
    }
}
