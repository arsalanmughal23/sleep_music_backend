<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the password is at least 8 characters long
        if (strlen($value) < 8) {
            return false;
        }

        // Check if the password contains at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Check if the password contains at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Check if the password contains at least one number
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Check if the password contains at least one special-character
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.';
    }
}
