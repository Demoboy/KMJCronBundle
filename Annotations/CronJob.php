<?php

namespace KMJ\CronBundle\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */

final class CronJob implements Annotation {
    public $month = "*";
    public $day = "*";
    public $hour = "*";
    public $minute = "*";
    public $dayOfWeek = "*";
}

?>