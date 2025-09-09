<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_price',
        'cash_paid',
        'payment_method',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}