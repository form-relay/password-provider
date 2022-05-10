<?php

namespace FormRelay\PasswordProvider\Service;

interface RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int;
}
