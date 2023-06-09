<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deliverer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deliverers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status', 'pix', 'cnpj_or_cpf'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
