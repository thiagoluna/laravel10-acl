<?php

namespace App\Dto\Permissions;

class EditPermissionDto
{
    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public string $description
    )
    {
    }
}
