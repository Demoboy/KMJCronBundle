<?php

namespace KMJ\UpdateBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * Description of UpdateLocationsCron
 *
 * @author kaelinjacobson
 */
class UpdateCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('kmj:update:update')
                ->setDescription('Updates the code base to the current version as found in the repository');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $kernel = $this->getContainer()->get("kernel");
        $appPath = $kernel->getRootDir() . "/../";
        $updateService = $this->getContainer()->get("updater");

        $output->writeln("Executing git pull");

        $pullProcess = new Process("cd {$appPath} && git pull {$updateService->getGitRemote()} {$updateService->getGitBranch()}");
        $pullProcess->setTimeout(3600);
        $pullProcess->run(function ($type, $buffer) use (&$output) {
                    if (Process::ERR !== $type) {
                        $output->write("<info>".$buffer);
                    }
                });

        $composerCommand = "cd {$appPath} && composer.phar ";

        if ($updateService->composerShouldUpdate()) {
            $composerCommand .= "update";
            $output->writeln("Executing composer update");
        } else {
            $composerCommand .= "install";
            $output->writeln("Running composer install");
        }

        $composerProcess = new Process($composerCommand);
        $composerProcess->setTimeout(3600);

        $composerProcess->run(function ($type, $buffer) use (&$output) {
                    if (Process::ERR !== $type) {
                        $output->write("<info>".$buffer);
                    }
                });

        if ($kernel->getEnvironment() != "prod") {
            if ($updateService->shouldSync()) {
                $output->writeln("Syncing database and uploaded files");

                $syncProcess = new Process("cd {$appPath} && app/console kmj:sync:sync --env='{$kernel->getEnvironment()}'");
                $syncProcess->setTimeout(3600);
                $syncProcess->run(function ($type, $buffer) use (&$output) {
                            if (Process::ERR !== $type) {
                                $output->write("<info>".$buffer);
                            }
                        });
            }
        }
    }

}