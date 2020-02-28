<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * Application manifest
 */
return [
    // Module meta
    'meta'     => [
        // Module title, required
        'title'       => _a('Message'),
        // Description, for admin, optional
        'description' => _a('A module to send message'),
        // Version number, required
        'version'     => '1.2.1',
        // Distribution license, required
        'license'     => 'New BSD',
        // Module is ready for clone? Default as false
        'clonable'    => false,
        'icon'        => 'fa-envelope',
    ],
    // Author information
    'author'   => [
        // Author full name, required
        'Dev'     => 'Xingyu Ji; Liu Chuang',
        // Email address, optional
        'Email'   => 'xingyu@eefocus.com',
        'UI/UE'   => '@zhangsimon, @loidco',
        'QA'      => 'Zhang Hua, @lavenderli',
        // Website link, optional
        'Website' => 'http://piengine.org',
        // Credits and aknowledgement, optional
        'Credits' => 'Zend Framework Team; Pi Engine Team; EEFOCUS Team.',
    ],
    // resource
    'resource' => [
        // Database meta
        'database'   => [
            // SQL schema/data file
            'sqlfile' => 'sql/mysql.sql',
        ],
        // permission
        'permission' => 'permission.php',
        // page
        'page'       => 'page.php',
        // Navigation definition
        'navigation' => 'navigation.php',
        // User specs
        'user'       => 'user.php',
        'block'      => 'block.php',

    ],
];
