<?php
/**
 * Message module send form
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
 * @subpackage      Form
 */
 
namespace Module\Message\Form;

use Pi;
use Pi\Form\Form as BaseForm;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Form of send message
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
            'name'          => 'username',
            'attributes'    => array(
                'type'  => 'text',
            ),
            'options'       => array(
                'label' => __('Sent to'),
            ),
        ));

        $set = '';
        switch ($this->markup) {
            case 'html':
                $editor         = 'html';
                break;
            case 'markdown':
                $editor         = 'markitup';
                $set            = 'markdown';
                break;
            case 'text':
            default:
                $editor         = 'textarea';
                break;
        }

        $this->add(array(
            'name'          => 'content',
            'options'       => array(
                'label'     => __('Content'),
                'editor'    => $editor,
                'set'       => $set,
            ),
            'attributes'    => array(
                'type'          => 'editor',
            ),
        ));

        $this->add(array(
            'name'          => 'submit',
            'attributes'    => array(
                'type'  => 'submit',
                'value' => __('Send'),
            )
        ));
    }
}