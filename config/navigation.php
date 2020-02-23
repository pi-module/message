<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

return [
    // Hide from front menu
    'front' => false,
    // Admin side
    'admin' => [
        'list'  => [
            'label'      => _a('List'),
            'permission' => [
                'resource' => 'list',
            ],
            'route'      => 'admin',
            'module'     => 'message',
            'controller' => 'list',
            'action'     => 'index',
        ],
        'prune' => [
            'label'      => _a('Prune'),
            'permission' => [
                'resource' => 'prune',
            ],
            'route'      => 'admin',
            'module'     => 'message',
            'controller' => 'prune',
            'action'     => 'index',
        ],
    ],
];
