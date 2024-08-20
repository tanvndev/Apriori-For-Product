<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\UserAction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        // Nếu không có đủ users hoặc products, bạn có thể tạo thêm ở đây

        $actionTypes = ['search', 'view', 'purchase'];

        $chunkSize = 1000; // Số lượng bản ghi được tạo trong mỗi chunk

        for ($i = 0; $i < 100; $i++) { // 100 chunks, mỗi chunk 1000 bản ghi
            $behaviors = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $behaviors[] = [
                    'user_id' => $userIds[array_rand($userIds)],
                    'action' => $actionTypes[array_rand($actionTypes)],
                    'product_id' => $productIds[array_rand($productIds)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            UserAction::insert($behaviors);
        }
    }
}
