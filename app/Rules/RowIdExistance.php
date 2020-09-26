<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Cart;

class RowIdExistance implements Rule
{
    private $cart;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($selectedCart)
    {
        $this->cart = Cart::find($selectedCart);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->cart) {
            return false;
        }
        $oldContent = json_decode($this->cart->content);
        $selectedIndex = -1;
        for ($i=0; $i < count($oldContent); $i++) {
            if ($oldContent[$i]->row_id == $value) {
                $selectedIndex = $i;
                break;
            }
        }
        if ($selectedIndex > -1) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Row id dosen\'t exist.';
    }
}
