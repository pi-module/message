<?php
/**
 * Message module reply filter
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
 * @since           1.0.0
 * @package         Module\Message
 * @subpackage      Form
 */

namespace Module\Message\Form;

use Pi;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

/**
 * Filter of reply message
 *
 * @author Xingyu Ji <xingyu@eefocus.com>
 */
class ReplyFilter extends InputFilter
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->add(array(
            'name'          => 'content',
            'filters'       => array(
                array(
                    'name'  => 'StringTrim',
                ),
            ),
        ));   
    }
}
