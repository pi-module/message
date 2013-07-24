<?php
/**
 * Message module notify controller
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
 * @since           1.0
 * @package         Module\Message
 * @subpackage      Controller\Front
 */

namespace Module\Message\Controller\Front;

use Module\Message\Service;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Pi;

/**
 * Feature list:
 * 1. List of notifications
 * 2. Show details of a notification
 * 3. Mark the notifications as read
 * 4. Delete one or more notifications
 */
class NotifyController extends ActionController
{
    /**
     * List notifications
     */
    public function indexAction()
    {
        $page = $this->params('p', 1);
        $limit = Pi::config('list_number');
        $offset = (int) ($page - 1) * $limit;

        //current user id
        $userId = Pi::user()->getUser()->id;

        $model = $this->getModel('notification');
        //get notification list count
        $select = $model->select()
                        ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                        ->where(array('uid' => $userId, 'delete_status' => 0));
        $count = $model->selectWith($select)->current()->count;

        if ($count) {
            //get notification list
            $select = $model->select()
                            ->where(array('uid' => $userId, 'delete_status' => 0))
                            ->order('time_send DESC')
                            ->limit($limit)
                            ->offset($offset);
            $rowset = $model->selectWith($select);
            $notificationList = $rowset->toArray();
            if (empty($notificationList) && $page > 1) {
                $this->redirect()->toRoute('', array(
                    'controller' => 'notify',
                    'action'     => 'index',
                    'p'          => $page - 1,
                ));
                return;
            }

            //get admin name TODO
            $adminName = Pi::user()->getUser(1)->identity;
            //get admin avatar
            $admiinAvatar = Pi::user()->avatar(1)->get('small');

            $paginator = Paginator::factory(intval($count));
            $paginator->setItemCountPerPage($limit);
            $paginator->setCurrentPageNumber($page);
            $paginator->setUrlOptions(array(
                // Use router to build URL for each page
                'pageParam'     => 'p',
                'totalParam'    => 't',
                'router'        => $this->getEvent()->getRouter(),
                'route'         => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params'        => array(
                    'module'        => $this->getModule(),
                    'controller'    => 'notify',
                    'action'        => 'index',
                    //'role'          => $role,
                ),
            ));
            $this->view()->assign('paginator', $paginator);
        } else {
            $notificationList = array();
        }
        $this->view()->assign('notifications', $notificationList);
        $this->view()->assign('adminName', $adminName);
        $this->view()->assign('adminAvatar', $admiinAvatar);

        return;
    }

    /**
     * Notification detail
     */
    public function detailAction()
    {
        $notificationId = $this->params('mid', 0);
        //current user id
        $userId = Pi::user()->getUser()->id;

        $model = $this->getModel('notification');
        //get notification
        $select = $model->select()
                        ->where(array('id' => $notificationId, 'uid' => $userId));
        $rowset = $model->selectWith($select)->current();
        if (!$rowset) {
            return;
        }
        $detail = $rowset->toArray();

        $detail['username'] = Pi::user()->getUser(1)->identity;;//TODO
        //get admin avatar
        $detail['avatar'] = Pi::user()->avatar(1)->get('small');

        if ($detail['is_new']) {
            //mark the notification as read
            $model->update(array('is_new' => 0), array('id' => $notificationId));
        }

        $this->view()->assign('notification', $detail);
        return;
    }

    /**
     * Mark the notification as read
     */
    public function markAction()
    {
        $notificationIds = _get('ids',
                                'regexp',
                                array('regexp' => '/^[0-9,]+$/'));
        $page = $this->params('p', 1);
        //current user id
        $userId = Pi::user()->getUser()->id;
        if (empty($notificationIds)) {
            $this->redirect()->toRoute('', array(
                'controller' => 'notify',
                'action'     => 'index',
                'p'          => $page
            ));
        }
        $notificationIds = explode(',', $notificationIds);

        $model = $this->getModel('notification');
        $model->update(array('is_new' => 0), array(
            'id'  => $notificationIds,
            'uid' => $userId
        ));

        $this->redirect()->toRoute('', array(
            'controller' => 'notify',
            'action'     => 'index',
            'p'          => $page
        ));
    }

    /**
     * Delete notifications
     */
    public function deleteAction()
    {
        $notificationIds = _get('ids', 'regexp', array('regexp' => '/^[0-9,]+$/'));
        $page = $this->params('p', 1);

        if (strpos($notificationIds, ',')) {
            $notificationIds = explode(',', $notificationIds);
        }
        if (empty($notificationIds)) {
            $this->redirect()->toRoute('', array(
                'controller' => 'notify',
                'action'     => 'index',
                'p'          => $page
            ));
        }
        $userId = Pi::user()->getUser()->id;
        $model = $this->getModel('notification');
        $model->update(array('delete_status' => 1), array(
            'id'  => $notificationIds,
            'uid' => $userId
        ));

        $this->redirect()->toRoute('', array(
            'controller' => 'notify',
            'action'     => 'index',
            'p'          => $page
        ));
        return;
    }
}
