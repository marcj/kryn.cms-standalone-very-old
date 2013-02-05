Kryn.cms
========

A enterprise open-source Content-Management-System and Content-Management-Framework with a full RESTful API
written in PHP and mootools.

We're in development. This means, there are still a _lot_ of issues in this product (not even an Alpha) and it's not anything implemented yet.

[![Build Status](https://drone.io/marcj/Kryn.cms/status.png)](https://drone.io/marcj/Kryn.cms/latest)
[![Build Status](https://travis-ci.org/KrynLabs/Kryn.cms.png?branch=propel1.6)](https://travis-ci.org/KrynLabs/Kryn.cms)


More information and screenshots:
https://www.facebook.com/kryncms


Installation
------------

1. Extract the tar/zip-ball
2. get `composer` through `wget http://getcomposer.org/composer.phar`
3. Install vendor libraries with `php composer.phar install`
4. Open `install.php` through your browser


Features
--------

 - Based on Propel ORM (Propel supports MySQL, PostgreSQL, SQLite, MSSQL, and Oracle), http://www.propelorm.org
 - Advanced, fast and fresh Administration Interface (powered by mootools, yay!)
 - The Administration API is completely abstracted through a RESTful JSON API
 - File abstraction layer (for mounts with external cloud storage), CDN
 - Dynamic template engines, use the engine you want. (Smarty and Twig are shipped per default)
 - getText i18n (plural support etc) with translator, compatible .po files
 - High-Performance through several cache layers
 - Session storage through several layers (LoadBalanced support)
 - Easy to extend through a fancy extension editor, completely modulized
 - Framework CRUD window generator, without coding
 - Easy and integrated backup system, perfect for live/dev-scenarios
 - Working in workspaces
 - Extremely detailed permission system
 - Comes with a solid bunch of UI input widgets
 - Several authentication layers (changeable for administration and/or per domain)
 - MVC architecture, no HTML inside PHP
 - Very secure password storage (salted, double sha512 in 500 rounds, incl. non-db key injection)
 - Observer pattern, truly extensible, http://symfony.com/doc/2.0/components/event_dispatcher/introduction.html
 - Symfony2 Routing Component, http://symfony.com/doc/2.0/components/routing/introduction.html
 - Symfony2 HttpKernel Component, http://symfony.com/doc/2.0/components/http_kernel/introduction.html
 - Monolog Logger, sends your logs to almost everywhere, https://github.com/Seldaek/monolog
 - CKEditor, inline editing and fancy WYSIWYG, http://ckeditor.com/

Screenshot
----------

![Administration Screenshot](https://raw.github.com/KrynLabs/Kryn.cms/propel1.6/docu/images/admin-browser-screenshot.png)

More Screenshots

Contribution
------------

Kryn.cms is a free open-source project for almost everyone. For web agencies, freelancers or hobbyists and has
a lot of features, where we need guys like you to keep up the quality.

Do you like Kryn.cms? Do you see potential in it? Then we'd love to see you contributing!
Because the product is still in heavy development, we really can need everyone's help to create a awesome product.

No matter if you have just ideas or have feedback, please just drop your thoughts in the issue tracker above.
Or if you're a developer and want to contribute then please contact me. We really do love to meet any new
web freak. :-) (marc@kryn.org)


PHPUnit
--------


 You have several environment variables to adjust the config in the test suite.

    DOMAIN     The domain the installation (should) have. Should be available through your network. Default is `127.0.0.1`
    PORT       The port the installation (should) have. Default is `8000`.
    DB_NAME    The database name. Default is `test`
    DB_USER    The database username. Default is `root`
    DB_PW      The database password. Default is empty.
    DB_TYPE    The database type. `mysql`, `pgsql`, `sqlite`, `sqlsrv`. Default is `mysql`.
    DB_SERVER  The database server address. Default is `127.0.0.1`

Examples:

    DOMAIN=ilee PORT=80 ./phpunit.phar test/Tests/REST/BasicTest.php
    DOMAIN=localhost PORT=80 ./phpunit.phar test/
    DOMAIN=localhost PORT=80 DB_PW='@#$TKKAFS' ./phpunit.phar test/

The test suite installs automatically Kryn.cms with the credentials above if `./config.php` does not exist.
Don't forget to run `composer install` first.
