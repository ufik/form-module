<?php

namespace WebCMS\FormModule;

/**
 * Description of Form
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Form extends \WebCMS\Module
{
    protected $name = 'Form';

    protected $author = 'Tomáš Voslař';

    protected $presenters = array(
        array(
            'name' => 'Form',
            'frontend' => true,
            'parameters' => false,
            ),
        array(
            'name' => 'Contact',
            'frontend' => false,
            'parameters' => false,
            ),
        array(
            'name' => 'Place',
            'frontend' => false,
            'parameters' => false,
            ),
        array(
            'name' => 'Attachment',
            'frontend' => false,
            'parameters' => false,
            ),
        array(
            'name' => 'Settings',
            'frontend' => false,
            ),
    );

    protected $params = array(

    );

    public function __construct()
    {
        $this->addBox('Form box', 'Form', 'formBox');
        $this->addBox('Form contacts box', 'Form', 'formContactsBox');
    }
}
