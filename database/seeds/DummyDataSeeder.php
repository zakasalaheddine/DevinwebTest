<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\ProductImage;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::create([
            'description' => 'Product A',
            'price' => 152,
        ]);
        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2016/06/07/17/15/yogurt-1442034__340.jpg',
            'product_id' => $product->id
        ]);
        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2016/04/15/08/04/strawberries-1330459__340.jpg',
            'product_id' => $product->id
        ]);
        $product = Product::create([
            'description' => 'Product B',
            'price' => 20,
        ]);

        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2016/02/19/11/35/make-up-1209798__340.jpg',
            'product_id' => $product->id
        ]);
        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2015/12/30/11/57/fruit-basket-1114060__340.jpg',
            'product_id' => $product->id
        ]);
        $product = Product::create([
            'description' => 'Product C',
            'price' => 15,
        ]);
        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2015/04/20/13/22/hands-731265__340.jpg',
            'product_id' => $product->id
        ]);
        ProductImage::create([
            'url' => 'https://cdn.pixabay.com/photo/2016/10/13/22/52/walnut-1739021__340.jpg',
            'product_id' => $product->id
        ]);
    }
}
