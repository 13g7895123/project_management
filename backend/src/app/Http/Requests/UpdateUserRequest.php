<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');
        $isModifyingSelf = $this->user()->id == $userId;

        return [
            'name' => 'sometimes|required|string|max:255',
            'username' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId)
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'bio' => 'sometimes|nullable|string|max:1000',
            'website' => 'sometimes|nullable|url|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'company' => 'sometimes|nullable|string|max:255',
            'position' => 'sometimes|nullable|string|max:255',
            'role' => [
                'sometimes',
                'required',
                Rule::in([User::ROLE_ADMIN, User::ROLE_USER]),
                // Prevent self role modification
                function ($attribute, $value, $fail) use ($isModifyingSelf) {
                    if ($isModifyingSelf) {
                        $fail('You cannot modify your own role.');
                    }
                }
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_SUSPENDED]),
                // Prevent self status modification
                function ($attribute, $value, $fail) use ($isModifyingSelf) {
                    if ($isModifyingSelf) {
                        $fail('You cannot modify your own status.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'username.unique' => 'This username is already taken.',
            'role.required' => 'Please select a user role.',
            'role.in' => 'Invalid role selected.',
            'status.required' => 'Please select a user status.',
            'status.in' => 'Invalid status selected.',
            'website.url' => 'Please provide a valid website URL.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'bio' => 'biography',
            'website' => 'website URL',
            'location' => 'location',
            'company' => 'company name',
            'position' => 'job position',
            'role' => 'user role',
            'status' => 'account status',
        ];
    }
}