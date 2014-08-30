<?php

namespace AdminModule\FormModule;

/**
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class PlacePresenter extends BasePresenter
{	
	private $place;

	protected function startup()
	{
		parent::startup();
	}

	protected function beforeRender()
	{
		parent::beforeRender();
	}
	
	public function actionDefault($idPage)
	{}
	
	public function renderDefault($idPage)
	{
		$this->reloadContent();	
		$this->template->idPage = $idPage;
	}
			
	protected function createComponentPlaceGrid($name)
	{			
		$grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Place', array(
				array('by' => 'name', 'dir' => 'ASC')
			)
		);
		
		$grid->addColumnText('name', 'Name')->setFilterText();
				
		$grid->addActionHref("editPlace", 'Edit', 'editPlace', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax')));
		$grid->addActionHref("deletePlace", 'Delete', 'deletePlace', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

		return $grid;
	}
	
    public function createComponentPlaceForm()
    {
        $form = $this->createForm();
        
        $form->addText('name', 'Name')->setRequired('Fill in name.')->setAttribute('class', array('form-control'));
        
        $form->addSubmit('send', 'Save')->setAttribute('class', array('btn btn-success'));
        $form->onSuccess[] = callback($this, 'placeFormSubmitted');

        $form->setDefaults($this->place->toArray());
        
        return $form;
    }

    public function placeFormSubmitted($form)
    {
        $values = $form->getValues();

        $this->place->setName($values->name);
        
        if (!$this->place->getId()) {
            $this->em->persist($this->place);
        }
        
        $this->em->flush();

        $this->flashMessage('Place has been updated.', 'success');
        $this->redirect('default', array(
            'idPage' => $this->actualPage->getId()
        ));
    }

	public function actionEditPlace($id, $idPage)
	{
		$this->reloadContent();
		
		if(is_numeric($id)){
			$this->place = $this->placeRepository->find($id);
		}else{
			$this->place = new \WebCMS\FormModule\Entity\Place;
		}
	}
	
	public function renderEditPlace($idPage)
	{
		$this->template->place = $this->place;
		$this->template->idPage = $idPage;
	}
	
	public function actionDeletePlace($id, $idPage)
	{
		$this->place = $this->placeRepository->find($id);
		
		if ($this->place) {
			$this->em->remove($this->place);
			$this->em->flush();	

			$this->flashMessage('place has been removed.', 'success');
		} else {
			$this->flashMessage('place not found.', 'warning');
		}
		
		$this->redirect('default', array(
			'idPage' => $this->actualPage->getId()
		));
	}
}