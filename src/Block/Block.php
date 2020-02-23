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

namespace Module\Message\Block;

use Pi;
use Module\Guide\Form\SearchLocationForm;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\In;
use Module\Message\Form\ReplyForm;
use Module\Message\Form\ReplyFilter;

class Block
{
    public static function conversation($options = [], $module = null)
    {
        $block = [];
        $block = array_merge($block, $options);

        Pi::service('authentication')->requireLogin();
        $conversation = isset($options['conversation']) ? $options['conversation'] : 0;

        // Current user id
        $userId = Pi::user()->getUser()->id;
        // Get message detail
        $detail = Pi::api('message', 'message')->showDetail($conversation, $userId);
        if ($userId == $detail['uid_from']) {
            $toId = $detail['uid_to'];
        } else {
            $toId = $detail['uid_from'];
        }

        // Get list of conversations
        $list = [];
        if ($conversation) {
            $where = [
                'conversation' => $conversation,
            ];
            if (!isset($options['admin']) || (isset($options['admin']) && !$options['admin'])) {
                $where['is_deleted_from'] = 0;
                $where['is_deleted_to']   = 0;
            }
            $order  = ['time_send ASC', 'id ASC'];
            $model  = Pi::model('message', 'message');
            $select = $model->select()->where($where)->order($order);
            $rowset = $model->selectWith($select);
            foreach ($rowset as $row) {
                $list[$row->id] = Pi::api('api', 'message')->canonizeMessage($row);
            }
        }

        $block['list'] = $list;
        $block['uid']  = $userId;
        return $block;

    }

}
