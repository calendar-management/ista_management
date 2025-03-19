<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function showProgress(int $id): View
    {
        $teacher = User::with([
            'teaching.module',
            'teaching.group',
            'teaching.progress'
        ])->findOrFail($id);
        // dd($teacher);
        return view('admin.progress', compact('teacher'));
    }
}
