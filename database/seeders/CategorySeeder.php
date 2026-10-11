<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    Category::insert([
        ['name' => 'Teknis',  'sla_hours' => 24, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Billing', 'sla_hours' => 48, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Umum',    'sla_hours' => 72, 'created_at' => now(), 'updated_at' => now()],
    ]);
}
}
