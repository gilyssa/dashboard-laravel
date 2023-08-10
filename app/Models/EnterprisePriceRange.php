<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnterprisePriceRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'price_band_id',
        'city_id',
        'status',
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function price_band_id()
    {
        return $this->belongsTo(PriceBand::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
