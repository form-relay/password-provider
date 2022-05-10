<?php

namespace FormRelay\PasswordProvider\Service;

use FormRelay\Core\Exception\FormRelayException;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int
    {
        try {
            return random_int($min, $max);
        } catch (\Exception $e) {
            throw new FormRelayException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
