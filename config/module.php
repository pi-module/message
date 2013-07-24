<?php
/**
 * Message module config
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Xingyu Ji <xingyu@eefocus.com>
 * @package         Module\Message
 */

/**
 * Application manifest
 */
return array(
    // Module meta
    'meta'  => array(
        // Module title, required
        'title'         => __('Message'),
        // Description, for admin, optional
        'description'   => __('A module to send message'),
        // Version number, required
        'version'       => '1.0',
        // Distribution license, required
        'license'       => 'New BSD',
        // Module is ready for clone? Default as false
        'clonable'      => false,
    ),
    // Author information
    'author'    => array(
        // Author full name, required
        'name'      => 'Xingyu Ji',
        // Email address, optional
        'email'     => 'xingyu@eefocus.com',
        // Website link, optional
        'website'   => 'http://www.xoopsengine.org',
        // Credits and aknowledgement, optional
        'credits'   => 'Zend Framework Team; Pi Engine Team; EEFOCUS Team.'
    ),
    // Maintenance actions
    'maintenance'   => array(
        // resource
        'resource' => array(
            // Database meta
            'database'  => array(
                // SQL schema/data file
                'sqlfile'   => 'sql/mysql.sql',
                // Tables to be removed during uninstall, optional
                'schema'    => array(
                    'private_message'      => 'table',
                    'notification'         => 'table',
                ),
            ),
            // Navigation definition
            'navigation'    => 'navigation.php',
        ),
    ),
);