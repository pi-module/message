<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    // route name
    'message' => array(
        'name' => 'message',
        'type' => 'Module\Message\Route\Message',
        'options' => array(
            'route' => '/message',
            'defaults' => array(
                'module' => 'message',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
    ),
);