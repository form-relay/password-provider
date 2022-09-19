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
    public const KEY_PASSWORDS = 'passwords';
    public const DEFAULT_PASSWORDS = [
        'password' => [
            'minLength' => 8,
            'maxLength' => 12,
            'alphabetOptions' => [],
        ],
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
        $passwords = $this->getConfig(static::KEY_PASSWORDS);
        foreach($passwords as $field => $generatorOptions) {
            $password = $this->passwordGenerator->generate(
                $generatorOptions
            );
            $submission->getContext()['passwords'][$field] = $password;
        }
    }

    protected function process(SubmissionInterface $submission): void
    {
        foreach($submission->getContext()['passwords'] as $field => $password) {
            $this->setField($submission, $field, $password);
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_PASSWORDS => static::DEFAULT_PASSWORDS,
        ];
    }
}
