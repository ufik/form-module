<?php

    namespace AdminModule\FormModule;

    /**
     * Description of SettingsPresenter
     * @author Tomáš Voslař <tomas.voslar at webcook.cz>
     */
    class SettingsPresenter extends BasePresenter {

	private $element;

	protected function startup() {
	    parent::startup();
	}

	protected function beforeRender() {
	    parent::beforeRender();
	}

	public function actionDefault($idPage) {
	    
	}

	public function createComponentSettingsForm() {

	    $settings = array();
	    $settings[] = $this->settings->get('Info email subject', 'formModule' . $this->actualPage->getId(), 'text');
	    $settings[] = $this->settings->get('Info email', 'formModule' . $this->actualPage->getId(), 'textarea', array());

	    return $this->createSettingsForm($settings);
	}

	protected function createComponentElementGrid($name) {

	    $grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Element', array(), array(
		'page = ' . $this->actualPage->getId()
		)
	    );

	    $grid->addColumnText('name', 'Name');
	    $grid->addColumnText('label', 'Label');
	    $grid->addColumnText('type', 'Type');
	    $grid->addColumnText('required', 'Required')->setCustomRender(function($item) {
		return $item->getRequired() ? 'Yes' : 'No';
	    });

	    $grid->addActionHref("updateElement", 'Response', 'updateElement', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax'), 'data-toggle' => 'modal', 'data-target' => '#myModal', 'data-remote' => 'false'));
	    $grid->addActionHref("deleteElement", 'Delete', 'deleteElement', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

	    return $grid;
	}

	public function renderDefault($idPage) {
	    $this->reloadContent();

	    $this->template->config = $this->settings->getSection('formModule');
	    $this->template->idPage = $idPage;
	}

	public function createComponentElementForm() {
	    $form = $this->createForm();

	    $types = array(
		'text' => 'Text input',
		'email' => 'Email',
		'textarea' => 'Long text',
		'date' => 'Date input',
		'checkbox' => 'Checkbox'
	    );

	    $form->addText('label', 'Label');
	    $form->addText('description', 'Description');
	    $form->addSelect('type', 'Type', $types);
	    $form->addCheckbox('required', 'Required');

	    $form->setDefaults($this->element->toArray());

	    $form->addSubmit('send', 'Save');
	    $form->onSuccess[] = callback($this, 'elementFormSubmitted');

	    return $form;
	}

	public function elementFormSubmitted($form) {
	    $values = $form->getValues();

	    $name = \Nette\Utils\Strings::webalize($values->label);
	    $name = str_replace('-', '', $name);

	    $this->element->setName($name);
	    $this->element->setLabel($values->label);
	    $this->element->setDescription($values->description);
	    $this->element->setType($values->type);
	    $this->element->setRequired($values->required);
	    $this->element->setPage($this->actualPage);

	    if (!$this->element->getId()) {
		$this->em->persist($this->element);
	    }

	    $this->em->flush();

	    $this->flashMessage('Element has been saved.', 'success');
	    $this->redirect('default', array(
		'idPage' => $this->actualPage->getId()
	    ));
	}

	public function actionUpdateElement($idPage, $id) {
	    $this->reloadModalContent();

	    if ($id)
		$this->element = $this->elementRepository->find($id);
	    else
		$this->element = new \WebCMS\FormModule\Entity\Element;
	}

	public function renderUpdateElement($idPage) {

	    $this->template->idPage = $idPage;
	}
	
	public function actionDeleteElement($idPage, $id){
	    $this->element = $this->elementRepository->find($id);
	    
	    if($this->element){
		$this->em->remove($this->element);
		$this->em->flush();
	    }
	    
	    $this->flashMessage('Element has been deleted.', 'success');
	    $this->redirect('default', array(
		'idPage' => $this->actualPage->getId()
	    ));
	}
    }
    