KMJCronBundle
================================


Welcome to the KMJCronBundle. The goal of this bundle is to provide an easy way to manage crons. 
It accomplishes this goal by searching for all Symfony 2 commands using the @CronJob annotation.
When found, the annotation is broken down and installed to the current user's crontab. Currently this bundle only supports linux servers. There is
a know issue where if the server is running cPanel, the crontab cannot be updated. Instead list it manually and copy them to the crontab.


1) Installation
----------------------------------

KMJCronBundle can conveniently be installed via Composer. Just add the following to your composer.json file:

<pre>
// composer.json
{
    // ...
    require: {
        // ..
        "kmj/cronbundle": "dev-master"
    }
}
</pre>


Then, you can install the new dependencies by running Composer's update command from the directory where your composer.json file is located:

<pre>
    php composer.phar update
</pre>


Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your AppKernel.php file, and register the new bundle:

<pre>
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new KMJ\CronBundle\KMJCronBundle(),
    // ...
);
</pre>



2) Usage
----------------------------------

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
@CronJob(hour="1,4", minute="/5", env="prod")
</pre>
Would run every five minutes at 1AM and 4AM only in the production environment.