KMJUpdateBundle
================================


Welcome to the KMJUpdateBundle. The goal of this bundle is to provide an easy way to keep code bases the same across servers, a push once and it will get done approach.


1) Installation
----------------------------------

KMJUpdateBundle can conveniently be installed via Composer. Just add the following to your composer.json file:

<pre>
// composer.json
{
    // ...
    require: {
        // ..
        "kmj/updatemaster": "dev-master"

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
    new KMJ\UpdateBundle\KMJUpdateBundle(),
    // ...
);
</pre>



2) Usage
----------------------------------

The KMJUpdateBundle is called as console command only.

<pre>
app/console kmj:update:update
</pre>

Based on the configuration selected, this command will pull branch selected, 
either install composer.phar to the latest lock file or update it to the newest available version, and finally if the command is not running in 
the production environment, it will use the KMJSyncBundle and sync uploaded files and the database.


3) Configuration
----------------------------------

kmj_update:
  sync: true                #set to false if you do not want the database to sync or you do not have the KMJSyncBundle installed
  composer:
    shouldupdate: true      #set to false to have composer use the lock file to install dependencies
  git:
    remote: origin          #The remote name in the git config
    branch: develop         #The banch to pull from on the remote server