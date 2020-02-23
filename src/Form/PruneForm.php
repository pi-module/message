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

namespace Module\Message\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class PruneForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name);
    }

    public function init()
    {
        // date
        $this->add(
            [
                'name'       => 'date',
                'type'       => 'datepicker',
                'options'    => [
                    'label'      => __('All messages Before'),
                    'datepicker' => [
                        'format' => 'yyyy-mm-dd',
                    ],
                ],
                'attributes' => [
                    'id'       => 'time-start',
                    'required' => true,
                    'value'    => date('Y-m-d', strtotime("-3 Months")),
                ],
            ]
        );
        // read
        $this->add(
            [
                'name'       => 'read',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Just read messages by user'),
                ],
                'attributes' => [
                    'value'       => 0,
                    'description' => __('Remove read messages by user before selected time'),
                ],
            ]
        );
        // deleted
        $this->add(
            [
                'name'       => 'deleted',
                'type'       => 'checkbox',
                'options'    => [
                    'label' => __('Just deleted messages by user'),
                ],
                'attributes' => [
                    'value'       => 0,
                    'description' => __('Remove deleted messages by user before selected time'),
                ],
            ]
        );
        // Submit
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => __('Prune'),
                ],
            ]
        );
    }
}	
