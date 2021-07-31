<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreCampaignRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('campaign_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'campaign_category_id'    => [
                'required',
            ],
            'campaign_master_id'     => [
                'required',
            ],
            'title'    => [
                'required',
                'unique:promo_code_master',
            ],
            'description' => [
                'required',
            ],
            'start_date' => [
                'required',
            ],
            'end_date' => [
                'required',
            ],
            'code_length' => [
                'required',
                'integer',
            ],
            'reward_value' => [
                'required',
                'integer',
            ],
            
        ];
    }
}
