<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'transaction_id', 'status', 'amount'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
