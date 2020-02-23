<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Message\Api;

use Pi;
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Set module link
        $moduleData = Pi::registry('module')->read($this->getModule());
        // Set index link
        if ($params['action'] == 'index') {
            $href = '';
        } else {
            $href = Pi::service('url')->assemble(
                'default', [
                'module' => $this->getModule(),
            ]
            );
        }
        // Set result
        $result   = [];
        $result[] = [
            'label' => $moduleData['title'],
            'href'  => $href,
        ];
        // Set module internal links
        switch ($params['action']) {
            case 'archive':
                $result[] = [
                    'label' => __('Archive'),
                ];
                break;

            case 'detail':
                $result[] = [
                    'label' => __('Message detail'),
                ];
                break;

            case 'send':
                $result[] = [
                    'label' => __('Send message'),
                ];
                break;
        }

        return $result;
    }
}
