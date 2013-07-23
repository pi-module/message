<?php
/**
 * Message module message api for global
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
 */

namespace Module\Message\Api;

use Pi;
use Pi\Application\AbstractApi;

/**
 * Api list:
 * 1. Send a message
 * 2. Send a notification
 * 3. Get total count
 * 4. Get new message count to alert
 */
class Api extends AbstractApi
{
    /**
     * The number of records each insertion
     *
     * @var int
     */
    protected static $batchInsertLen = 1000;

    /**
     * Send a message
     *
     * @param int $to
     * @param string $message
     * @param int $from
     * @return bool
     */
    public function send($to, $message, $from)
    {
        $model  = Pi::model('private_message', $this->getModule());
        $privateMessage = array(
            'uid_from'   => $from,
            'uid_to'     => $to,
            'content'    => $message,
            'time_send'  => time(),
        );
        $row = $model->createRow($privateMessage);
        $result = $row->save();
        if ($result) {
            //audit log
            $args = array(
                'from:' . Pi::user()->getUser($from)->identity,//TODO
                'to:' . Pi::user()->getUser($from)->identity,
                $message,
            );
            Pi::service('audit')->log('message', $args);
        }
        return $result;
    }

    /**
     * Send a notification
     *
     * @param int|array $to
     * @param string $message
     * @param string $subject
     * @param string $tag
     * @return int|false
     */
    public function notify($to, $message, $subject, $tag = '')
    {
        $model  = Pi::model('notification', $this->getModule());
        if (is_numeric($to)) {
            $message = array(
                'uid'        => $to,
                'subject'    => $subject,
                'content'    => $message,
                'tag'        => $tag,
                'time_send'  => time(),
            );
            $row = $model->createRow($message);
            $row->save();
            if (!$row->id) {
                return false;
            }
        } else {
            if ($to === '*') {
                $uids = Pi::user()->getIds();//TODO
            } elseif (is_array($to)) {
                $uids = $to;
            } else {
                return false;
            }
            if (!empty($uids)) {
                $tableName      = Pi::db()->prefix('notification', $this->getModule());
                $columns        = array('uid', 'subject', 'content', 'tag', 'time_send');
                $values         = array($subject, $message, $tag, time());
                $columnString   = '';
                $valueString    = ':uid, ';
                foreach ($columns as $column) {
                    $columnString .= $model->quoteIdentifier($column) . ', ';
                }
                foreach ($values as $value) {
                    $valueString .= $model->quoteValue($value) . ', ';
                }
                $columnString = substr($columnString, 0, -2);
                $valueString = substr($valueString, 0, -2);
                $sql = 'INSERT INTO ' . $model->quoteIdentifier($tableName) . ' (' . $columnString . ') VALUES ';
                while (!empty($uids)) {
                    $mySql = $sql;
                    $loop = 0;
                    foreach ($uids as $key => $uid) {
                        $myValueString = str_replace(':uid', $model->quoteValue($uid), $valueString);
                        $mySql .= '(' . $myValueString . '), ';
                        unset($uids[$key]);
                        if (++$loop > static::$batchInsertLen) {
                            break;
                        }
                    }
                    $mySql = substr($mySql, 0, -2);
                    $model->getAdapter()->query($mySql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                }
            }
        }
        return true;
    }

    /**
     * Get total account
     *
     * @return int|false
     */
    public function getAccount($uid)
    {
        //get total private message count
        $privateModel  = Pi::model('private_message', $this->getModule());
        $select = $privateModel->select()
                               ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                               ->where(function($where) use ($uid) {
                                   $fromWhere = clone $where;
                                   $toWhere = clone $where;
                                   $fromWhere->equalTo('uid_from', $uid);
                                   $fromWhere->equalTo('delete_status_from', 0);
                                   $toWhere->equalTo('uid_to', $uid);
                                   $toWhere->equalTo('delete_status_to', 0);
                                   $where->andPredicate($fromWhere)->orPredicate($toWhere);
                               });
        $privateCount = $privateModel->selectWith($select)->current()->count;
        //get total notification count
        $notifyModel  = Pi::model('notification', $this->getModule());
        $select = $notifyModel->select()
                              ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                              ->where(array('uid' => $uid, 'delete_status' => 0));
        $notifyCount = $notifyModel->selectWith($select)->current()->count;
        return $privateCount + $notifyCount;
    }

    /**
     * Get new message count to alert
     *
     * @return int|false
     */
    public function getAlert($uid)
    {
        //get new private message count
        $privateModel  = Pi::model('private_message', $this->getModule());
        $select = $privateModel->select()
                               ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                               ->where(array('uid_to' => $uid, 'delete_status_to' => 0, 'is_new_to' => 1));
        $privateCount = $privateModel->selectWith($select)->current()->count;
        //get new notification count
        $notifyModel  = Pi::model('notification', $this->getModule());
        $select = $notifyModel->select()
                              ->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))
                              ->where(array('uid' => $uid, 'delete_status' => 0, 'is_new' => 1));
        $notifyCount = $notifyModel->selectWith($select)->current()->count;
        return $privateCount + $notifyCount;
    }
}
