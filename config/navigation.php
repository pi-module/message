<?php
/**
 * Message module navigation config
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

return array(
    'front'      => array(
        'private' => array(
            'label'         => 'Private message',
            'route'         => 'default',
            'controller'    => 'index',
            'action'        => 'index',
        ),
        'notify' => array(
            'label'         => 'Notification',
            'route'         => 'default',
            'controller'    => 'notify',
            'action'        => 'index',
        ),
        'send' => array(
            'label'         => 'Send message',
            'route'         => 'default',
            'controller'    => 'index',
            'action'        => 'send',
        ),
    ),
);
