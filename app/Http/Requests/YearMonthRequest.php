<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class YearMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year'  => 'required|integer|min:2000|max:4000',
            'month' => 'required|integer|min:1|max:12',
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->all(), $this->route()?->parameters() ?? []);
    }
}
