<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Role name is required',
            'name.unique' => 'Role name must be unique',
            'permissions.required' => 'At least one permission is required',
            'permissions.array' => 'Permissions must be an array',
            'permissions.*.string' => 'Each permission must be a string',
            'permissions.*.exists' => 'Each permission must exist in the permissions table',
        ];
    }
}
