<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|string|max:255',
            'gender'      => 'nullable|string|in:FEMALE,MALE',
            'age'         => 'nullable|integer',
            'phone'       => 'nullable|string|max:255',
            'photo'       => 'nullable|image|mimes:png,jpg,jpeg,svg,gif|max:2048',
            'team_id'     => 'required|exists:teams,id',
            'role_id'     => 'required|exists:roles,id',
        ];
    }
}
