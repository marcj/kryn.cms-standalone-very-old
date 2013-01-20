Kryn.cms
========

A AJAX PHP enterprise open-source Content-Management-System and Content-Management-Framework with full RESTful API.

We're in development. This means, there are still a _lot_ of issues in this product (not even an Alpha).

More information and screenshots:
https://www.facebook.com/kryncms


Installation
------------

1. Extract the tar/zip-ball
2. Open the install.php through your browser


Features
--------

 - Propel ORM (MySQL, PostgreSQL)
 - Advanced, fast and fresh AJAX Backend Interface/RIA with mootools (yay!)
 - File abstraction layer (for mounts with external cloud storage), CDN
 - RESTful API - all actions are available through a RESTful JSON API
 - Smarty template engine (not required)
 - getText i18n (plural support etc) with translator - compatible .po files
 - High-Performance through several cache layers
 - Session storage through several layers (LoadBalanced support)
 - Easy to extend through a fancy extension editor
 - Framework CRUD window generator without coding
 - Easy and integrated backup system - perfect for live/dev-scenarios
 - Workspaces (in the works)
 - Several authenication layers (seperated in backend and several frontend)
 - MVC architecture
 - Very secure password storage (salted, double sha512 in 500 rounds, incl. non-db hash key injection)


Contribution
------------

Kryn.cms is a free open-source project for almost everyone. For web agencies, freelancers or hobbyists and has
a lot of features, therefore we need guys like you to keep up the quality.

Do you like Kryn.cms? Do you see potential in it? Then we'd love to see you contributing!
Because the product still in heavy development, we really can need everyone's help to create a awesome product.

No matter if you have just ideas or have feedback, please just drop your thoughts in the issue tracker above.
Or if you're a developer and want to contribute then please contact me. We really do love to meet any new
web freak. :-) (marc@kryn.org)


Since we're in highly development

PHPUnit
--------


 You have several environment variable to adjust the config in the test suite.

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
