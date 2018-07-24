# H5PBundle
Bundle to integrate H5P into Symfony. This bundle is a port of the H5P Drupal module. For more info about H5P see [H5P.org](https://h5p.org)

This bundle was tested on Symfony 3.4

Installation
------------

Install with composer
``` bash
composer require emmedy/h5p-bundle
```

Enable the bundle in `AppKernel.php`
``` php
$bundles = array(
    // ...
    new Emmedy\H5PBundle\EmmedyH5PBundle(),
)
```

Add the H5P assets to the bundle
``` bash
php app/console h5p-bundle:include-assets
php app/console assets:install --symlink
```

Add required tables and relations to the database
``` bash
php app/console doctrine:schema:update --force 
```

Enable the routing in `routing.yml`
``` yaml
emmedy_h5p.demo:
    resource: "@EmmedyH5PBundle/Resources/config/routing_demo.yml"
    prefix:   /

emmedy_h5p:
    resource: "@EmmedyH5PBundle/Resources/config/routing.yml"
    prefix:   /
```

emmedy_h5p.demo is optional. It can be used as an example how to use H5P within Symfony and test if this bundle is working properly.

Configuration
-------------

Configure the bundle in `config.yml`. (Watch for the underscore between h5 and p)
``` yml
emmedy_h5_p:
    use_permission: true # This is false by default to let the demo work out of the box.
    storage_dir: h5p # Location to store all H5P libraries and files
    web_dir: web # Location of the public web directory
```
For all configurations see [Configuration.php](DependencyInjection/Configuration.php)

Usage
-------------

First add a virtual host that points to you project. Then in your browser go to `http://<your virtualhost>/h5p/list`

Todo
-------------

Not everything is ported yet. The following things still need to be done:
* Upload library. Currently only H5P default libraries can be selected from Hub.
* Download package
* Embed package
* Store usage data and points
