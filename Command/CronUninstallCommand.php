<?php

namespace KMJ\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use KMJ\CronBundle\Manager\CronManager;

/**
 * Description of UpdateLocationsCron
 *
 * @author kaelinjacobson
 */
class CronUninstallCommand extends ContainerAwareCommand {

    const CRON_BUNDLE_FINGERPRINT = "df150f";
    
    public function configure() {
        $this->setName('kmj:cron:uninstall')
                ->setDescription('Uninstalls cron commands to crontab');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $cronManager = new CronManager(); //crons are now loaded into memory and accessable through $->get();
        $reader = $this->getContainer()->get("annotation_reader");
        
        $output->writeln("<info>Uninstalling cron jobs from crontab</info>");
        
        foreach ($cronManager->get() as $key => $cron) {
            if (substr($cron->getComment(), -6) == self::CRON_BUNDLE_FINGERPRINT) {
                $cronManager->remove($key);
            }
        }
        
        $output->writeln("<info>Done!</info>");
    }

}

?>