<?php

namespace FormRelay\PasswordProvider\DataProvider;

use FormRelay\Core\DataProvider\DataProvider;
use FormRelay\Core\Log\LoggerInterface;
use FormRelay\Core\Model\Submission\SubmissionInterface;
use FormRelay\Core\Request\RequestInterface;
use FormRelay\Core\Service\ClassRegistryInterface;
use FormRelay\PasswordProvider\Service\PasswordGenerator;
use FormRelay\PasswordProvider\Service\PasswordGeneratorInterface;

class PasswordDataProvider extends DataProvider
{
    public const KEY_FIELD = 'field';
    public const DEFAULT_FIELD = 'password';

    public const KEY_GENERATOR_OPTIONS = 'generator';
    public const DEFAULT_GENERATOR_OPTIONS = [
        'minLength' => 8,
        'maxLength' => 12,
        'alphabetOptions' => [],
    ];

    /** @var PasswordGeneratorInterface */
    protected $passwordGenerator;

    public function __construct(ClassRegistryInterface $registry, LoggerInterface $logger, ?PasswordGeneratorInterface $passwordGenerator = null)
    {
        parent::__construct($registry, $logger);
        $this->passwordGenerator = $passwordGenerator ?? new PasswordGenerator();
    }

    protected function processContext(SubmissionInterface $submission, RequestInterface $request): void
    {
        $password = $this->passwordGenerator->generate(
            $this->getConfig(static::KEY_GENERATOR_OPTIONS)
        );
        $submission->getContext()['password'] = $password;
    }

    protected function process(SubmissionInterface $submission): void
    {
        $this->setFieldFromContext($submission, 'password', $this->getConfig(static::KEY_FIELD));
    }

    /**
     * @return array<string, string>
     */
    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_FIELD => static::DEFAULT_FIELD,
            static::KEY_GENERATOR_OPTIONS => static::DEFAULT_GENERATOR_OPTIONS,
        ];
    }
}
