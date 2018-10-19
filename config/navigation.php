<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

return array(
    // Hide from front menu
    'front' => false,
    // Admin side
    'admin' => array(
        'list' => array(
            'label' => _a('List'),
            'permission' => array(
                'resource' => 'list',
            ),
            'route' => 'admin',
            'module' => 'message',
            'controller' => 'list',
            'action' => 'index',
        ),
        'prune' => array(
            'label' => _a('Prune'),
            'permission' => array(
                'resource' => 'prune',
            ),
            'route' => 'admin',
            'module' => 'message',
            'controller' => 'prune',
            'action' => 'index',
        ),
    ),
);