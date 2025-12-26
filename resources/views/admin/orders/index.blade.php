<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            🏪 QUẢN LÝ TRẠNG THÁI BÀN
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="tables-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($tables as $table)
                    @php 
                        $activeOrder = $table->orders->first(); 
                    @endphp
                    
                    <div id="table-card-{{ $table->id }}" 
                         class="relative bg-white p-6 rounded-2xl shadow-sm border-2 transition-all 
                         {{ $activeOrder ? 'border-orange-500 bg-orange-50' : 'border-gray-100' }}">
                        
                        <div class="flex flex-col items-center">
                            <span id="icon-{{ $table->id }}" class="text-3xl mb-2">{{ $activeOrder ? '🍽️' : '🪑' }}</span>
                            <h3 class="font-bold text-lg">{{ $table->name }}</h3>
                            
                            <div id="status-container-{{ $table->id }}" class="flex flex-col items-center w-full">
                                @if($activeOrder)
                                    <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2 py-0.5 rounded-full mt-1">CÓ KHÁCH</span>
                                    <p class="text-sm font-semibold mt-2">{{ number_format($activeOrder->total_price) }}đ</p>
                                    
                                    <button onclick="openModal('{{ $table->name }}', {{ $table->id }}, {{ $activeOrder->id }})" 
                                        class="mt-4 w-full bg-orange-500 text-white text-xs py-2 rounded-lg font-bold hover:bg-orange-600 transition">
                                        XEM MÓN
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 mt-1 uppercase font-semibold">Trống</span>
                                    <div class="h-10"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 id="modalTableName" class="text-xl font-bold text-gray-800"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <div id="modalContent" class="space-y-3 max-h-96 overflow-y-auto mb-6">
                </div>

            <div id="modalFooter" class="pt-4 border-t">
                <button id="btnPay" onclick="" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i> THANH TOÁN & DỌN BÀN
                </button>
            </div>
        </div>
    </div>

    <script>
        const tableData = @json($tables);

        function openModal(tableName, tableId, orderId) {
            const modal = document.getElementById('orderModal');
            const nameEl = document.getElementById('modalTableName');
            const contentEl = document.getElementById('modalContent');
            const btnPay = document.getElementById('btnPay');
            
            nameEl.innerText = "Chi tiết " + tableName;
            
            // Tìm dữ liệu đơn hàng
            const table = tableData.find(t => t.id === tableId);
            const order = table.orders[0];

            let html = '';
            order.order_items.forEach(item => {
                html += `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800">${item.product.name}</p>
                            <p class="text-xs text-gray-500 font-medium">SL: ${item.quantity}</p>
                        </div>
                        <p class="font-semibold text-orange-600">${new Intl.NumberFormat('vi-VN').format(item.price * item.quantity)}đ</p>
                    </div>
                `;
            });
            
            contentEl.innerHTML = html;
            
            // Gán sự kiện thanh toán cho nút bấm trong modal
            btnPay.onclick = () => payOrder(orderId, tableId);
            
            modal.classList.remove('hidden');
        }

        async function payOrder(orderId, tableId) {
            if(!confirm('Xác nhận khách đã thanh toán và dọn sạch bàn?')) return;

            try {
                const response = await fetch(`/admin/orders/${orderId}/pay`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // 1. Đóng Modal
                    closeModal();

                    // 2. Cập nhật giao diện bàn ngay lập tức (Biến thành bàn trống)
                    const card = document.getElementById(`table-card-${tableId}`);
                    const icon = document.getElementById(`icon-${tableId}`);
                    const statusContainer = document.getElementById(`status-container-${tableId}`);

                    // Chuyển màu card về xám (trống)
                    card.classList.remove('border-orange-500', 'bg-orange-50');
                    card.classList.add('border-gray-100', 'bg-white');
                    
                    // Đổi icon
                    icon.innerText = '🪑';
                    
                    // Thay đổi nội dung bên trong card bàn
                    statusContainer.innerHTML = `
                        <span class="text-xs text-gray-400 mt-1 uppercase font-semibold">Trống</span>
                        <div class="h-10"></div>
                    `;

                    alert('✅ Thanh toán thành công. Bàn đã được làm trống!');
                } else {
                    alert('❌ Có lỗi xảy ra khi thanh toán.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Không thể kết nối đến máy chủ.');
            }
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }
    </script>
</x-app-layout>