<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HuellaCarbono extends Model
{
    use HasFactory;

    protected $table = 'huellas_carbonos';

    protected $fillable = [
        'prenda_id',
        'co2_fabricacion',
        'co2_total_nueva',
        'co2_segunda_mano',
        'co2_ahorrado',
        'porcentaje_ahorro',
        'categoria_calculo'
    ];

    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
