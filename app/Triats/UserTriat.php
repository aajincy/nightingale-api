<?php

namespace App\Triats;

/**
 * User Triat to Be reused  on Multiple Models                 
 */
trait UserTriat
{
    public function profile()
    {
        return $this->hasOne("App\Models\Profile");
    }
}
