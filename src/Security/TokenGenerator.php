<?php

namespace App\Security;

/**
 * Class TokenGenerator
 */
class TokenGenerator
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZasbcdefghijklmnopqrstuvwxyz0123456789';

    public function getRandomSecureToken(int $length = 30): string
    {
        $token = '';
        $maxNumber = strlen(self::ALPHABET);

        for($i = 0; $i < $length; $i++) {
            try {
                $token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
            } catch (\Exception $e) {
            }
        }

        return $token;
    }
}