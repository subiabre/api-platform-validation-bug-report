<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data->getPlainPassword()) {
            $hash = $this->userPasswordHasher->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($hash);
            $data->eraseCredentials();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
