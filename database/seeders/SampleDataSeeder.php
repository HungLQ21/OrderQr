<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 5 bàn mẫu
        for ($i = 1; $i <= 5; $i++) {
            Table::updateOrCreate(['name' => 'Bàn số ' . $i], ['status' => 'available']);
        }

        // Tạo danh mục
        $cat1 = Category::updateOrCreate(['name' => 'Món Chính']);
        $cat2 = Category::updateOrCreate(['name' => 'Đồ Uống']);

        // Tạo món ăn mẫu
        Product::updateOrCreate(
            ['name' => 'Phở Bò'],
            [
                'category_id' => $cat1->id,
                'price' => 50000,
                'description' => 'Phở bò Hà Nội thơm ngon',
                'image' => 'https://vcdn1-dulich.vnecdn.net/2022/06/03/pho-8902-1654230039.jpg?w=0&h=0&q=100&dpr=2&fit=crop&s=iY_reY_O6p-U785uS1HhLA',
                'is_available' => true
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Cà Phê Sữa'],
            [
                'category_id' => $cat2->id,
                'price' => 25000,
                'description' => 'Cà phê pha phin truyền thống',
                'image' => 'https://Super_Coffee_Image_Link_Example',
                'is_available' => true
            ]
        );
    }
}