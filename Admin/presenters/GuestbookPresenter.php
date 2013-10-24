<?php

namespace AdminModule\FormModule;

/**
 * Description of FormPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class FormPresenter extends BasePresenter {
	
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
			
	
	protected function createComponentGuestbookGrid($name){
				
		$grid = $this->createGrid($this, $name, 'WebCMS\GuestbookModule\Doctrine\Post', array(
			array('by' => 'date', 'dir' => 'DESC')
			),
			array(
				'page = ' . $this->actualPage->getId()
			)
		);
		
		$grid->addColumnText('name', 'Name')->setSortable()->setFilterText();
		$grid->addColumnDate('date', 'Post date')->setCustomRender(function($item){
			return $item->getDate()->format('d.m.Y H:i:s');
		})->setFilterDate();
		$grid->addColumnText('responsePost', 'Responded?')->setCustomRender(function($item){
			return $item->getPostResponse() ? 'Yes' : 'No';
		});
		
		$grid->addActionHref("responsePost", 'Response', 'responsePost', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax')));
		$grid->addActionHref("deletePost", 'Delete', 'deletePost', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

		return $grid;
	}

	public function actionDeletePost($id, $idPage){
		$this->post = $this->repository->find($id);
		
		$this->em->remove($this->post);
		$this->em->flush();
		
		$this->flashMessage($this->translation['Post has been removed.'], 'success');
		$this->redirect('default', array(
			'idPage' => $this->actualPage->getId()
		));
	}
}