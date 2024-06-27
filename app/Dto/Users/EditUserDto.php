<?php

namespace App\Dto\Users;

class EditUserDto
{
    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public ?string $password = null
    )
    {
    }
}
