<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\Message\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Form of send message
 *
 * @author Xingyu Ji <xingyu@eefocus.com>
 */
class SendForm extends BaseForm
{
    /**
     * Editor type
     *
     * @var string
     */
    protected $markup = 'text';

    /**
     * Constructor
     *
     * @param null|string|int $name Optional name for the element
     * @param string $markup Page type: text, html, markdown
     */
    public function __construct($name = null, $markup = null)
    {
        $this->markup = $markup ?: $this->markup;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'readonly' => 'true'
            ),
            'options' => array(
                'label' => __('Recipient'),
            ),
        ));

        $set = '';
        switch ($this->markup) {
            case 'html':
                $editor = 'html';
                break;
            case 'markdown':
                $editor = 'markitup';
                $set = 'markdown';
                break;
            case 'text':
            default:
                $editor = 'textarea';
                break;
        }

        $this->add(array(
            'name' => 'content',
            'options' => array(
                'label' => __('Content'),
                'editor' => $editor,
                'set' => $set,
            ),
            'attributes' => array(
                'type' => 'editor',
                'placeholder' => __('Message content'),
                'rows' => '5',
              ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary',
                'value' => __('Send'),
            )
        ));
    }
}
