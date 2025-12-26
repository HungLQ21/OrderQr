<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
    use App\Models\Table;
    use Illuminate\Support\Facades\DB;
class MenuController extends Controller
{
  public function index(Request $request) {
    $tableId = $request->query('table_id');
    session(['table_id' => $tableId]); // Dòng này cực kỳ quan trọng
    
    $products = Product::all();
    return view('menu.index', compact('products', 'tableId'));
}
    public function storeOrder(Request $request) {
    // 1. Kiểm tra dữ liệu đầu vào
    $cart = $request->input('cart');
    $tableId = session('table_id') ?? $request->query('table_id'); 

    if (!$cart || empty($cart)) {
        return response()->json(['message' => 'Giỏ hàng trống!'], 400);
    }

    // 2. Dùng DB Transaction để đảm bảo nếu lỗi thì không lưu gì hết
    DB::beginTransaction();
    try {
        $order = Order::create([
            'table_id' => $tableId ?? 1, // Mặc định bàn 1 nếu session lỗi
            'total_price' => 0,
            'status' => 'pending'
        ]);

        $total = 0;
        foreach ($cart as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                $price = $product->price * $qty;
                $total += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $qty,
                    'price' => $product->price
                ]);
            }
        }

        $order->update(['total_price' => $total]);
        
        DB::commit(); // Lưu thật vào Database
        return response()->json(['message' => 'Đặt món thành công!']);

    } catch (\Exception $e) {
        DB::rollback(); // Có lỗi thì hủy hết
        return response()->json(['message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}
public function testQR() {
    // Lấy toàn bộ danh sách bàn từ database
    $tables = Table::all();
    return view('menu.test_qr', compact('tables'));
}
}
