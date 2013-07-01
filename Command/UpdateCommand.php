<?php

namespace KMJ\UpdateBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

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
        //get the current of the app
        $kernel = $this->getContainer()->get("kernel");
        $appPath = $kernel->getRootDir() . "/../";
        $updateService = $this->getContainer()->get("updater");
        
        $output->writeln("cd {$appPath} && git pull {$updateService->getGitRemote()} {$updateService->getGitBranch()}");
        die();

        $pullProcess = new \Symfony\Component\Process\Process("cd {$appPath} && git pull {$updateService->getGitRemote()} {$updateService->getGitBranch()}");

        $pullProcess->run(function ($type, $buffer) use (&$output) {
                    if (Process::ERR !== $type) {
                        $output->writeln($buffer);
                    }
                });

        $composerCommand = "cd {$appPath} && composer.phar ";

        if ($updateService->composerShouldUpdate()) {
            $composerCommand .= "update";
        } else {
            $composerCommand .= "install";
        }

        $composerProcess = new \Symfony\Component\Process\Process($composerCommand);

        $composerProcess->run(function ($type, $buffer) use (&$output) {
                    if (Process::ERR !== $type) {
                        $output->writeln($buffer);
                    }
                });
                
        return;

        if ($kernel->getEnvironment() != "prod") {
            if ($updateService->shouldSync()) {
                $syncProcess = new Process("cd {$appPath} && app/console kmj:sync:sync");

                $syncProcess->run(function ($type, $buffer) use (&$output) {
                            if (Process::ERR !== $type) {
                                $output->writeln($buffer);
                            }
                        });
            }
        }
    }

}