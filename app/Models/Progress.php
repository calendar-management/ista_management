<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = ['formateur_id', 'module_id', 'date', 'hours_completed', 'notes'];

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
