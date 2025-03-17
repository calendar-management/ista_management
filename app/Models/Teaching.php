<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    use HasFactory;
    protected $table = 'teaching';

    protected $primaryKey = 'id_teaching';

    /**
     * Get the user (teacher) that owns the teaching record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Get the group that the teaching record belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    /**
     * Get the module that the teaching record belongs to.
     */
    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    /**
     * Get the progress associated with the teaching record.
     */
    public function progress()
    {
        return $this->hasOne(Progress::class, 'id_teaching');
    }
}
