<?php

namespace FrontendModule\FormModule;

use Nette\Application\UI;
use Kdyby\BootstrapFormRenderer\BootstrapRenderer;

/**
 * Description of FormPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class FormPresenter extends \FrontendModule\BasePresenter
{	
	private $repository;
	
	private $elementRepository;

	private $contactRepository;

	private $placeRepository;
	
	private $elements;
	
	protected function startup()
	{
		parent::startup();

		$this->repository = $this->em->getRepository('WebCMS\FormModule\Entity\Entry');
		$this->elementRepository = $this->em->getRepository('WebCMS\FormModule\Entity\Element');
		$this->contactRepository = $this->em->getRepository('WebCMS\FormModule\Entity\Contact');
		$this->placeRepository = $this->em->getRepository('WebCMS\FormModule\Entity\Place');
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		
	}
	
	public function actionDefault($id)
	{
		$this->elements = $this->elementRepository->findBy(array(
			'page' => $this->actualPage
		));
	}
	
	public function createComponentForm($name, $context = null, $fromPage = null)
	{
		if($context != null){
			$this->elements = $context->em->getRepository('WebCMS\FormModule\Entity\Element')->findBy(array(
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
		
		$form->addHidden('contactId');
		foreach($this->elements as $element){
			if($element->getType() === 'text'){
				$form->addText($element->getName(), $element->getLabel());
			}elseif($element->getType() === 'date'){
				$form->addText($element->getName(), $element->getLabel())->getControlPrototype()->addClass('datepicker');
			}elseif($element->getType() === 'textarea'){
				$form->addTextArea($element->getName(), $element->getLabel());
			}elseif($element->getType() === 'checkbox'){
				$form->addCheckbox($element->getName(), $element->getLabel());
			}elseif($element->getType() === 'email'){
				$form->addText($element->getName(), $element->getLabel())->addRule(UI\Form::EMAIL);
			}
			
			$form[$element->getName()]->getControlPrototype()->addClass('form-control');
			$form[$element->getName()]->setAttribute('placeholder', $element->getDescription());
			
			if($element->getRequired()){
				$form[$element->getName()]->setRequired($element->getDescription());
			}
		}
		
		$form->addSubmit('send', 'Send')->setAttribute('class', 'btn btn-primary btn-lg');
		$form->onSuccess[] = callback($this, 'formSubmitted');
		
		return $form;
	}
	
	public function formSubmitted($form)
	{
		$values = $form->getValues();

		if (!array_key_exists('realHash', $_POST) || \WebCMS\Helpers\SystemHelper::rpHash($_POST['real']) == $_POST['realHash']) {
			
			$data = array();

			$redirect = $values->redirect;
			$contactId = $values->contactId;
			unset($values->redirect);
			unset($values->contactId);

			$emailContent = '';
			foreach($values as $key => $val){
				$element = $this->elementRepository->findOneByName($key);

				if($element->getType() === 'checkbox'){
					$value = $val ? $this->translation['Yes'] : $this->translation['No'];
				}else{
					$value = $val;
				}

				$data[$element->getLabel()] = $value;

				$emailContent .= $element->getLabel() . ': ' . $value . '<br />';
			}

			$entry = new \WebCMS\FormModule\Entity\Entry;
			$entry->setDate(new \DateTime);
			$entry->setPage($this->actualPage);
			$entry->setData($data);

			$this->em->persist($entry);
			$this->em->flush();

			// info email
			if (is_numeric($contactId)) {
				$contact = $this->contactRepository->find($contactId);
				$infoMail = $contact->getEmail();
			} else {
				$infoMail = $this->settings->get('Info email', 'basic', 'text')->getValue();
				$infoMail = \WebCMS\Helpers\SystemHelper::replaceStatic($infoMail);
				$parsed = explode('@', $infoMail);	
			}
			
			$mailBody = $this->settings->get('Info email', 'formModule' . $this->actualPage->getId(), 'textarea')->getValue();
			$mailBody = \WebCMS\Helpers\SystemHelper::replaceStatic($mailBody, array('[FORM_CONTENT]'), array($emailContent));

			$mail = new \Nette\Mail\Message;
			$mail->addTo($infoMail);

			if($this->getHttpRequest()->url->host !== 'localhost') $mail->setFrom('no-reply@' . $this->getHttpRequest()->url->host);
			else $mail->setFrom('no-reply@test.cz'); // TODO move to settings

			$mail->setSubject($this->settings->get('Info email subject', 'formModule' . $this->actualPage->getId(), 'text')->getValue());
			$mail->setHtmlBody($mailBody);
			
			try {
				$mail->send();	
				$this->flashMessage('Data has been sent.', 'success');
			} catch (\Exception $e) {
				$this->flashMessage('Cannot send email.', 'danger');					
			}
			
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
		
		} else {
			
			$this->flashMessage('Wrong protection code.', 'danger');	
			$httpRequest = $this->getContext()->getService('httpRequest');

			$url = $httpRequest->getReferer();
			$url->appendQuery(array(self::FLASH_KEY => $this->getParam(self::FLASH_KEY)));

			$this->redirectUrl($url->absoluteUrl);
			
	    }
	}
	
	public function renderDefault($id)
	{
		$this->template->form = $this->createComponentForm('form', $this, $this->actualPage);
		$this->template->elements = $this->elements;
		$this->template->id = $id;
	}

	public function formBox($context, $fromPage)
	{	
		$template = $context->createTemplate();
		$template->form = $this->createComponentForm('form',$context, $fromPage);
		$template->setFile('../app/templates/form-module/Form/boxes/form.latte');
	
		return $template;
	}

	public function formContactsBox($context, $fromPage)
	{
		$template = $context->createTemplate();
		$template->places = $context->em->getRepository('\WebCMS\FormModule\Entity\Place')->findBy(array(), array(
			'name' => 'ASC'
		));
		$template->contacts = $context->em->getRepository('\WebCMS\FormModule\Entity\Contact')->findAll(array(), array(
			'name' => 'ASC'
		));
		$template->setFile('../app/templates/form-module/Form/boxes/contacts.latte');
	
		return $template;
	}
}
