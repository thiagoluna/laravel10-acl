<?php

namespace App\Dto\Users;

class CreateUserDto
{
    public function __construct(
        readonly public string $name,
        readonly public string $email,
        readonly public string $password
    )
    {
    }
}
