<?php

namespace AdminModule\FormModule;

use Nette;

    /**
     * Description of SettingsPresenter
     * @author Tomáš Voslař <tomas.voslar at webcook.cz>
     */
    class SettingsPresenter extends BasePresenter
    {
        private $element;

        protected function startup()
        {
            parent::startup();
        }

        protected function beforeRender()
        {
            parent::beforeRender();

        // validate FROM email address
        $infoFromEmail = $this->settings->get('Info email FROM address', 'formModule'.$this->actualPage->getId(), 'text')->getValue();
            if (!empty($infoFromEmail)) {
                if (!Nette\Utils\Validators::isEmail($infoFromEmail)) {
                    $this->flashMessage('Please fill in valid FROM email address.', 'warning');
                }
            }
        // validate REPLYTO email address
        $infoReplyToEmail = $this->settings->get('Info email REPLYTO address', 'formModule'.$this->actualPage->getId(), 'text')->getValue();
            if (!empty($infoReplyToEmail)) {
                if (!Nette\Utils\Validators::isEmail($infoReplyToEmail)) {
                    $this->flashMessage('Please fill in valid REPLY-TO email address.', 'warning');
                }
            }
        // validate/sanitize CC addresses
        $infoCc = $this->settings->get('Info email CC recipients', 'formModule'.$this->actualPage->getId(), 'text')->getValue();
            if (!empty($infoCc)) {
                $sanitizedCc = rtrim(rtrim($infoCc), ';');
                if ($infoCc !== $sanitizedCc) {
                    $this->settings->get('Info email CC recipients', 'formModule'.$this->actualPage->getId(), 'text')->setValue($sanitizedCc);
                    $this->em->flush();
                }
                $cc = explode(';', $sanitizedCc);
                foreach ($cc as $key => $value) {
                    if (!Nette\Utils\Validators::isEmail($value)) {
                        $this->flashMessage('Invalid CC email address ('.$value.').', 'warning');
                    }
                }
            }
        // validate/sanitize BCC addresses
        $infoBcc = $this->settings->get('Info email BCC recipients', 'formModule'.$this->actualPage->getId(), 'text')->getValue();
            if (!empty($infoBcc)) {
                $sanitizedBcc = rtrim(rtrim($infoBcc), ';');
                if ($infoBcc !== $sanitizedBcc) {
                    $this->settings->get('Info email BCC recipients', 'formModule'.$this->actualPage->getId(), 'text')->setValue($sanitizedBcc);
                    $this->em->flush();
                }
                $bcc = explode(';', $sanitizedBcc);
                foreach ($bcc as $key => $value) {
                    if (!Nette\Utils\Validators::isEmail($value)) {
                        $this->flashMessage('Invalid BCC email address ('.$value.').', 'warning');
                    }
                }
            }
        }

        public function actionDefault($idPage)
        {
        }

        public function createComponentSettingsForm()
        {
            $settings = array();
            $settings[] = $this->settings->get('Info email FROM address', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email FROM name', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email REPLYTO address', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email REPLYTO name', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email CC recipients', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email BCC recipients', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email subject', 'formModule'.$this->actualPage->getId(), 'text');
            $settings[] = $this->settings->get('Info email', 'formModule'.$this->actualPage->getId(), 'textarea', array());

            return $this->createSettingsForm($settings);
        }

        protected function createComponentElementGrid($name)
        {
            $grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Element', array(), array(
        'page = '.$this->actualPage->getId(),
        )
        );

            $grid->addColumnText('name', 'Name');
            $grid->addColumnText('label', 'Label');
            $grid->addColumnText('description', 'Description');
            $grid->addColumnText('value', 'Value');
            $grid->addColumnText('type', 'Type');
            $grid->addColumnText('required', 'Required')->setCustomRender(function ($item) {
        return $item->getRequired() ? 'Yes' : 'No';
        });

            $grid->addActionHref("updateElement", 'Edit', 'updateElement', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax'), 'data-toggle' => 'modal', 'data-target' => '#myModal', 'data-remote' => 'false'));
            $grid->addActionHref("deleteElement", 'Delete', 'deleteElement', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

            return $grid;
        }

        public function renderDefault($idPage)
        {
            $this->reloadContent();

            $this->template->config = $this->settings->getSection('formModule');
            $this->template->idPage = $idPage;
        }

        public function createComponentElementForm()
        {
            $form = $this->createForm();

            $types = array(
        'text' => 'Text input',
        'email' => 'Email',
        'textarea' => 'Long text',
        'date' => 'Date input',
        'checkbox' => 'Checkbox',
        'hidden' => 'Hidden field',
        );

            $form->addText('label', 'Label')->setRequired();
            $form->addText('description', 'Description')->setRequired();
            $form->addText('value', 'Value')->setAttribute('class', array('jq_value_input', 'hidden'));
            $form->addSelect('type', 'Type', $types)->setAttribute('class', 'jq_type_box');
            $form->addCheckbox('required', 'Required');

            $form->setDefaults($this->element->toArray());

            $form->addSubmit('send', 'Save');
            $form->onSuccess[] = callback($this, 'elementFormSubmitted');

            return $form;
        }

        public function elementFormSubmitted($form)
        {
            $values = $form->getValues();

            $name = \Nette\Utils\Strings::webalize($values->label);
            $name = str_replace('-', '', $name);

            $this->element->setName($name);
            $this->element->setLabel($values->label);
            $this->element->setDescription($values->description);
            $this->element->setType($values->type);
            $this->element->setRequired($values->required);
            $this->element->setValue($values->value);
            $this->element->setPage($this->actualPage);

            if (!$this->element->getId()) {
                $this->em->persist($this->element);
            }

            $this->em->flush();

            $this->flashMessage('Element has been saved.', 'success');
            $this->redirect('default', array(
            'idPage' => $this->actualPage->getId(),
        ));
        }

        public function actionUpdateElement($idPage, $id)
        {
            $this->reloadModalContent();

            if ($id) {
                $this->element = $this->elementRepository->find($id);
            } else {
                $this->element = new \WebCMS\FormModule\Entity\Element();
            }
        }

        public function renderUpdateElement($idPage)
        {
            $this->template->idPage = $idPage;
        }

        public function actionDeleteElement($idPage, $id)
        {
            $this->element = $this->elementRepository->find($id);

            if ($this->element) {
                $this->em->remove($this->element);
                $this->em->flush();
            }

            $this->flashMessage('Element has been deleted.', 'success');
            $this->redirect('default', array(
            'idPage' => $this->actualPage->getId(),
        ));
        }
    }
