<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\RowIdExistance;

class CartItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function all($keys = null)
    {
        $data = parent::all();
        $data['cart_id'] = $this->route('id');

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
                return [
                    'cart_id' => 'required|exists:carts,id',
                ];
            case 'POST':
                return [
                    'product_id' => 'required|numeric|exists:products,id',
                    'quantity' => 'required|numeric'
                ];
            case 'PUT':
                return [
                    'product_id' => 'required|numeric|exists:products,id',
                    'quantity' => 'required|numeric',
                    'cart_id' => 'required|exists:carts,id',
                    'row_id' => ['required', new RowIdExistance($this->id)],
                ];
            case 'DELETE':
                return [
                    'cart_id' => 'required|exists:carts,id',
                    'row_id' => ['required', new RowIdExistance($this->id)],
                ];
            
            default:
               return [];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
