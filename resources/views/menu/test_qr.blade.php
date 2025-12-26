<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test quét mã QR - Bàn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-center mb-8">📱 GIẢ LẬP QUÉT MÃ QR TẠI BÀN</h1>
        <div class="fixed top-4 right-4 z-50">
    @if (Route::has('login'))
        <div class="flex gap-2">
            @auth
                <a href="{{ url('/admin/orders') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold shadow-lg hover:bg-black transition text-sm">
                    <i class="fas fa-chart-line mr-2"></i>VÀO QUẢN TRỊ
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-white text-gray-800 px-4 py-2 rounded-lg font-bold shadow-md hover:bg-gray-100 transition text-sm border">
                    <i class="fas fa-user-lock mr-2"></i>NHÂN VIÊN ĐĂNG NHẬP
                </a>
            @endauth
        </div>
    @endif
</div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($tables as $table)
            <div class="bg-white p-4 rounded-2xl shadow-md flex flex-col items-center border-2 border-transparent hover:border-orange-500 transition cursor-pointer">
                <div class="bg-gray-200 w-32 h-32 rounded-lg mb-4 flex items-center justify-center relative">
                    <i class="fas fa-qrcode text-5xl text-gray-400"></i>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 bg-black/20 transition rounded-lg">
                         <a href="{{ route('menu', ['table_id' => $table->id]) }}" 
                            class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full font-bold">
                            QUÉT NGAY
                         </a>
                    </div>
                </div>
                
                <h3 class="font-bold text-lg text-gray-800">{{ $table->name }}</h3>
                <span class="text-xs text-gray-500 italic">ID Bàn: {{ $table->id }}</span>
                
                <a href="{{ route('menu', ['table_id' => $table->id]) }}" 
                   class="mt-3 text-blue-600 hover:underline text-sm font-medium">
                   Giả lập quét bàn {{ $table->id }}
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-12 bg-blue-50 p-4 rounded-xl border border-blue-200">
            <h4 class="text-blue-800 font-bold mb-2 uppercase text-sm"><i class="fas fa-info-circle mr-2"></i>Hướng dẫn:</h4>
            <p class="text-blue-700 text-sm italic">
                Trong thực tế, mỗi bàn sẽ có 1 mã QR in ra giấy. Khi khách dùng điện thoại quét, 
                camera sẽ tự động mở link: <strong>{{ config('app.url') }}/menu?table_id=[ID_CUA_BAN]</strong>
            </p>
        </div>
    </div>

</body>
</html>