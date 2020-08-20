<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Gubler\ADSearchBundle\Test\Command;

use Gubler\ADSearchBundle\Command\CreateTestUserJsonCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateTestUserJsonCommandTest extends TestCase
{
    /**
     * @test
     */
    public function canExecuteCommand(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(['outputPath' => './']);

        self::assertFileExists('./test_users.json');

        $testUsers = json_decode(file_get_contents('./test_users.json'), true);

        self::assertEquals('Admin, System', $testUsers[0]['cn'][0]);

        unlink('./test_users.json');
    }

    /**
     * @param null|Application $application
     *
     * @return CommandTester
     */
    private function createCommandTester(?Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }

        $application->setAutoExit(false);

        $command = new CreateTestUserJsonCommand();
        $application->add($command);

        return new CommandTester($application->find('ad-search:create-user-json'));
    }
}
