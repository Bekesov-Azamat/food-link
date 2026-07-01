<?php

namespace App\Services;

use Illuminate\Support\Str;

class ShortCodeGenerator
{
    public function generate(int $length = 6): string
    {
        return Str::random($length);
    }
}
