<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'original_url' => self::originalUrlRules(),
        ];
    }

    /**
     * @return list<string>
     */
    public static function originalUrlRules(): array
    {
        return [
            'required',
            'string',
            'url:http,https',
            'max:2048',
        ];
    }
}
