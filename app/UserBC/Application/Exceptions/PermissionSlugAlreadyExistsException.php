<?php

declare(strict_types=1);

namespace App\UserBC\Application\Exceptions;

use App\SharedKernel\Application\Exception\ApplicationException;

final class PermissionSlugAlreadyExistsException extends ApplicationException
{
    public function __construct()
    {
        parent::__construct('El slug del permiso ya existe');
    }
}
