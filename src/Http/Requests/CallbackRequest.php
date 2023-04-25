<?php

namespace JustBetter\MagentoClient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $oauth_consumer_key
 * @property string $oauth_consumer_secret
 * @property string $oauth_verifier
 */
class CallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'oauth_consumer_key' => 'required|string|max:32',
            'oauth_consumer_secret' => 'required|string|max:32',
            'oauth_verifier' => 'required|string|max:32',
        ];
    }
}
