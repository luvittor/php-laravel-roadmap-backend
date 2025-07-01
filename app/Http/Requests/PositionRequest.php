<?php

namespace App\Http\Requests;

class PositionRequest extends YearMonthRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'order' => 'required|integer|min:1',
        ]);
    }
}
