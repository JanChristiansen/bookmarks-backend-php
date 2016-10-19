<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:create')
            ->setDescription('Creates a new user with root category')
            ->setHelp("Creates a new user including root category")
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the user.')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Password of the user. If none given the command asks interactively.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getOption('password');
        if (strlen($password) == 0) {
            $password = $this->askForPassword($input, $output);
        }

        $userService = $this->getContainer()->get('app.service.user');
        $user = $userService->create($username, $password);

        $output->writeln('');
        $output->writeln("User with root category node created.");
        $output->writeln("Username: " . $user->getUsername());
        $output->writeln("ID: " . $user->getId());
        $output->writeln('');
        $output->writeln('You can now login with ' . $user->getUsername());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    protected function askForPassword(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(
            function ($value) {
                if (strlen($value) == 0) {
                    throw new \Exception('The password can not be empty');
                }

                return $value;
            }
        );

        return $helper->ask($input, $output, $question);
    }
}
