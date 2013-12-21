<?php

namespace WebCMS\FormModule;

/**
 * Description of Form
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Form extends \WebCMS\Module {
	
	protected $name = 'Form';
	
	protected $author = 'Tomáš Voslař';
	
	protected $presenters = array(
		array(
			'name' => 'Form',
			'frontend' => TRUE,
			'parameters' => FALSE
			),
		array(
			'name' => 'Settings',
			'frontend' => FALSE
			)
	);
	
	protected $params = array(
		
	);
	
	public function __construct(){
		$this->addBox('Form box', 'Form', 'formBox');
	}
	
}