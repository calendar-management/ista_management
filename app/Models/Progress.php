<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_progress';

    /**
     * Get the teaching record that owns the progress.
     */
    public function teaching()
    {
        return $this->belongsTo(Teaching::class, 'id_teaching');
    }
}
