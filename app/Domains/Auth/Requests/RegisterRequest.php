<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $phone = preg_replace('/[^0-9]/', '', $this->phone ?? '');

    // Normalize country-code prefix: 8801XXXXXXXXX → 01XXXXXXXXX
    if (strlen($phone) === 13 && str_starts_with($phone, '88')) {
      $phone = substr($phone, 2);
    }

    $this->merge(['phone' => $phone]);
  }

  public function rules(): array
  {
    return [
      'name'     => 'required|string|max:150',
      'phone'    => ['required', 'string', 'unique:users,phone', 'regex:/^01[3-9]\d{8}$/'],
      'email'    => 'nullable|email|max:255|unique:users,email',
      'password' => 'required|string|min:8|confirmed',
    ];
  }

  public function messages(): array
  {
    return [
      'phone.unique'       => 'This phone number is already registered.',
      'phone.regex'        => 'Phone must be a valid Bangladeshi number (e.g. 01712345678 or +8801712345678).',
      'email.unique'       => 'This email address is already registered.',
      'password.confirmed' => 'The password confirmation does not match.',
      'password.min'       => 'Password must be at least 8 characters.',
    ];
  }
}
