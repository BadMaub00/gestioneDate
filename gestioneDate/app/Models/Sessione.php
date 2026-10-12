<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessione extends Model
{
    protected $table = 'sessione_esami';
    protected $primaryKey = 'sessione_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['data_inizio','data_fine','nome','anno'];

    public function appelli()
    {
        return $this->hasMany(Appello::class, 'sessione_id', 'sessione_id');
    }
}
