<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UpdateUnitRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        $segments =$this->segments();
        $id = $segments[sizeof($segments) - 1];
        
        return [
            'unit' => ['required','max:20'], //alphaSpaces
            'status' => ['required','numeric']
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['unit'] = filter_var($input['unit'], FILTER_SANITIZE_STRING);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
        $input['updated_by'] = Auth::id();
        $this->merge($input);
    }
}
