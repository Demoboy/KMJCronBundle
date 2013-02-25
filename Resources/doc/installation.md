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