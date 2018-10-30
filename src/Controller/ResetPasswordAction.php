<?php

namespace App\Controller;

use App\Entity\User;

class ResetPasswordAction
{
    public function __invoke(User $data)
    {
        return $data;
    }
}