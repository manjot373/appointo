<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'add:admin',
    description: 'Add a short description for your command',
)]
class AddAdminCommand extends Command
{
    private $em;
    private $hasher;
    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $email = $io->ask("Enter Email", null, function ($email) {
            if (empty($email)) {
                throw new \RuntimeException('Email is required.');
            }
        
            return $email;
        });
        $password = $io->ask("Enter Password",null, function ($password) {
            if (empty($password)) {
                throw new \RuntimeException('Password is required.');
            }
        
            return $password;
        });

        $a = new Admin();
        $a->setEmail($email)
        ->setPassword($this->hasher->hashPassword($a,$password))
        ->setRoles(['ROLE_ADMIN']);

        $this->em->persist($a);
        $this->em->flush();


        $io->success('Admin added successfully!');

        return Command::SUCCESS;
    }
}
