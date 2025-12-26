<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['table_id', 'total_price', 'status'];
    public function orders() { return $this->hasMany(Order::class); }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // Định nghĩa mối quan hệ với bảng OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
