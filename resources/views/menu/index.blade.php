<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Gọi Món - Bàn {{ $tableId }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Giới hạn độ rộng của Cart Bar khớp với Container */
        .cart-container {
            left: 50% !important;
            transform: translateX(-50%);
            width: calc(100% - 2rem);
            max-width: 28rem; /* Khớp với max-w-md */
        }
    </style>
</head>
<body class="bg-gray-100 pb-24">

    <div class="max-w-md mx-auto min-h-screen bg-gray-50 shadow-2xl relative">
        
        <header class="bg-orange-500 text-white p-4 shadow-md sticky top-0 z-50">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">🍕 GEMINI RESTO</h1>
                <span class="bg-white text-orange-600 px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                    Bàn: {{ $tableId ?? 'Trống' }}
                </span>
            </div>
        </header>

        <div class="flex overflow-x-auto p-4 bg-white gap-4 no-scrollbar border-b sticky top-[60px] z-40">
            <button class="bg-orange-100 text-orange-600 px-4 py-2 rounded-lg font-semibold whitespace-nowrap shadow-sm">Tất cả</button>
            <button class="text-gray-500 px-4 py-2 font-medium whitespace-nowrap hover:text-orange-500">Món chính</button>
            <button class="text-gray-500 px-4 py-2 font-medium whitespace-nowrap hover:text-orange-500">Đồ uống</button>
            <button class="text-gray-500 px-4 py-2 font-medium whitespace-nowrap hover:text-orange-500">Tráng miệng</button>
        </div>

        <main class="p-4 space-y-4">
            @foreach($products as $product)
            <div class="bg-white p-3 rounded-2xl shadow-sm flex gap-4 items-center mb-3 product-item hover:shadow-md transition-shadow" 
                 data-id="{{ $product->id }}" 
                 data-name="{{ $product->name }}" 
                 data-price="{{ $product->price }}">
                
                <div class="w-24 h-24 flex-shrink-0">
                    <img src="{{ asset($product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover rounded-xl shadow-inner border border-gray-50">
                </div>
                
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800 leading-tight mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-400 mb-2 line-clamp-1">{{ $product->description }}</p>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-orange-600 font-extrabold text-lg">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        
                        <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-2 py-1">
                            <button onclick="updateQty({{ $product->id }}, -1)" 
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-md shadow-sm text-orange-600 font-bold active:scale-90 transition">-</button>
                            
                            <span id="qty-{{ $product->id }}" class="min-w-[24px] text-center font-bold text-gray-700">0</span>
                            
                            <button onclick="updateQty({{ $product->id }}, 1)" 
                                    class="w-8 h-8 flex items-center justify-center bg-orange-500 rounded-md shadow-sm text-white font-bold active:scale-90 transition">+</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </main>

        <div id="cart-bar" class="hidden fixed bottom-6 cart-container bg-gray-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center z-[60]">
            <div>
                <p id="total-qty-text" class="text-xs text-gray-400 uppercase tracking-wider">Đã chọn 0 món</p>
                <p id="total-price-text" class="text-xl font-bold text-orange-400">0đ</p>
            </div>
            <button onclick="submitOrder()" class="bg-orange-500 hover:bg-orange-600 px-6 py-3 rounded-xl font-bold transition transform active:scale-95 shadow-lg flex items-center">
                GỬI ORDER <i class="fas fa-paper-plane ml-2"></i>
            </button>
        </div>

    </div> <script>
        // ... (Giữ nguyên phần JavaScript của ông, nó đã chạy rất tốt rồi) ...
        let cart = {}; 

        function updateQty(id, change) {
            if (!cart[id]) cart[id] = 0;
            cart[id] += change;
            if (cart[id] < 0) cart[id] = 0;
            document.getElementById(`qty-${id}`).innerText = cart[id];
            calculateTotal();
        }

        function calculateTotal() {
            let totalItems = 0;
            let totalPrice = 0;
            for (let id in cart) {
                const qty = cart[id];
                if (qty > 0) {
                    const el = document.querySelector(`[data-id="${id}"]`);
                    const price = parseFloat(el.getAttribute('data-price'));
                    totalItems += qty;
                    totalPrice += qty * price;
                }
            }
            document.getElementById('total-qty-text').innerText = `Đã chọn ${totalItems} món`;
            document.getElementById('total-price-text').innerText = new Intl.NumberFormat('vi-VN').format(totalPrice) + 'đ';
            
            const cartBar = document.getElementById('cart-bar');
            totalItems > 0 ? cartBar.classList.remove('hidden') : cartBar.classList.add('hidden');
        }

        async function submitOrder() {
            const finalCart = Object.fromEntries(Object.entries(cart).filter(([_, q]) => q > 0));
            if (Object.keys(finalCart).length === 0) return alert("Vui lòng chọn món!");

            try {
                const response = await fetch("{{ route('order.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ cart: finalCart })
                });
                const result = await response.json();
                if (response.ok) {
                    alert("✅ " + result.message);
                    window.location.href = "/";
                } else {
                    alert("❌ Lỗi: " + result.message);
                }
            } catch (error) {
                alert("Không thể kết nối máy chủ!");
            }
        }
    </script>
</body>
</html>