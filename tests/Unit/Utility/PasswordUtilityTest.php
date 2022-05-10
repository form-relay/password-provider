<?php

namespace FormRelay\PasswordProvider\Tests\Unit\Utility;

use FormRelay\PasswordProvider\Tests\PrngTestTrait;
use FormRelay\PasswordProvider\Utility\PasswordUtility;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordUtilityTest extends TestCase
{
    use PrngTestTrait;

    /**
     * @return array<string, array<int, array<int, string>|MockObject>>
     */
    public function shuffleArrayRngTestProvider(): array
    {
        return [
            'unchanged' => [
                $this->createRngMockAlwaysReturningMinimum(),
                ['a', 'b', 'c'],
                ['a', 'b', 'c'],
            ],
            'reversed' => [
                $this->createRngMockAlwaysReturningMaximum(),
                ['a', 'b', 'c'],
                ['c', 'b', 'a'],
            ],
            'pseudoRandomResult' => [
                $this->createRngMock(
                    [[0, 3], 2],
                    [[0, 2], 0],
                    [[0, 1], 1],
                    [[0, 0], 0]
                ),
                ['a', 'b', 'c', 'd'],
                ['c', 'a', 'd', 'b'],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider shuffleArrayRngTestProvider
     * @param MockObject $rng
     * @param array<int> $input
     * @param array<int> $expected
     */
    public function shuffleArray(MockObject $rng, array $input, array $expected): void
    {
        $result = PasswordUtility::shuffleArray($rng, $input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<string, array<int, MockObject|string>>
     */
    public function shuffleStringRngTestProvider(): array
    {
        return [
            'unchanged' => [
                $this->createRngMockAlwaysReturningMinimum(),
                'abc',
                'abc',
            ],
            'reversed' => [
                $this->createRngMockAlwaysReturningMaximum(),
                'abc',
                'cba',
            ],
            'pseudoRandomResult' => [
                $this->createRngMock(
                    [[0, 3], 2],
                    [[0, 2], 0],
                    [[0, 1], 1],
                    [[0, 0], 0]
                ),
                'abcd',
                'cadb',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider shuffleStringRngTestProvider
     * @param MockObject $rng
     * @param string $input
     * @param string $expected
     */
    public function shuffleString(MockObject $rng, string $input, string $expected): void
    {
        $result = PasswordUtility::shuffleString($rng, $input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<string, array<array|int|string>>
     */
    public function getRandomCharacterRgnTestProvider(): array
    {
        return [
            'firstCharacter' => [[0, 2], 0, 'abc', 'a'],
            'lastCharacter' => [[0, 2], 2, 'abc', 'c'],
            'oneCharAlphabet' => [[0, 0], 0, 'x', 'x'],
            'pseudoRandomChar' => [[0, 3], 2, 'abcd', 'c'],
        ];
    }

    /**
     * @test
     * @dataProvider getRandomCharacterRgnTestProvider
     * @param array<int> $rngWith
     * @param int $rngResult
     * @param string $alphabet
     * @param string $expected
     */
    public function getRandomCharacter(array $rngWith, int $rngResult, string $alphabet, string $expected): void
    {
        $rng = $this->createRngMock([$rngWith, $rngResult]);
        $result = PasswordUtility::getRandomCharacter($rng, $alphabet);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array<string, array<MockObject|int|string>>
     */
    public function generateRandomStringRngTestProvider(): array
    {
        return [
            'oneCharAlphabetAndOneLengthString' => [
                $this->createRngMock(
                    [[0, 0], 0]
                ),
                1,
                'a',
                'a'
            ],

            'oneCharAlphabetAndLongerString' => [
                $this->createRngMock(
                    [[0, 0], 0],
                    [[0, 0], 0],
                    [[0, 0], 0]
                ),
                3,
                'a',
                'aaa'
            ],
            'longerAlphabetAndOneCharString' => [
                $this->createRngMock(
                    [[0, 2], 1]
                ),
                1,
                'abc',
                'b'
            ],
            'pseudoRandomGeneratedString' => [
                $this->createRngMock(
                    [[0, 2], 1],
                    [[0, 2], 0],
                    [[0, 2], 2],
                    [[0, 2], 2]
                ),
                4,
                'abc',
                'bacc'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider generateRandomStringRngTestProvider
     * @param MockObject $rng
     * @param int $length
     * @param string $alphabet
     * @param string $expected
     */
    public function generateRandomString(MockObject $rng, int $length, string $alphabet, string $expected): void
    {
        $result = PasswordUtility::generateRandomString($rng, $length, $alphabet);
        $this->assertEquals($expected, $result);
    }
}
