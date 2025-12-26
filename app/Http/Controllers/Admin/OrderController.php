<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table; // Thêm Model Table
use App\Models\Product; // Thêm Model Product
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // 1. Lấy những bàn CÓ đơn hàng đang chờ (pending)
        // Những bàn không có đơn hoặc đơn đã thanh toán sẽ bị loại bỏ khỏi danh sách này
        $tables = Table::whereHas('orders', function($query) {
            $query->where('status', 'pending');
        })->with(['orders' => function($query) {
            $query->where('status', 'pending')->with('orderItems.product');
        }])->get();

        return view('admin.orders.index', compact('tables'));
    }

    public function markAsPaid($id) {
    try {
        $order = Order::findOrFail($id);
        $order->status = 'paid';
        $order->save();

        // Trả về JSON để JavaScript biết là đã xong
        return response()->json([
            'success' => true, 
            'message' => 'Đã thanh toán thành công'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function showMenu($tableId) 
    {
        $products = Product::all();
        
        // Khách mới vào bàn này chỉ thấy "trống" nếu đơn trước đã 'paid'
        $currentOrder = Order::where('table_id', $tableId)
                             ->where('status', 'pending')
                             ->first();

        return view('menu', compact('products', 'tableId', 'currentOrder'));
    }
}