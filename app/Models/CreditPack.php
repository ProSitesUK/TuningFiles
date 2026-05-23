<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPack extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['is_active' => 'bool'];

    public function priceFormatted(): string { return '£'.number_format($this->price_pennies / 100, 0); }
}
