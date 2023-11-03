<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'deliverer_id',
        'user_id',
        'removed',
        'quantity',
        'type',
        'updated_id',
        'removed_id',
        'enterprise_price_range_id', 
        'currentPrice',
        'isNote',
        'date'
    ];

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(Deliverer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_id');
    }

    public function removedBy()
    {
        return $this->belongsTo(User::class, 'removed_id');
    }

    public function enterprisePriceRange()
{
    return $this->belongsTo(EnterprisePriceRange::class, 'enterprise_price_range_id');
}
}

