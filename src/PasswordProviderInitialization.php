<?php

namespace FormRelay\PasswordProvider;

use FormRelay\Core\Initialization;
use FormRelay\PasswordProvider\DataProvider\PasswordDataProvider;

class PasswordProviderInitialization extends Initialization
{
    public const DATA_PROVIDERS = [
        PasswordDataProvider::class,
    ];
}
