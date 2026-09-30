<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    use HasFactory;

    protected $table = 'fichas';

    protected $fillable = [
        'estudiante',
        'representante',
        'cedulaRepresentante',
        'cedulaEstudiante',
        'telefono',
        'curso_id',
        'fecha',
    ];
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
