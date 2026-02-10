<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'kana' => 'required|string|max:50',
            'tel' => 'required|string|unique:customers,tel|max:20',
            'email' => 'required|email|max:255',
            'postcode' => 'required|string|max:7',
            'address' => 'required|string|max:100',
            'birthday' => 'nullable|date',
            'gender' => 'required|integer|in:0,1,2',
            'memo' => 'required|string|max:1000',
        ];
    }
}
