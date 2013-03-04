Kryn.cms
========

A enterprise open-source Content-Management-System and Content-Management-Framework with a full RESTful API
written in PHP and JavaScript using Symfony Components, Mootools and other rock-solid libraries.

We're in development. This means, there are still a _lot_ of issues in this product (not even an Alpha) and it's not everything implemented yet.

[![Build Status](https://drone.io/marcj/Kryn.cms/status.png)](https://drone.io/marcj/Kryn.cms/latest)
[![Build Status](https://travis-ci.org/KrynLabs/Kryn.cms.png?branch=propel1.6)](https://travis-ci.org/KrynLabs/Kryn.cms)


Installation
------------

1. Extract the tar/zip-ball or `git clone https://github.com/KrynLabs/Kryn.cms.git`.
2. Download `composer` through `wget http://getcomposer.org/composer.phar`.
3. Install vendor libraries with `php composer.phar install`.
4. Setup a VirtualHost pointing to the `./web/` directory.
5. Open `domain.tld/install.php` in your browser and follow the instruction.


Features
--------

 - Based on Propel ORM (Propel supports MySQL, PostgreSQL, SQLite, MSSQL, and Oracle), http://www.propelorm.org
 - Advanced, fast and fresh administration interface (powered by mootools, yay!)
 - The administration API is completely abstracted through a RESTful JSON API
 - File abstraction layer (for mounts with external storages [s3, ftp, dropbox, etc]), CDN
 - Dynamic template engines, use the engine you want. (Smarty, Twig and plain PHP are shipped)
 - i18n using `getText` (with all of its features [e.g. including plural support, contexts]) compatible .po files
 - High-Performance through several cache layers 
 - Session storage through several layers (distributed sessions supported)
 - Easy to extend through a fancy extension editor, completely modulized
 - CRUD window generator, without writing one line of code
 - Easy and integrated backup system, perfect for live/dev-scenarios
 - Working in workspaces
 - Extremely detailed permission system
 - Ships with a solid bunch of UI input widgets
 - Several flexible authentication layers (e.g. changeable for administration, changeable per domain)
 - MVC architecture
 - Secure password storage using up-to-date encryptions
 - Observer pattern, truly extensible, http://symfony.com/doc/2.0/components/event_dispatcher/introduction.html
 - Symfony2 Routing Component, http://symfony.com/doc/2.0/components/routing/introduction.html
 - Symfony2 HttpKernel Component, http://symfony.com/doc/2.0/components/http_kernel/introduction.html
 - Monolog Framework, sends your logs to almost everywhere, https://github.com/Seldaek/monolog
 - CKEditor, inline editing and fancy WYSIWYG, http://ckeditor.com/

Screenshot
----------

![Administration Frontend Edit](https://raw.github.com/KrynLabs/Kryn.cms/propel1.6/docu/images/admin-frontend-edit.png)
![Administration File manager](https://raw.github.com/KrynLabs/Kryn.cms/propel1.6/docu/images/admin-files-context-image.png)
![Administration CRUD Framework Window](https://raw.github.com/KrynLabs/Kryn.cms/propel1.6/docu/images/admin-users.png)

[More Screenshots](https://github.com/KrynLabs/Kryn.cms/blob/propel1.6/docu/screenshots.markdown)

More information:
https://www.facebook.com/kryncms

Contribution
------------

Kryn.cms is a free open-source project for almost everyone. For web agencies, freelancers or hobbyists and has
a lot of features, where we need guys like you to maintain and enhance our quality!

Do you like Kryn.cms? Do you see potential in it? Then we'd love to see you contributing!
Because the product is still in early development, we really need everyone's help to create a awesome product.

No matter if you just got ideas or give us feedback, please just drop your thoughts in the issue tracker above.
If you're a developer and want to contribute then please contact me. We really do love to meet any new
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

    ./phpunit.phar
    DOMAIN=localhost PORT=80 ./phpunit.phar
    DOMAIN=localhost PORT=80 DB_PW='@#$TKKAFS' ./phpunit.phar test/Tests/Object/ApiTest.php

The test suite installs automatically Kryn.cms with the configuration above if `./config.php` does not exist.
Don't forget to run `composer install` first.
