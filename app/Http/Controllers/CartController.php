<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\Product;
use App\Discount;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function AddItems(Request $request, $cartId)
    {
        $selectedProduct = Product::find($request->product_id);
        if ($selectedProduct == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Product Not Found'
            ]);
        }
        $selectedCart = Cart::find($cartId);
        $images = [];
        foreach ($selectedProduct->images as $image) {
            array_push($images, $image->url);
        }
        
        $tax =number_format(env('TAX_VALUE')) / 100 * $selectedProduct->price;
        $content = [
            'row_id' => uniqid(),
            'product_id' => $selectedProduct->id,
            'name' => $selectedProduct->description,
            'price' => $selectedProduct->price,
            'qty' => $request->quantity,
            'options' => $images,
            'tax' => $tax,
            'subtotal' => ($tax + $selectedProduct->price) * $request->quantity
        ];
        if ($selectedCart == null) {
            $selectedCart = new Cart;
            $selectedCart->content = json_encode([$content]);
            $selectedCart->discount = '';
            $selectedCart->save();
        } else {
            $oldContent = json_decode($selectedCart->content);
            array_push($oldContent, $content);
            $selectedCart->content = $oldContent;
            $selectedCart->save();
        }
        dd($selectedCart);
        return response()->json([
            'Status' => 'SUCCESS',
            'Data' => $selectedCart->content
        ]);
    }

    public function UpdateItem(Request $request, $cartId)
    {
        $selectedProduct = Product::find($request->product_id);
        if ($selectedProduct == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Product Not Found'
            ]);
        }
        $selectedCart = Cart::find($cartId);
        if ($selectedCart == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Cart Not Found'
            ]);
        }
        $oldContent = json_decode($selectedCart->content);
        $images = [];
        foreach ($selectedProduct->images as $image) {
            array_push($images, $image->url);
        }
        $tax =number_format(env('TAX_VALUE')) / 100 * $selectedProduct->price;
        $content = [
            'row_id' => $request->row_id,
            'product_id' => $selectedProduct->id,
            'name' => $selectedProduct->description,
            'price' => $selectedProduct->price,
            'qty' => $request->quantity,
            'options' => $images,
            'tax' => $tax,
            'subtotal' => ($tax + $selectedProduct->price) * $request->quantity
        ];
        $selectedIndex = -1;
        for ($i=0; $i < $oldContent; $i++) {
            if ($oldContent[$i]->row_id == $request->row_id) {
                $selectedIndex = $i;
                break;
            }
        }
        if ($selectedIndex > -1) {
            $oldContent[$selectedIndex] = $content;
            $selectedCart->content = json_encode($oldContent);
            $selectedCart->save();
        }
        dd($selectedCart);
        return response()->json([
            'Status' => 'SUCCESS',
            'Data' => $selectedCart->content
        ]);
    }

    public function DeleteItem(Request $request, $cartId)
    {
        $selectedCart = Cart::find($cartId);
        if ($selectedCart == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Cart Not Found'
            ]);
        }
        $oldContent = json_decode($selectedCart->content);
        $newContent = [];
        if (count($oldContent) > 0) {
            for ($i=0; $i < $oldContent; $i++) {
                if ($oldContent[$i]->row_id != $request->row_id) {
                    array_push($newContent, $oldContent[$i]);
                }
            }
        }
        $selectedCart->content = json_encode($newContent);
        $selectedCart->save();
        dd($selectedCart);
        return response()->json([
            'Status' => 'SUCCESS',
            'Data' => $selectedCart->content
        ]);
    }

    public function AddDiscount(Request $request, $cartId)
    {
        $selectedCart = Cart::find($cartId);
        if ($selectedCart == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Cart Not Found'
            ]);
        }
        $selectedDiscount = Discount::where('discount_code', $request->discount_code)->first();
        if ($selectedDiscount == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Discount Not Found'
            ]);
        }
        $content = json_decode($selectedCart->content);
        $discountedValue = 0;
        foreach ($content as $item) {
            $discountedValue += $item->subtotal;
        }
        $discountedValue = $discountedValue - ($selectedDiscount->percentage_value / 100 * $discountedValue);
        $discountValue = [
            'code' => $selectedDiscount->discount_code,
            'discounted_amount' => $discountedValue,
            'value' => $selectedDiscount->percentage_value
        ];
        $selectedCart->discount = json_encode($discountValue);
        $selectedCart->save();
        return response()->json([
            'Status' => 'SUCCESS',
            'Data' => json_decode($selectedCart->discount)
        ]);
    }

    public function GetCart($cartId)
    {
        $selectedCart = Cart::find($cartId);
        if ($selectedCart == null) {
            return response()->json([
                'Status' => 'ERROR',
                'Message' => 'Cart Not Found'
            ]);
        }
        $content = json_decode($selectedCart->content);
        $discount = json_decode($selectedCart->discount);
        $totalTax = 0;
        $totalCart = 0;
        $content = json_decode($selectedCart->content);
        foreach ($content as $item) {
            $totalCart += $item->subtotal;
            $totalTax += $item->tax;
        }
        $data = [
            "identifier" => $selectedCart->id,
            "items" => $content,
            "discount" => $discount,
            "summary" => [
                "discount_amount" => $discount->discounted_amount,
                "tax" => $totalTax,
                "total_amount" => $totalCart - $discount->discounted_amount
            ]
        ];
        return response()->json($data);
    }
}
