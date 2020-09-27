<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Cart extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $content = json_decode($this->content);
        $discount = json_decode($this->discount);
        $totalTax = 0;
        $totalCart = 0;
        foreach ($content as $item) {
            $totalCart += $item->subtotal;
            $totalTax += $item->tax;
        }
        return [
            'identifier' => $this->id,
            'items' => $content,
            'discount' => $discount,
            "summary" => [
                "discount_amount" => $discount->discounted_amount,
                "tax" => round($totalTax, 2),
                "total_amount" => $totalCart - $discount->discounted_amount
            ]
        ];
    }
}
