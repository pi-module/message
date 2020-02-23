<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * User profile and resource specs
 *
 * @see    Pi\Application\Installer\Resource\User
 * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 */
return [
    // Quicklinks
    'quicklink' => [
        'message' => [
            'title' => _a('Messages'),
            'link'  => Pi::service('url')->assemble(
                'default',
                ['module' => 'message']
            ),
            'icon'  => 'icon-bell',
        ],
    ],
];
