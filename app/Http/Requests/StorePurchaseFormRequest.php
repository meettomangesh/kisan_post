<?php

namespace App\Http\Requests;

use App\Models\PurchaseForm;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StorePurchaseFormRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('purchase_form_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        return [
            'supplier_name' => ['required','max:250'],
            'product_name' => ['required','max:250'],
            'unit' => ['required','max:20'],
            'category' => ['required','max:50'],
            'total_in_kg' => ['required','max:10'],
            'total_units' => ['required','max:10'],
            'price' => ['required','numeric'],
            'order_date' => ['required']
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['supplier_name'] = filter_var($input['supplier_name'], FILTER_SANITIZE_STRING);
        $input['product_name'] = filter_var($input['product_name'], FILTER_SANITIZE_STRING);
        $input['unit'] = filter_var($input['unit'], FILTER_SANITIZE_STRING);
        $input['category'] = filter_var($input['category'], FILTER_SANITIZE_STRING);
        $input['total_in_kg'] = filter_var($input['total_in_kg'], FILTER_SANITIZE_STRING);
        $input['total_units'] = filter_var($input['total_units'], FILTER_SANITIZE_STRING);
        $input['price'] = filter_var($input['price'], FILTER_SANITIZE_STRING);
        $input['order_date'] = filter_var($input['order_date'], FILTER_SANITIZE_STRING);
        $input['created_by'] = Auth::id();
        $this->merge($input);
    }
}
