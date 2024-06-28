<?php

namespace App\Dto\Permissions;

class CreatePermissionDto
{
    public function __construct(
        readonly public string $name,
        readonly public string $description = '',
    )
    {
    }
}
