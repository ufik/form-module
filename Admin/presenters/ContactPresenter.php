<?php

namespace AdminModule\FormModule;

/**
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class ContactPresenter extends BasePresenter
{	
	private $contact;

	protected function startup()
	{
		parent::startup();
	}

	protected function beforeRender()
	{
		parent::beforeRender();
	}
	
	public function actionDefault($idPage)
	{
	}
	
	public function renderDefault($idPage)
	{
		$this->reloadContent();	
		$this->template->idPage = $idPage;
	}
			
	protected function createComponentContactGrid($name)
	{			
		$grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Contact', array(
				array('by' => 'name', 'dir' => 'ASC')
			)
		);
		
		$grid->addColumnText('name', 'Name')->setFilterText();
		$grid->addColumnText('street', 'Street')->setFilterText();
		$grid->addColumnText('city', 'City')->setFilterText();
		$grid->addColumnText('web', 'Web')->setFilterText();
        $grid->addColumnText('email', 'Email')->setFilterText();
				
		$grid->addActionHref("editContact", 'Edit', 'editContact', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax')));
		$grid->addActionHref("deleteContact", 'Delete', 'deleteContact', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

		return $grid;
	}
	
    public function createComponentContactForm()
    {
        $form = $this->createForm();
        
        $places = $this->placeRepository->findAll();
        $places = $this->collectionToArray($places, 'name');

        $form->addText('name', 'Name')->setRequired('Fill in name.')->setAttribute('class', array('form-control'));
        $form->addText('email', 'Email')->setRequired('Fill in email.')->setAttribute('class', array('form-control'));
        $form->addText('street', 'Street')->setAttribute('class', array('form-control'));
        $form->addText('city', 'City')->setAttribute('class', array('form-control'));
        $form->addText('postcode', 'Postcode')->setAttribute('class', array('form-control'));
        $form->addText('web', 'Web')->setAttribute('class', array('form-control'));
        $form->addSelect('place', 'Place', $places)->setAttribute('class', array('form-control'));
        
        $form->addSubmit('send', 'Save')->setAttribute('class', array('btn btn-success'));
        $form->onSuccess[] = callback($this, 'contactFormSubmitted');

        $form->setDefaults($this->contact->toArray());
        
        return $form;
    }

    public function contactFormSubmitted($form)
    {
        $values = $form->getValues();

        $this->contact->setName($values->name);
        $this->contact->setEmail($values->email);
        $this->contact->setStreet($values->street);
        $this->contact->setCity($values->city);
        $this->contact->setPostcode($values->postcode);
        $this->contact->setWeb($values->web);

        if (is_numeric($values->place)) {
            $place = $this->placeRepository->find($values->place);
            $this->contact->setPlace($place);    
        }
        
        if (!$this->contact->getId()) {
            $this->em->persist($this->contact);
        }
        
        $this->em->flush();

        $this->flashMessage('Contact has been updated.', 'success');
        $this->redirect('default', array(
            'idPage' => $this->actualPage->getId()
        ));
    }

	public function actionEditContact($id, $idPage)
	{
		$this->reloadContent();
		
		if(is_numeric($id)){
			$this->contact = $this->contactRepository->find($id);
		}else{
			$this->contact = new \WebCMS\FormModule\Entity\Contact;
		}
	}
	
	public function renderEditContact($idPage)
	{
		$this->template->contact = $this->contact;
		$this->template->idPage = $idPage;
	}
	
	public function actionDeleteContact($id, $idPage)
	{
		$this->contact = $this->contactRepository->find($id);
		
		if ($this->contact) {
			$this->em->remove($this->contact);
			$this->em->flush();	

			$this->flashMessage('Contact has been removed.', 'success');
		} else {
			$this->flashMessage('Contact not found.', 'warning');
		}
		
		$this->redirect('default', array(
			'idPage' => $this->actualPage->getId()
		));
	}
}