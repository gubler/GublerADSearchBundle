<?php

declare(strict_types=1);
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
     * @covers \Gubler\ADSearchBundle\Command\CreateTestUserJsonCommand
     */
    public function testCanExecuteCommand(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->execute(input: ['outputPath' => './']);

        self::assertFileExists(filename: './test_users.json');

        $testUsers = json_decode(
            json: file_get_contents(filename: './test_users.json') ?:
                throw new \DomainException(message: 'Unable to read file'),
            associative: true,
            depth: 512,
            flags: \JSON_THROW_ON_ERROR);

        self::assertEquals(expected: 'Admin, System', actual: $testUsers[0]['cn'][0]);

        unlink(filename: './test_users.json');
    }

    private function createCommandTester(): CommandTester
    {
        $application = new Application();

        $application->setAutoExit(boolean: false);

        $command = new CreateTestUserJsonCommand();
        $application->add(command: $command);

        return new CommandTester(command: $application->find(name: 'ad-search:create-user-json'));
    }
}
