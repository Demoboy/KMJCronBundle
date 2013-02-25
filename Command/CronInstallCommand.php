<?php

namespace KMJ\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use KMJ\CronBundle\Manager\CronManager;
use KMJ\CronBundle\Annotations\CronJob;
use KMJ\CronBundle\Manager\Cron;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of UpdateLocationsCron
 *
 * @author kaelinjacobson
 */
class CronInstallCommand extends ContainerAwareCommand {
    
    const CRON_BUNDLE_FINGERPRINT = "df150f";

    public function configure() {
        $this->setName('kmj:cron:install')
                ->setDescription('Installs cron commands to crontab')
                ->addOption('dump', null, InputOption::VALUE_NONE, 'If set crons are not written to the crontab but displayed');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $cronManager = new CronManager(); //crons are now loaded into memory and accessable through $->get();

        $reader = $this->getContainer()->get("annotation_reader");

        $output->writeln("<info>Installing commands to crontab</info>");

        if ($input->getOption('dump'))
            $output->writeln("<info>Dumping crons to screen</info>");

        foreach ($this->getApplication()->all() as $command) {
            $reflectionClass = new \ReflectionClass($command);

            foreach ($reader->getClassAnnotations($reflectionClass) as $annotations) {
                if ($annotations instanceof CronJob) {
                    //this cron has annotations of the cronjob class. Use this to create a crontab
                    $cron = new Cron();
                    $doNotAdd = false;

                    if (defined(PHP_BINARY)) {
                        $path = PHP_BINARY;
                    } else {
                        $path = "php";
                    }

                    $cron->setCommand("{$path} " . $this->getContainer()->get('kernel')->getRootDir() . '/console --env=' . $this->getContainer()->get('kernel')->getEnvironment() .' '. $command->getName())
                            ->setDayOfMonth($annotations->day)
                            ->setDayOfWeek($annotations->dayOfWeek)
                            ->setHour($annotations->hour)
                            ->setMinute($annotations->minute)
                            ->setMonth($annotations->month)
                            ->setComment($command->getDescription() . ' ' . self::CRON_BUNDLE_FINGERPRINT);

                    foreach ($cronManager->get() as $existingCron) {
                        if ($cron->equals($existingCron)) {
                            $doNotAdd = true;
                            break;
                        }
                    }

                    if ($input->getOption('dump'))
                        $output->writeln($cron);

                    if (!$doNotAdd && !$input->getOption('dump'))
                        $cronManager->add($cron);
                }
            }
        }



        $output->writeln("<info>Done!</info>");
    }

}

?>