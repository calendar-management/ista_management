<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fromateur extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'admin_id', 'specialty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
