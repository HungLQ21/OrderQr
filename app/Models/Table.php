<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{



    protected $fillable = ['name', 'status'];

    // Thêm hàm này để Table hiểu rằng nó có nhiều đơn hàng (Orders)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
