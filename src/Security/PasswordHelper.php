<?php

namespace App\Security;

class PasswordHelper
{
    public function validatePassword($password)
    {
        $minLength = 8;
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasDigits = preg_match('/\d/', $password);

        return (
            strlen($password) >= $minLength &&
            $hasUpperCase &&
            $hasLowerCase &&
            $hasDigits
        );
    }
}
