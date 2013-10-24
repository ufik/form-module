<?php

namespace FrontendModule\FormModule;

/**
 * Description of FormPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class FormPresenter extends \FrontendModule\BasePresenter{
	
	private $repository;
	
	private $elementRepository;
	
	private $elements;
	
	protected function startup() {
		parent::startup();

		$this->repository = $this->em->getRepository('WebCMS\FormModule\Doctrine\Entry');
		$this->elementRepository = $this->em->getRepository('WebCMS\FormModule\Doctrine\Element');
	}

	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($id){
		$this->elements = $this->elementRepository->findBy(array(
			'page' => $this->actualPage
		));
	}
	
	public function createComponentForm(){
		$form = $this->createForm('form-submit');
		
		foreach($this->elements as $element){
			if($element->getType() === 'text'){
				$form->addText($element->getName(), $element->getLabel());
			}elseif($element->getType() === 'date'){
				$form->addText($element->getName(), $element->getLabel())->setAttribute('class', 'datepicker');
			}elseif($element->getType() === 'textarea'){
				$form->addTextArea($element->getName(), $element->getLabel());
			}elseif($element->getType() === 'checkbox'){
				$form->addCheckbox($element->getName(), $element->getLabel());
			}
			
			$form[$element->getName()]->setAttribute('class', 'form-control');
			
			if($element->getRequired()){
				$form[$element->getName()]->setRequired($element->getDescription());
			}
		}
		
		$form->addSubmit('send', 'Send');
		$form->onSuccess[] = callback($this, 'formSubmitted');
		
		return $form;
	}
	
	public function formSubmitted($form){
		$values = $form->getValues();
		
		$data = array();
		
		foreach($values as $key => $val){
			$element = $this->elementRepository->findOneByName($key);
			
			$data[$element->getLabel()] = $val;
		}
		
		$entry = new \WebCMS\FormModule\Doctrine\Entry;
		$entry->setDate(new \DateTime);
		$entry->setPage($this->actualPage);
		$entry->setData($data);
		
		$this->em->persist($entry);
		$this->em->flush();
		
		// info email
		$infoMail = $this->settings->get('Info email', 'basic', 'text')->getValue();
		$parsed = explode('@', $infoMail);
		
		$mailBody = $this->settings->get('Info email', 'formModule' . $this->actualPage->getId(), 'textarea')->getValue();
		
		$mail = new \Nette\Mail\Message;
		$mail->addTo($infoMail);
		
		if($this->getHttpRequest()->url->host !== 'localhost') $mail->setFrom('no-reply@' . $this->getHttpRequest()->url->host);
		else $mail->setFrom('no-reply@test.cz');
			
		$mail->setSubject('New message from ' . $this->getHttpRequest()->url->baseUrl);
		$mail->setHtmlBody($mailBody);
		$mail->send();
		
		$this->flashMessageTranslated('Data has been sent.', 'success');
		$this->redirect('default', array(
			'path' => $this->actualPage->getPath(),
			'abbr' => $this->abbr
		));
	}
	
	public function renderDefault($id){
		
		$this->template->elements = $this->elements;
		$this->template->id = $id;
	}

}