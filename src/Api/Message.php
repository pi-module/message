<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

namespace Module\Message\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Db\Sql\Predicate\Expression;

class Message extends AbstractApi
{
    /**
     * Show details of a message
     *
     * @param  int $messageId
     * @return array
     */
    public function showDetail($conversation, $userId, $checkAuth = true)
    {
        if($checkAuth){
            Pi::service('authentication')->requireLogin();

            // dismiss alert
            Pi::user()->message->dismissAlert($userId);
        }

        $model = Pi::model('message', 'message');
        //get private message
        $select = $model->select()
            ->where(function ($where) use ($conversation, $userId) {
                $subWhere = clone $where;
                $subWhere->equalTo('uid_from', $userId);
                $subWhere->or;
                $subWhere->equalTo('uid_to', $userId);
                $where->like('conversation', $conversation)
                    ->andPredicate($subWhere);
            });
        $rowset = $model->selectWith($select)->current();
        if (!$rowset) {
            return;
        }
        $detail = $rowset->toArray();

        // Get user
        if ($userId == $detail['uid_from']) {
            //get username url
            $user = Pi::user()->getUser($detail['uid_to'])
                ?: Pi::user()->getUser(0);
            $detail['name'] = $user->name;
        } else {
            //get username url
            $user = Pi::user()->getUser($detail['uid_from'])
                ?: Pi::user()->getUser(0);
            $detail['name'] = $user->name;
        }

        // Get avatar
        $detail['avatar'] = Pi::user()->avatar($detail['uid_from'], 'medium', array(
            'alt' => $user->name,
            'class' => 'img-circle',
        ));

        // Set profile Url
        $detail['profileUrl'] = Pi::user()->getUrl(
            'profile',
            $detail['uid_from']
        );

        //markup content
        $detail['content'] = Pi::service('markup')->render($detail['content'], 'html', 'html');

        if (!$detail['is_read_to'] && $userId == $detail['uid_to']) {
            //mark the message as read
            $model->update(array('is_read_to' => 1), array('conversation' => $conversation));
        }
        return $detail;
    }

    public function changeUid($oldId, $newId, $conversation)
    {
        $model = Pi::model('message', 'message');
        $model->update(array('uid_from' => $newId), array('uid_from' => $oldId, 'conversation' => $conversation));
        $model->update(array('uid_to' => $newId), array('uid_to' => $oldId, 'conversation' => $conversation));
        
        
    }
}