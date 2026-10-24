<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    use HasFactory;

    protected $table = 'edificio';
    protected $primaryKey = 'edificio_id';
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    

    protected $fillable = [
        'nome',
        'via',
    ];

    /**
     * Relazione con le aule appartenenti a questo edificio.
     */
    public function aule()
    {
        return $this->hasMany(Aula::class, 'edificio_id', 'edificio_id');
    }
}