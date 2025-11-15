<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HuellaCarbono extends Model
{
    use HasFactory;

    protected $table = 'huellas_carbonos';

    protected $fillable = [
        'huella_nueva',
        'huella_reusada',
        'prenda_id'
        
    ];


    public $timestamps = false;

    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
