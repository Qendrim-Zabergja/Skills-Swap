<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;

class EmailVerificationRequest extends FormRequest
{
    public function authorize()
    {
        if (! hash_equals((string) $this->route('id'), (string) $this->user()->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'), 
                          sha1($this->user()->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    public function rules()
    {
        return [
            // No additional validation needed
        ];
    }

    public function fulfill()
    {
        if (! $this->user()->hasVerifiedEmail()) {
            $this->user()->markEmailAsVerified();

            event(new Verified($this->user()));
        }
    }
}