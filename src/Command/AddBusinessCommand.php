<?php

namespace App\Command;

use App\Entity\Business;
use App\Entity\BusinessUser;
use App\Repository\BusinessUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'add:business',
    description: 'Create a business and user or update password if user exists',
)]
class AddBusinessCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BusinessUserRepository $businessRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Ask email
        $email = $io->ask('Enter user email');

        $business = $this->businessRepository->findOneBy(['email' => $email]);

        // 2. If user exists → update password
        if ($business) {
            $io->warning('User already exists. Password will be updated.');

            $password = $io->askHidden('Enter new password');

            $hashed = $this->passwordHasher->hashPassword($business, $password);
            $business->setPassword($hashed);

            $this->em->flush();

            $io->success('Password updated successfully.');
        }
        // 3. Else create user
        else {
            $io->note('User not found. Creating new user.');

            $password = $io->askHidden('Enter password');
            $io->section('Business information');

            $name = $io->ask('Business name');
            $address = $io->ask('Business address');

            $business = new BusinessUser();
            $business->setEmail($email);

            $hashed = $this->passwordHasher->hashPassword($business, $password);
            $business->setPassword($hashed);

            $business->setUsername($name);
            $business->setRoles(['ROLE_BUSINESS','ROLE_ADMIN']);

           

            $io->success('User created successfully.');
        }

        // 4. Ask business data

 $this->em->persist($business);
        $this->em->flush();



        $io->success('Business created successfully.');

        return Command::SUCCESS;
    }
}
