<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\Message\Controller\Api;

use Pi;
use Pi\Mvc\Controller\ActionController;

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
class NotifyController extends ActionController
{
    public function listAction()
    {
        // Set default result
        $result = [
            'result' => false,
            'data'   => [],
            'error'  => [
                'code'        => 1,
                'message'     => __('Nothing selected'),
                'messageFlag' => false,
            ],
        ];

        // Get info from url
        $token = $this->params('token');

        // Check token
        $check = Pi::api('token', 'tools')->check($token, true);
        if ($check['status'] == 1) {
            // Set page
            $page  = $this->params('page', 1);
            $limit = $this->params('limit', 25);

            // Set model
            $model = $this->getModel('notification');

            // Get count
            $count = $model->count(['uid' => $check['uid'], 'is_deleted' => 0]);

            // Get list
            $list = [];
            if ($count) {
                $where  = ['uid' => $check['uid'], 'is_deleted' => 0];
                $order  = 'time_send DESC';
                $offset = $offset = (int)($page - 1) * $limit;

                // Make query
                $select = $model->select()->where($where)->order($order)->limit($limit)->offset($offset);
                $rowset = $model->selectWith($select);

                $list = [];
                foreach ($rowset as $row) {
                    $list[$row->id]                   = $row->toArray();
                    $list[$row->id]['time_send_view'] = _date($row->time_send);
                    $list[$row->id]['content']        = Pi::service('markup')->compile(
                        $row->content,
                        'text',
                        ['nl2br' => false]
                    );
                }

                // Set default result
                $result = [
                    'result' => true,
                    'data'   => [
                        'notifications' => array_values($list),
                        'paginator'     => [
                            'count' => $count,
                            'limit' => $limit,
                            'page'  => $page,
                        ],
                        'condition'     => [
                            'title' => __('List of received notifications'),
                        ],
                    ],
                    'error'  => [
                        'code'    => 0,
                        'message' => '',
                    ],
                ];
            }

        } else {
            // Set error
            $result['error'] = [
                'code'    => $check['code'],
                'message' => $check['message'],
            ];
        }

        // Return result
        return $result;
    }
}
