<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $governorates = config('locations.governorates');
        $cities = $governorates[$this->input('governorate')] ?? [];

        return [
            // أرقام سورية: 09xxxxxxxx أو +9639xxxxxxxx أو 009639...
            'phone'           => ['required', 'string', 'regex:/^(\+?963|00963|0)?9\d{8}$/'],
            'governorate'     => ['required', 'string', Rule::in(array_keys($governorates))],
            'city'            => ['required', 'string', Rule::in($cities)],
            'address_details' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'    => 'رقم الهاتف غير صحيح، مثال: 0933123456',
            'governorate.in' => 'المحافظة غير صالحة.',
            'city.in'        => 'المنطقة لا تتبع المحافظة المختارة.',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone'           => 'رقم الهاتف',
            'governorate'     => 'المحافظة',
            'city'            => 'المنطقة',
            'address_details' => 'العنوان التفصيلي',
        ];
    }
}
