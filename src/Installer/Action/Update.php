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

namespace Module\Message\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Zend\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', [$this, 'updateSchema']);
        parent::attachDefaultListeners();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(Event $e)
    {
        $moduleVersion = $e->getParam('version');

        // Set message model
        $messageModel   = Pi::model('message', $this->module);
        $messageTable   = $messageModel->getTable();
        $messageAdapter = $messageModel->getAdapter();

        // Set message model
        $notificationModel   = Pi::model('notification', $this->module);
        $notificationTable   = $notificationModel->getTable();
        $notificationAdapter = $notificationModel->getAdapter();

        // Update to version 1.0.3
        if (version_compare($moduleVersion, '1.0.3', '<')) {
            // Alter table : ADD conversation
            $sql = sprintf("ALTER TABLE %s ADD `conversation` VARCHAR(32) NOT NULL DEFAULT '' , ADD INDEX (`conversation`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `uid_from` (`uid_from`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `uid_to` (`uid_to`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `time_send` (`time_send`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `select_1` (`uid_from`, `is_deleted_from`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `select_2` (`uid_to`, `is_deleted_to`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }

            // Add index
            $sql = sprintf("ALTER TABLE %s ADD INDEX `unread` (`uid_to`, `is_deleted_to`, `is_read_to`)", $messageTable);
            try {
                $messageAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        // Update to version 1.0.4
        if (version_compare($moduleVersion, '1.0.4', '<')) {
            // Update value
            $select = $messageModel->select();
            $rowset = $messageModel->selectWith($select);
            foreach ($rowset as $row) {
                $conversation      = Pi::api('api', 'message')->setConversation($row->time_send);
                $row->conversation = $conversation;
                $row->save();
            }
        }

        // Update to version 1.2.0
        if (version_compare($moduleVersion, '1.2.0', '<')) {
            // Alter table : ADD image
            $sql = sprintf("ALTER TABLE %s ADD `image` VARCHAR(255) NOT NULL DEFAULT '' AFTER `content`", $notificationTable);
            try {
                $notificationAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db', [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        return true;
    }
}
