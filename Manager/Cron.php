<?php

namespace KMJ\CronBundle\Manager;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Cron represents a cron command. It holds:
 * - time data
 * - command
 * - comment
 * - log files
 * - cron execution status
 */
class Cron {

    /**
     * @var string
     */
    protected $minute = '*';

    /**
     * @var string
     */
    protected $hour = '*';

    /**
     * @var string
     */
    protected $dayOfMonth = '*';

    /**
     * @var string
     */
    protected $month = '*';

    /**
     * @var string
     */
    protected $dayOfWeek = '*';

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $logFile = null;

    /**
     * The size of the log file
     *
     * @var string
     */
    protected $logSize = null;

    /**
     * @var string
     */
    protected $errorFile = null;

    /**
     * The size of the error file
     *
     * @var string
     */
    protected $errorSize = null;

    /**
     * The last run time based on when log files have been written
     *
     * @var int
     */
    protected $lastRunTime = null;

    /**
     * The status of the cron, based on the log files
     *
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $comment;

    /**
     * Parses a cron line into a Cron instance
     *
     * TODO: this deserves a serious regex
     *
     * @static
     * @param $cron string The cron line
     * @return Cron
     */
    public static function parse($cron) {
        $parts = \explode(' ', $cron);
        $command = \implode(' ', \array_slice($parts, 5));

        // extract comment
        if (\strpos($command, '#')) {
            list($command, $comment) = \explode('#', $command);
            $comment = \trim($comment);
        }

        // extract error file
        if (\strpos($command, '2>')) {
            list($command, $errorFile) = \explode('2>', $command);
            $errorFile = \trim($errorFile);
        }

        // extract log file
        if (\strpos($command, '>')) {
            list($command, $logFile) = \explode('>', $command);
            $logFile = \trim($logFile);
        }

        // compute last run time, and file size
        $lastRunTime = null;
        $logSize = null;
        $errorSize = null;
        if (isset($logFile) && \file_exists($logFile)) {
            $lastRunTime = \filemtime($logFile);
            $logSize = \filesize($logFile);
        }
        if (isset($errorFile) && \file_exists($errorFile)) {
            $lastRunTime = \max($lastRunTime? : 0, \filemtime($errorFile));
            $errorSize = \filesize($errorFile);
        }

        // compute status
        $status = 'error';
        if ($logSize === null && $errorSize === null) {
            $status = 'unknown';
        } else if ($errorSize === null || $errorSize == 0) {
            $status = 'success';
        }

        // create cron instance
        $cron = new self();
        $cron->setMinute($parts[0]);
        $cron->setHour($parts[1]);
        $cron->setDayOfMonth($parts[2]);
        $cron->setMonth($parts[3]);
        $cron->setDayOfWeek($parts[4]);
        $cron->setCommand(\trim($command));
        $cron->setLastRunTime($lastRunTime);
        $cron->setLogSize($logSize);
        $cron->setErrorSize($errorSize);
        $cron->setStatus($status);

        if (isset($comment)) {
            $cron->setComment($comment);
        }
        if (isset($logFile)) {
            $cron->setLogFile($logFile);
        }
        if (isset($errorFile)) {
            $cron->setErrorFile($errorFile);
        }

        return $cron;
    }

    public function getMinute() {
        return $this->minute;
    }

    public function setMinute($minute) {
        $minute = str_replace("\\", "/", $minute);

        $this->minute = $minute;
        return $this;
    }

    public function getHour() {
        return $this->hour;
    }

    public function setHour($hour) {
        $hour = str_replace("\\", "/", $hour);

        $this->hour = $hour;
        return $this;
    }

    public function getDayOfMonth() {
        return $this->dayOfMonth;
    }

    public function setDayOfMonth($dayOfMonth) {
        $dayOfMonth = str_replace("\\", "/", $dayOfMonth);

        $this->dayOfMonth = $dayOfMonth;
        return $this;
    }

    public function getMonth() {
        return $this->month;
    }

    public function setMonth($month) {
        $month = str_replace("\\", "/", $month);

        $this->month = $month;
        return $this;
    }

    public function getDayOfWeek() {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek($dayOfWeek) {
        $dayOfWeek = str_replace("\\", "/", $dayOfWeek);

        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getCommand() {
        return $this->command;
    }

    public function setCommand($command) {
        $this->command = $command;
        return $this;
    }

    public function getLogFile() {
        return $this->logFile;
    }

    public function setLogFile($logFile) {
        $this->logFile = $logFile;
        return $this;
    }

    public function getLogSize() {
        return $this->logSize;
    }

    public function setLogSize($logSize) {
        $this->logSize = $logSize;
        return $this;
    }

    public function getErrorFile() {
        return $this->errorFile;
    }

    public function setErrorFile($errorFile) {
        $this->errorFile = $errorFile;
        return $this;
    }

    public function getErrorSize() {
        return $this->errorSize;
    }

    public function setErrorSize($errorSize) {
        $this->errorSize = $errorSize;
        return $this;
    }

    public function getLastRunTime() {
        return $this->lastRunTime;
    }

    public function setLastRunTime($lastRunTime) {
        $this->lastRunTime = $lastRunTime;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($comment) {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Concats time data to get the time expression
     *
     * @return string
     */
    public function getExpression() {
        return \sprintf('%s %s %s %s %s', $this->minute, $this->hour, $this->dayOfMonth, $this->month, $this->dayOfWeek);
    }

    /**
     * Transforms the cron instance into a cron line
     *
     * @return string
     */
    public function __toString() {
        $cronLine = $this->getExpression() . ' ' . $this->command;
        
        if ('' != $this->logFile) {
            $cronLine .= ' > ' . $this->logFile;
        }
        if ('' != $this->errorFile) {
            $cronLine .= ' 2> ' . $this->errorFile;
        }
        if ('' != $this->comment) {
            $cronLine .= ' #' . $this->comment;
        }
        return $cronLine;
    }

    public function equals(self $cron) {
        return ($cron->getCommand() == $this->getCommand() &&
                $cron->getMinute() == $this->getMinute() &&
                $cron->getHour() == $this->getHour() &&
                $cron->getDayOfMonth() == $this->getDayOfMonth() &&
                $cron->getDayOfWeek() == $this->getDayOfWeek() &&
                $cron->getMonth() == $this->getMonth());
    }

}