<?php

namespace KMJ\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Description of UpdateLocationsCron
 *
 * @author kaelinjacobson
 */
class CronUpdateCommand extends ContainerAwareCommand {

    public function configure() {
        $this->setName('kmj:cron:update')
                ->setDescription('Updates cron commands to crontab');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $uninstall = $this->getApplication()->find('kmj:cron:uninstall');
        $input = new ArrayInput(array(''));
        $uninstall->run($input, $output);
        
        $install = $this->getApplication()->find('kmj:cron:install');
        $input = new ArrayInput(array(''));
        $install->run($input, $output);
    }

}

?>