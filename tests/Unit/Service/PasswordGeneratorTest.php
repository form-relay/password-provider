<?php

namespace FormRelay\PasswordProvider\Tests\Unit\Service;

use FormRelay\PasswordProvider\Service\PasswordGenerator;
use FormRelay\PasswordProvider\Tests\PrngTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorTest extends TestCase
{
    use PrngTestTrait;

    /** @var PasswordGenerator */
    protected $subject;

    /**
     * @return  array<string, array<int, array<int, array<string, int|string>|int|string>|MockObject|string>>
     */
    public function passwordGenerationRngTestProvider(): array
    {
        return [
            'passwordLengthOneWithOneAlphabetWithOneCharacter' => [
                // RNG
                $this->createRngMock(
                    // determine length
                    [[1, 1], 1],
                    // index order generation
                    [[0, 0], 0],
                    // password character generation
                    [[0, 0], 0]
                ),
                // min/max length
                [1, 1],
                // alphabet options
                ['a'],
                // expected result
                'a'
            ],
            // TODO add more test cases
            //      - bigger alphabet
            //      - longer password
            //      - password with variable length
            //      - multiple alphabets
            //      - alphabets with minimum character requirements

            'variableLengthWithMultipleAlphabetsAndMinimumCharactersFromAlphabetAllRngMinimal' => [
                // RNG
                $this->createRngMockAlwaysReturningMinimum(),
                // min/max length
                [4, 6],
                // alphabet options
                [
                    'abc',
                    [
                        'alphabet' => 'def',
                        'min' => 2,
                    ],
                    'ghi'
                ],
                // expected result
                'ddaa'
            ],

            'variableLengthWithMultipleAlphabetsAndMinimumCharactersFromAlphabetAllRngMaximal' => [
                // RNG
                $this->createRngMockAlwaysReturningMaximum(),
                // min/max length
                [4, 6],
                // alphabet options
                [
                    'abc',
                    [
                        'alphabet' => 'def',
                        'min' => 2,
                    ],
                    'ghi'
                ],
                // expected result
                'iiiiff'
            ],

            'variableLengthWithMultipleAlphabetsAndMinimumCharactersFromAlphabetRngPseudoRandom' => [
                // RNG
                $this->createRngMock(
                    // determine length
                    [[4, 6], 5],
                    // index order generation
                    [[0, 4], 2],
                    [[0, 3], 3],
                    [[0, 2], 2],
                    [[0, 1], 1],
                    [[0, 0], 0],
                    // min characters from alphabet
                    [[0, 2], 2],
                    [[0, 2], 1],
                    // password character generation
                    [[0, 8], 5],
                    [[0, 8], 7],
                    [[0, 8], 1]
                ),
                // min/max length
                [4, 6],
                // alphabet options
                [
                    'abc',
                    [
                        'alphabet' => 'def',
                        'min' => 2,
                    ],
                    'ghi'
                ],
                // expected result
                'bhffe'
            ],
        ];
    }

    /**
     * @test
     * @dataProvider passwordGenerationRngTestProvider
     * @param MockObject $rng
     * @param array<int> $length
     * @param array<int, string> $alphabetOptions
     * @param string $expected
     * @return void
     */
    public function passwordGeneration(MockObject $rng, array $length, array $alphabetOptions, string $expected): void
    {
        $options = [
            'minLength' => $length[0],
            'maxLength' => $length[1],
            'alphabetOptions' => $alphabetOptions,
        ];
        $this->subject = new PasswordGenerator($rng);
        $result = $this->subject->generate($options);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider rngMockProvider
     * @param MockObject $rng
     */
    public function passwordLengthIsObeyed(MockObject $rng): void
    {
        $options = [
            'minLength' => 7,
            'maxLength' => 14,
        ];
        $this->subject = new PasswordGenerator($rng);
        $result = $this->subject->generate($options);
        $this->assertGreaterThanOrEqual(7, strlen($result));
        $this->assertLessThanOrEqual(14, strlen($result));
    }

    /**
     * @test
     * @dataProvider rngMockProvider
     * @param MockObject $rng
     */
    public function passwordContainsMinimumCharactersFromAlphabet(MockObject $rng): void
    {
        $options = [
            'minLength' => 10,
            'maxLength' => 10,
            'alphabetOptions' => [
                'abcdefghijklmnopqrstuvwxyz',
                [
                    'alphabet' => 'XY',
                    'min' => 5,
                ],
            ],
        ];
        $this->subject = new PasswordGenerator($rng);
        $result = $this->subject->generate($options);
        $this->assertEquals(1, preg_match('/^.*[XY].*[XY].*[XY].*[XY].*[XY].*$/', $result));
    }

    /**
     * @test
     * @dataProvider rngMockProvider
     * @param MockObject $rng
     */
    public function passwordContainsOnlyCharactersFromGivenAlphabets(MockObject $rng): void
    {
        $options = [
            'minLength' => 20,
            'maxLength' => 30,
            'alphabetOptions' => [
                'abcdef',
                'GHIJKL',
            ],
        ];
        $this->subject = new PasswordGenerator($rng);
        $result = $this->subject->generate($options);
        $this->assertEquals(1, preg_match('/^[abcdefGHIJKL]{20,30}$/', $result));
    }
}
