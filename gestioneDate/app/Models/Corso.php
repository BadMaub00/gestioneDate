<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corso extends Model
{
    protected $table = 'corso';
    protected $primaryKey = 'corso_id';
    protected $fillable = ['nome', 'tipo_corso'];

    public function insegnamenti()
    {
        return $this->hasMany(InsegnamentiCorso::class, 'corso_id', 'corso_id');
    }
}