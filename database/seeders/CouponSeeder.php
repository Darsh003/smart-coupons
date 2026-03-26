<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'SAVE10',
                'type' => 'percentage',
                'value' => 10.00,
                'max_discount' => null,
                'min_order_value' => 0.00,
                'is_combinable' => false,
                'is_active' => true,
                'usage_limit' => null,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
            ],
            [
                'code' => 'FLAT200',
                'type' => 'flat',
                'value' => 200.00,
                'max_discount' => null,
                'min_order_value' => 500.00,
                'is_combinable' => false,
                'is_active' => true,
                'usage_limit' => null,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
            ],
            [
                'code' => 'BIGSALE',
                'type' => 'percentage',
                'value' => 20.00,
                'max_discount' => 300.00,
                'min_order_value' => 0.00,
                'is_combinable' => false,
                'is_active' => true,
                'usage_limit' => null,
                'valid_from' => now(),
                'valid_until' => now()->addYear(),
            ]
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
