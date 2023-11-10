<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class uniqueEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = $user = DB::table('users')
        ->where('email', $value)
        ->whereNull('deleted_at')
        ->first();
        if ($user) {
            $fail("The $attribute is duplicated.");
        }
    }
}
