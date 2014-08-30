<?php

namespace AdminModule\FormModule;

/**
 * Description of FormPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class FormPresenter extends BasePresenter {
	
	private $entry;
	
	protected function startup() {
		parent::startup();
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
			
	
	protected function createComponentEntryGrid($name){
				
		$grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Entry', array(
			array('by' => 'date', 'dir' => 'DESC')
			),
			array(
				'page = ' . $this->actualPage->getId()
			)
		);
		
		$grid->addColumnDate('date', 'Entry date')->setCustomRender(function($item){
			return $item->getDate()->format('d.m.Y H:i:s');
		})->setFilterDate();
				
		$grid->addActionHref("viewEntry", 'View', 'viewEntry', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax')));
		$grid->addActionHref("deleteEntry", 'Delete', 'deleteEntry', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

		return $grid;
	}
	
	public function actionViewEntry($id, $idPage){
		$this->reloadContent();
		
		$this->entry = $this->repository->find($id);
	}
	
	public function renderViewEntry($idPage){
		
		$this->template->entry = $this->entry;
		$this->template->idPage = $idPage;
	}
	
	public function actionDeleteEntry($id, $idPage){
		$this->post = $this->repository->find($id);
		
		$this->em->remove($this->post);
		$this->em->flush();
		
		$this->flashMessage('Entry has been removed.', 'success');
		$this->redirect('default', array(
			'idPage' => $this->actualPage->getId()
		));
	}
}