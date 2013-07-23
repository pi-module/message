<?php
/**
 * Message module inbox controller
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

use Pi\Mvc\Controller\ActionController;
use Module\Message\Form\SendForm;
use Module\Message\Form\SendFilter;
use Module\Message\Form\ReplyForm;
use Module\Message\Form\ReplyFilter;
use Module\Message\Service;
use Pi\Paginator\Paginator;
use Pi;

/**
 * Feature list:
 * 1. List of messages
 * 2. Show details of a message
 * 3. Reply a message
 * 4. Send a message
 * 5. Mark the messages as read
 * 6. Delete one or more messages
 */
class IndexController extends ActionController
{
    /**
     * List private messages
     */
    public function indexAction()
    {
        $page = $this->params('p', 1);
        $limit = Pi::config('list_number');
        $offset = (int) ($page - 1) * $limit;

        //current user id
        $userId = Pi::user()->getUser()->id;

        $model = $this->getModel('private_message');
        //get private message list count
        $select = $model->select()
                        ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                        ->where(function($where) use ($userId) {
                            $fromWhere = clone $where;
                            $toWhere = clone $where;
                            $fromWhere->equalTo('uid_from', $userId);
                            $fromWhere->equalTo('delete_status_from', 0);
                            $toWhere->equalTo('uid_to', $userId);
                            $toWhere->equalTo('delete_status_to', 0);
                            $where->andPredicate($fromWhere)
                                  ->orPredicate($toWhere);
                        });
        $count = $model->selectWith($select)->current()->count;

        if ($count) {
            //get private message list group by user
            $select = $model->select()
                            ->where(function($where) use ($userId) {
                                $fromWhere = clone $where;
                                $toWhere = clone $where;
                                $fromWhere->equalTo('uid_from', $userId);
                                $fromWhere->equalTo('delete_status_from', 0);
                                $toWhere->equalTo('uid_to', $userId);
                                $toWhere->equalTo('delete_status_to', 0);
                                $where->andPredicate($fromWhere)
                                      ->orPredicate($toWhere);
                            })
                            ->order('time_send DESC')
                            ->limit($limit)
                            ->offset($offset);
            $rowset = $model->selectWith($select);
            if (!$rowset && $page > 1) {
                $this->redirect()->toRoute('', array(
                    'controller' => 'index',
                    'action'     => 'index',
                    'p'          => $page - 1,
                ));
                return;
            }
            $messageList = $rowset->toArray();

            array_walk($messageList, function(&$v, $k) use($userId){
                //format messages
                $v['content'] = Service::messageSummary($v['content']);

                if ($userId == $v['uid_from']) {
                    $v['is_new'] = 0;
                    $v['username'] = __('Sent to ')
                                   . Pi::user()->getUser($v['uid_to'])->identity;
                    //get avatar
                    $v['avatar'] = Pi::user()->avatar($v['uid_to'])->get('small');
                } else {
                    $v['is_new'] = $v['is_new_to'];
                    $v['username'] = Pi::user()->getUser($v['uid_from'])->identity;//TODO
                    //get avatar
                    $v['avatar'] = Pi::user()->avatar($v['uid_from'])->get('small');
                }

                unset($v['is_new_from'], $v['is_new_to'], $v['delete_status_from'], $v['delete_status_to']);
            });

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
                    'controller'    => 'index',
                    'action'        => 'index',
                    //'role'          => $role,
                ),
            ));
            $this->view()->assign('paginator', $paginator);
        } else {
            $messageList = array();
        }
        $this->view()->assign('messages', $messageList);
        return;
    }

    /**
     * Send a private message
     */
    public function sendAction()
    {
        $form = $this->getSendForm('send');
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            $form->setInputFilter(new SendFilter);
            if (!$form->isValid()) {
                $this->renderSendForm($form);
                return;
            }
            $data   = $form->getData();
            //check username
            $toUserId = Pi::user()->getUser($data['username'], 'identity')->id;
            if (!$toUserId) {
                $this->view()->assign('errMessage', __('Username is invalid, please try again.'));
                $this->renderSendForm($form);
                return;
            }

            //current user id
            $userId = Pi::user()->getUser()->id;
            $result = Pi::service('api')->message->send($toUserId, $data['content'], $userId);
            if (!$result) {
                $this->view()->assign('errMessage', __('Send failed, please try again.'));
                $this->renderSendForm($form);
                return;
            }

            $this->redirect()->toRoute('', array('controller' => 'index', 'action' => 'index'));
            return;
        }
        $this->renderSendForm($form);
    }

    /**
     * Initialize send form instance
     *
     * @param string $name
     * @return SendForm
     */
    protected function getSendForm($name)
    {
        $form = new SendForm($name);
        $form->setAttribute('action', $this->url('', array('action' => 'send')));

        return $form;
    }

    /**
     * Render send form
     *
     * @param SendForm $form
     * @return void
     */
    protected function renderSendForm($form)
    {
        $this->view()->assign('title', __('Send message'));
        $this->view()->assign('form', $form);
    }

    /**
     * Message detail and reply message
     */
    public function detailAction()
    {
        $messageId = $this->params('mid', 0);
        //current user id
        $userId = Pi::user()->getUser()->id;

        $form = new ReplyForm('reply');
        $form->setAttribute('action', $this->url('', array(
            'action' => 'detail',
            'mid' => $messageId,
        )));
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            $form->setInputFilter(new ReplyFilter);
            if (!$form->isValid()) {
                $this->view()->assign('form', $form);
                $this->showDetail($messageId);
                return;
            }
            $data = $form->getData();

            $result = Pi::service('api')->message->send($data['uid_to'],
                                                        $data['content'],
                                                        $userId);
            if (!$result) {
                $this->view()->assign('errMessage', __('Send failed, please try again.'));
                $this->view()->assign('form', $form);
                $this->showDetail($messageId);
                return;
            }

            $this->redirect()->toRoute('', array('controller' => 'index', 'action' => 'index'));
            return;
        } else {
            $detail = $this->showDetail($messageId);
            $toId = $userId == $detail['uid_from'] ? $detail['uid_to'] : $detail['uid_from'];
            $form->setData(array('uid_to' => $toId));
            $this->view()->assign('form', $form);
        }
    }

    /**
     * Show details of a message
     *
     * @param int $messageId
     * @return array
     */
    protected function showDetail($messageId)
    {
        //current user id
        $userId = Pi::user()->getUser()->id;

        $model = $this->getModel('private_message');
        //get private message
        $select = $model->select()
                        ->where(function($where) use ($messageId, $userId) {
                            $subWhere = clone $where;
                            $subWhere->equalTo('uid_from', $userId);
                            $subWhere->or;
                            $subWhere->equalTo('uid_to', $userId);
                            $where->equalTo('id', $messageId)->andPredicate($subWhere);
                        });
        $rowset = $model->selectWith($select)->current();
        if (!$rowset) {
            return;
        }
        $detail = $rowset->toArray();

        if ($userId == $detail['uid_from']) {
            //get avatar
            $detail['avatar'] = Pi::user()->avatar($detail['uid_to'])->get('small');
            $detail['username'] = __('Sent to ')
                                . Pi::user()->getUser($detail['uid_to'])->identity;
        } else {
            //get avatar
            $detail['avatar'] = Pi::user()->avatar($detail['uid_from'])->get('small');
            $detail['username'] = Pi::user()->getUser($detail['uid_from'])->identity;
        }

        if ($detail['is_new_to']) {
            //mark the message as read
            $model->update(array('is_new_to' => 0), array('id' => $messageId));
        }

        $this->view()->assign('myAvatar', Pi::user()->avatar()->get('small'));
        $this->view()->assign('message', $detail);
        return $detail;
    }

    /**
     * Mark the message as read
     */
    public function markAction()
    {
        $messageIds = _get('ids', 'regexp', array('regexp' => '/^[0-9,]+$/'));
        $page = $this->params('p', 1);
        //current user id
        $userId = Pi::user()->getUser()->id;
        if (empty($messageIds)) {
            $this->redirect()->toRoute('', array(
                'controller' => 'index',
                'action'     => 'index',
                'p'          => $page
            ));
        }
        $messageIds = explode(',', $messageIds);

        $model = $this->getModel('private_message');
        $result = $model->update(array('is_new_to' => 0),
                                 function($where) use ($userId, $messageIds) {
            $subWhere = clone $where;
            $subWhere->equalTo('uid_from', $userId)
                     ->or
                     ->equalTo('uid_to', $userId);
            $where->in('id', $messageIds)->andPredicate($subWhere);
        });

        $this->redirect()->toRoute('', array(
            'controller' => 'index',
            'action'     => 'index',
            'p'          => $page
        ));
    }

    /**
     * Delete messages
     */
    public function deleteAction()
    {
        $messageIds = _get('ids', 'regexp', array('regexp' => '/^[0-9,]+$/'));
        $toId = $this->params('tid', 0);
        $page = $this->params('p', 1);

        if (strpos($messageIds, ',')) {
            $messageIds = explode(',', $messageIds);
        }
        if (empty($messageIds)) {
            $this->redirect()->toRoute('', array(
                'controller' => 'index',
                'action'     => 'index',
                'p'          => $page
            ));
        }
        $userId = Pi::user()->getUser()->id;
        $model = $this->getModel('private_message');

        if ($toId) {
            if ($userId == $toId) {
                $model->update(array('delete_status_to' => 1), array(
                    'id'     => $messageIds,
                    'uid_to' => $userId
                ));
            } else {
                $model->update(array('delete_status_from' => 1), array(
                    'id'       => $messageIds,
                    'uid_from' => $userId
                ));
            }
        } else {
            $model->update(array('delete_status_from' => 1), array(
                'uid_from' => $userId,
                'id'       => $messageIds
            ));
            $model->update(array('delete_status_to' => 1), array(
                'uid_to' => $userId,
                'id'     => $messageIds
            ));
        }

        $this->redirect()->toRoute('', array(
            'controller' => 'index',
            'action'     => 'index',
            'p'          => $page
        ));
        return;
    }
}
