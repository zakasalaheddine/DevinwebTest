<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\Product;
use App\Discount;
use Illuminate\Support\Str;
use App\Http\Requests\CartItemsRequest;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Resources\Cart as CartResource;

class CartController extends Controller
{
    public function AddItems(CartItemsRequest $request, $cartId)
    {
        $selectedProduct = Product::find($request->product_id);
        $selectedCart = Cart::find($cartId);
        $images = [];
        foreach ($selectedProduct->images as $image) {
            array_push($images, $image->url);
        }
        
        $tax =round(number_format(env('TAX_VALUE')) / 100 * $selectedProduct->price, 2);
        $content = [
            'row_id' => uniqid(),
            'product_id' => $selectedProduct->id,
            'name' => $selectedProduct->description,
            'price' => $selectedProduct->price,
            'qty' => $request->quantity,
            'options' => $images,
            'tax' => $tax,
            'subtotal' => $tax * $selectedProduct->price * $request->quantity
        ];
        if ($selectedCart == null) {
            $selectedCart = new Cart;
            $selectedCart->content = json_encode([$content]);
            $selectedCart->discount = '';
            $selectedCart->save();
        } else {
            $oldContent = json_decode($selectedCart->content);
            array_push($oldContent, $content);
            $selectedCart->content = json_encode($oldContent);
            $selectedCart->save();
        }
        return new CartResource($selectedCart);
    }

    public function UpdateItem(CartItemsRequest $request, Cart $selectedCart)
    {
        $selectedProduct = Product::find($request->product_id);
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
            'subtotal' => $tax * $selectedProduct->price * $request->quantity
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

            return new CartResource($selectedCart);
        }
    }

    public function DeleteItem(CartItemsRequest $request, Cart $selectedCart)
    {
        $oldContent = json_decode($selectedCart->content);
        $newContent = [];
        if (count($oldContent) > 0) {
            for ($i=0; $i < count($oldContent); $i++) {
                if ($oldContent[$i]->row_id != $request->row_id) {
                    array_push($newContent, $oldContent[$i]);
                }
            }
        }
        $selectedCart->content = json_encode($newContent);
        $selectedCart->save();
        
        return new CartResource($selectedCart);
    }

    public function AddDiscount(StoreDiscountRequest $request, $id)
    {
        $selectedCart = Cart::find($id);
        $selectedDiscount = Discount::where('discount_code', $request->discount_code)->first();
        $content = json_decode($selectedCart->content);
        $discountedValue = 0;
        if ($content) {
            foreach ($content as $item) {
                $discountedValue += $item->subtotal;
            }
            $discountedValue = $discountedValue - ($selectedDiscount->percentage_value / 100 * $discountedValue);
        }
        $discountValue = [
            'code' => $selectedDiscount->discount_code,
            'discounted_amount' => $discountedValue,
            'value' => $selectedDiscount->percentage_value
        ];
        $selectedCart->discount = json_encode($discountValue);
        $selectedCart->save();
        
        return new CartResource($selectedCart);
    }

    public function GetCart(CartItemsRequest $request, $id)
    {
        $selectedCart = Cart::find($id);
        return new CartResource($selectedCart);
    }
}
