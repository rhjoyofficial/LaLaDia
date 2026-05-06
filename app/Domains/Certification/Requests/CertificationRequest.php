<?php

namespace App\Domains\Certification\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('certification.manage') || auth()->user()->hasRole('Super Admin');
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'               => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'category'           => ['nullable', 'string', 'max:255'],
            'organization'       => ['nullable', 'string', 'max:255'],
            'given_date'         => ['nullable', 'date'],
            'expiry_date'        => ['nullable', 'date', 'after_or_equal:given_date'],
            'additional_details' => ['nullable', 'string'],
            'logo'               => [$isUpdate ? 'nullable' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'image'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'          => ['boolean'],
            'sort_order'         => ['nullable', 'integer'],
        ];
    }
}
