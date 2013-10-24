<?php

namespace AdminModule\FormModule;

/**
 * Description of BasePresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class BasePresenter extends \AdminModule\BasePresenter {
	
	protected $repository;
	
	protected $elementRepository;
	
	protected function startup() {
		parent::startup();
		
		$this->repository = $this->em->getRepository('WebCMS\FormModule\Doctrine\Entry');
		$this->elementRepository = $this->em->getRepository('WebCMS\FormModule\Doctrine\Element');
	}

	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($idPage){

	}
	
	public function renderDefault($idPage){
		$this->reloadContent();
		
		$this->template->idPage = $idPage;
	}
}