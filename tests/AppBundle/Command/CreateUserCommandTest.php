<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\CreateUserCommand;
use AppBundle\DataFixtures\ORM\LoadUsersData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends WebTestCase
{
    /**
     * @var CreateUserCommand
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function setUp()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $application->add(new CreateUserCommand());

        $this->command = $application->find('app:user:create');
        $this->commandTester = new CommandTester($this->command);

        $this->loadFixtures(array(LoadUsersData::class));
    }

    public function testExecuteAskForPassword()
    {
        $helper = $this->command->getHelper('question');
        $helper->setInputStream($this->getInputStream("Test\n"));

        $this->commandTester->execute(
            array(
                'command' => $this->command->getName(),
                'username' => 'atr',
            )
        );

        $output = $this->commandTester->getDisplay();
        $this->assertContains('Please enter password: ', $output);
        $this->assertContains('User with root category node created.', $output);
        $this->assertContains('Username: atr', $output);
        $this->assertContains('ID: ', $output);
        $this->assertContains('You can now login with atr', $output);
    }

    public function testExecuteWithPasswordOption()
    {
        $this->commandTester->execute(
            array(
                'command' => $this->command->getName(),
                'username' => 'atr',
                '--password' => 'secret',
            )
        );

        $output = $this->commandTester->getDisplay();
        $this->assertNotContains('Please enter password: ', $output);
        $this->assertContains('User with root category node created.', $output);
        $this->assertContains('Username: atr', $output);
        $this->assertContains('ID: ', $output);
        $this->assertContains('You can now login with atr', $output);
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}