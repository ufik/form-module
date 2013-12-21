<?php

namespace FrontendModule\FormModule;

use Nette\Application\UI;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;

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
	
	public function createComponentForm($name, $context = null, $fromPage = null){
		
		if($context != null){
			$this->elements = $context->em->getRepository('WebCMS\FormModule\Doctrine\Element')->findBy(array(
				'page' => $fromPage
			));
			
			$form = new UI\Form();
		
			$form->getElementPrototype()->action = $context->link('default', array(
				'path' => $fromPage->getPath(),
				'abbr' => $context->abbr,
				'do' => 'form-submit'
			));

			$form->setTranslator($context->translator);
			$form->setRenderer(new BootstrapRenderer);
			
			$form->getElementPrototype()->class = 'form-horizontal contact-agent-form';
			
			$form->addHidden('redirect')->setDefaultValue(true);
		}else{
			$form = $this->createForm('form-submit', 'default', $context);
			$form->addHidden('redirect')->setDefaultValue(false);
		}
		
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
			$form[$element->getName()]->setAttribute('placeholder', $element->getDescription());
			
			if($element->getRequired()){
				$form[$element->getName()]->setRequired($element->getDescription());
			}
		}
		
		$form->addSubmit('send', 'Send')->setAttribute('class', 'btn btn-primary btn-lg');
		$form->onSuccess[] = callback($this, 'formSubmitted');
		
		return $form;
	}
	
	public function formSubmitted($form){
		$values = $form->getValues();
		
		$data = array();
		
		$redirect = $values->redirect;
		unset($values->redirect);
		
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
		else $mail->setFrom('no-reply@test.cz'); // TODO move to settings
			
		$mail->setSubject('New message from ' . $this->getHttpRequest()->url->baseUrl);
		$mail->setHtmlBody($mailBody);
		$mail->send();
		
		$this->flashMessageTranslated('Data has been sent.', 'success');
		
		if(!$redirect){
			$this->redirect('default', array(
				'path' => $this->actualPage->getPath(),
				'abbr' => $this->abbr
			));
		}else{
			$httpRequest = $this->getContext()->getService('httpRequest');
			
			$url = $httpRequest->getReferer();
			$url->appendQuery(array(self::FLASH_KEY => $this->getParam(self::FLASH_KEY)));
			
			$this->redirectUrl($url->absoluteUrl);
		}
	}
	
	public function renderDefault($id){
		
		$this->template->elements = $this->elements;
		$this->template->id = $id;
	}

	public function formBox($context, $fromPage){
		
		$template = $context->createTemplate();
		$template->form = $this->createComponentForm('form',$context, $fromPage);
		$template->setFile('../app/templates/form-module/Form/boxes/form.latte');
	
		return $template;
	}
}