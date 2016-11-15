# XMSecurityBundle
Extends the FOSUserBundle customizing and adding some functionality.

## Installation

### Step 1: Download the Bundle

**This package is not on composer, so the repository will need to be added manually in composer.json**

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ php composer.phar require xm/filter-bundle
```

This command requires [Composer](https://getcomposer.org/download/).

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new XM\SecurityBundle\XMSecurityBundle(),
        );

        // ...
    }
}
```
