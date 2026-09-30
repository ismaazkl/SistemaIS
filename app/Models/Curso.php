<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    
    use hashFactory;
    protected $table = 'cursos';
    protected $fillable = [
        'id',
        'curso',
    ];

    public function fichas()
    {
        return $this->hasMany(Ficha::class, 'curso_id');
    }


}
