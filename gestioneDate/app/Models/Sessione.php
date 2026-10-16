<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessione extends Model
{
    use HasFactory;

    protected $table = 'sessione_esami';
    protected $primaryKey = 'sessione_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'data_inizio', 
        'data_fine', 
        'nome', 
        'anno', 
        'numero_appelli'
    ];

    public function appelli()
    {
        return $this->hasMany(Appello::class, 'sessione_id', 'sessione_id');
    }
}