<?php

namespace FrontendModule\FormModule;

/**
 * Description of FormPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class FormPresenter extends \FrontendModule\BasePresenter{
	
	private $repository;
	
	protected function startup() {
		parent::startup();

	}

	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($id){

	}

	public function renderDefault($id){
		
		$this->template->id = $id;
	}

}