<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceBand extends Model
{
    protected $table = 'price_bands';

    protected $fillable = ['value', 'status'];

    // Outros relacionamentos, acessores e mutadores podem ser adicionados aqui

}
