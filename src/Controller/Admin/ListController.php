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

namespace Module\Message\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Zend\Db\Sql\Predicate\Expression;

class ListController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $page  = $this->params('page', 1);
        $list  = [];
        $limit = 50;
        // Set info
        $order  = ['time_send DESC', 'id DESC'];
        $offset = (int)($page - 1) * $limit;
        // Get info
        $select = $this->getModel('message')->select()->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('message')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            // markup content
            $list[$row->id]['content'] = Pi::service('markup')->compile(
                $row->content,
                'html',
                ['nl2br' => false]
            );
            // content Short
            $list[$row->id]['contentShort'] = (mb_strlen(strip_tags($row->content), 'utf-8') > 300) ? mb_substr(strip_tags($row->content), 0, 300, 'utf-8')
                . ' ... ' : strip_tags($row->content);
            // user from
            $list[$row->id]['userFrom'] = Pi::user()->getUser($row->uid_from);
            if ($list[$row->id]['userFrom']) {
                $list[$row->id]['userFrom'] = $list[$row->id]['userFrom']->toArray();
            }
            $list[$row->id]['userFrom']['avatar'] = Pi::user()->avatar(
                $row->uid_from, 'medium', [
                'alt'   => $list[$row->id]['userFrom']['name'],
                'class' => 'rounded-circle',
            ]
            );
            // user to
            $list[$row->id]['userTo'] = Pi::user()->getUser($row->uid_to);
            if ($list[$row->id]['userTo']) {
                $list[$row->id]['userTo'] = Pi::user()->getUser($row->uid_to)->toArray();
            }

            $list[$row->id]['userTo']['avatar'] = Pi::user()->avatar(
                $row->uid_to, 'medium', [
                'alt'   => $list[$row->id]['userTo']['name'],
                'class' => 'rounded-circle',
            ]
            );
            // Tiem send view
            $list[$row->id]['time_send_view'] = _date($row->time_send);
        }
        // Set paginator
        $columns   = ['count' => new Expression('count(*)')];
        $select    = $this->getModel('message')->select()->columns($columns);
        $count     = $this->getModel('message')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => 'list',
                        'action'     => 'index',
                    ]
                ),
            ]
        );
        // Set view
        $this->view()->setTemplate('list-index');
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
    }
}
