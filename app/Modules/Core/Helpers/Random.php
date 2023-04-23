<?php

namespace App\Modules\Core\Helpers;

class Random
{
    public static function generateNonce(int $length = 32): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $nonce = '';
        $maxRand = strlen($characters) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $nonce .= $characters[mt_rand(0, $maxRand)];
        }

        return $nonce;
    }
}
