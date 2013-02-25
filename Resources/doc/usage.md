KMJCronBundle Usage
================================

The KMJCronBundle only works with Symfony 2 commands [read more] (http://symfony.com/doc/master/components/console/introduction.html).

With your command include the following annotation at the top of your command class

<pre>

// ..
use KMJ\CronBundle\Annotations\CronJob;
/**
 * @CronJob(hour="0", minute="0")
 */
class ExampleCommand //.. {

   //..

}
</pre>

This annotation tells the KMJCronBundle that you want to run this command to execute at 12:00 AM

Since the annotation are installed to the crontab, the standard format for crons is used where * is wildcard.
Any time frame not provided is a wildcard. Slashes are also available however you must use / not \. The bundle converts this slash at installation.

So a command with the following annotation
<pre>
@CronJob(hour="1,4", minute="/5")
</pre>
Would run every five minutes at 1AM and 4AM.