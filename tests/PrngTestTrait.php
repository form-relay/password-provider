<?php

namespace FormRelay\PasswordProvider\Tests;

use FormRelay\PasswordProvider\Service\RandomNumberGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;

trait PrngTestTrait // extends \PHPUnit\Framework\TestCase
{
    // @phpstan-ignore-next-line -> splat operator seems to be incompatible with phpStan typehint check
    protected function createRngMock(array ...$calls): MockObject
    {
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $with = [];
        $return = [];
        foreach ($calls as $call) {
            $with[] = $call[0];
            $return[] = $call[1];
        }
        $rng->method('generate')
            ->withConsecutive(...$with)
            ->willReturnOnConsecutiveCalls(...$return);
        return $rng;
    }

    protected function createRngMockAlwaysReturningMinimum(): MockObject
    {
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $rng->method('generate')
            ->willReturnCallback(function (int $min, int $max) {
                return $min;
            });
        return $rng;
    }

    protected function createRngMockAlwaysReturningMaximum(): MockObject
    {
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $rng->method('generate')
            ->willReturnCallback(function (int $min, int $max) {
                return $max;
            });
        return $rng;
    }

    protected function createRngMockReturningAlternatingMinMax(bool $startWithMin = true): MockObject
    {
        $useMax = $startWithMin;
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $rng->method('generate')
            ->willReturnCallback(function (int $min, int $max) use (&$useMax) {
                $useMax = !$useMax;
                return $useMax ? $max : $min;
            });
        return $rng;
    }

    protected function createRngMockReturningAverage(): MockObject
    {
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $rng->method('generate')
            ->willReturnCallback(function (int $min, int $max) {
                return (int)floor(($min + $max) / 2);
            });
        return $rng;
    }

    protected function createRngMockWithSeed(int $seed = 123456789): MockObject
    {
        $firstCall = true;
        $rng = $this->createMock(RandomNumberGeneratorInterface::class);
        $rng->method('generate')
            ->willReturnCallback(function (int $min, int $max) use (&$firstCall, $seed) {
                if ($firstCall) {
                    mt_srand($seed);
                    $firstCall = false;
                }
                return mt_rand($min, $max);
            });
        return $rng;
    }

    /**
     * @return array<string, array<MockObject>>
     */
    public function rngMockProvider(): array
    {
        return [
            'prng min' => [$this->createRngMockAlwaysReturningMinimum()],
            'prng max' => [$this->createRngMockAlwaysReturningMaximum()],
            'prng alternating' => [$this->createRngMockReturningAlternatingMinMax()],
            'prng average' => [$this->createRngMockReturningAverage()],
            'prng default seed' => [$this->createRngMockWithSeed()],
            'prng seed 987654321' => [$this->createRngMockWithSeed(987654321)],
            'prng seed 42' => [$this->createRngMockWithSeed(42)],
            'prng seed 999' => [$this->createRngMockWithSeed(999)],
        ];
    }
}
