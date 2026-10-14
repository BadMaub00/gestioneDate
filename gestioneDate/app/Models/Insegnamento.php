<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insegnamento extends Model
{
    protected $table = 'insegnamento';
    protected $primaryKey = 'insegnamento_id';
    protected $fillable = ['nome'];

    public function insegnamenti()
    {
        return $this->hasMany(InsegnamentiCorso::class, 'insegnamento_id', 'insegnamento_id');
    }
}