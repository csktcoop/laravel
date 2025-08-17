<?php

namespace Database\Seeders;

use App\Models\Inventory\Category;
use App\Models\Inventory\Price;
use App\Models\Inventory\Product;
use App\Models\Inventory\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total = 21;
        // $total = 2000; // For benchmark purpose

        // Seed warehouses
        $virtual_warehouse = Warehouse::firstOrCreate( // Products in the warehouse should have null quantity.
            ['id' => 1],
            [
                'name' => 'Virtual Warehouse',
                'location' => 'Multiverse',
                'source' => 'Seeder',
            ],
        );
        $warehouses = collect([$virtual_warehouse]);
        $warehouses = Warehouse::factory()
            ->count($total - 1)
            ->create(['source' => 'Seeder']);
        $warehouses = $warehouses->prepend($virtual_warehouse);

        // Seed categories
        $createTree = true;
        for ($i = 0; $i < $total; $i++) {
            if ($createTree) {
                $createTree = false;
                $root = Category::factory()->create([
                    'parent_id' => Category::PARENT_ID_NONE,
                    'source' => 'Seeder',
                ]);

                $n = $i + 3;
                for ($i; $i < $n; $i++) {
                    // Create child categories
                    $level1 = Category::factory()->create([
                        'parent_id' => $root->id,
                        'source' => 'Seeder',
                    ]);

                    $m = $i + 9;
                    for ($i; $i < $m; $i++) {
                        // Create grand-child categories
                        Category::factory()->create([
                            'parent_id' => $level1->id,
                            'source' => 'Seeder',
                        ]);
                    }
                }

                continue;
            }
            Category::factory()->create(['source' => 'Seeder']);
        }

        // Seed products
        $firstId = 1;
        Product::factory()
            ->count($total)
            ->create(['source' => 'Seeder'])
            ->each(function ($product) use ($firstId, $total) {
                // Attach warehouses with qty
                $randomWarehouseId = rand($firstId, $total);
                $product->warehouses()->attach($randomWarehouseId, [
                    'quantity' => $randomWarehouseId === 1 ? null : 100,
                ]);

                // Attach categories
                $product->categories()->attach(rand($firstId, $total));
            });

        // Seed prices
        Price::factory()->count($total)
            ->make(['source' => 'Seeder'])
            ->each(function ($price) use ($total, $firstId) {
                $price->product_id = rand($firstId, $total);
                $price->save();
            });
    }
}
