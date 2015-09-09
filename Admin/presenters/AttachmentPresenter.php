<?php

namespace AdminModule\FormModule;

/**
 *
 * @author Jakub Šanda <jakub.sanda at webcook.cz>
 */
class AttachmentPresenter extends BasePresenter
{
    private $attachment;

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

    protected function createComponentAttachmentGrid($name)
    {
        $grid = $this->createGrid($this, $name, 'WebCMS\FormModule\Entity\Attachment', array(
                array('by' => 'name', 'dir' => 'ASC'),
            )
        );

        $grid->addColumnText('name', 'Name')->setFilterText();

        $grid->addActionHref("editAttachment", 'Edit', 'editAttachment', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-primary', 'ajax')));
        $grid->addActionHref("deleteAttachment", 'Delete', 'deleteAttachment', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => array('btn', 'btn-danger'), 'data-confirm' => 'Are you sure you want to delete this item?'));

        return $grid;
    }

    public function createComponentAttachmentForm()
    {
        $form = $this->createForm();

        $form->addText('name', 'Name')->setRequired('Fill in name.')->setAttribute('class', array('form-control'));

        $form->addSubmit('send', 'Save')->setAttribute('class', array('btn btn-success'));
        $form->onSuccess[] = callback($this, 'attachmentFormSubmitted');

        $form->setDefaults($this->attachment->toArray());

        return $form;
    }

    public function attachmentFormSubmitted($form)
    {
        $values = $form->getValues();

        $this->attachment->setName($values->name);

        if(array_key_exists('files', $_POST)){
            $counter = 0;
            foreach($_POST['files'] as $path){

                $this->attachment->setPath($path);

                $counter++;
            }
        }

        if (!$this->attachment->getId()) {
            $this->em->persist($this->attachment);
        }

        $this->em->flush();

        $this->flashMessage('Attachment has been updated.', 'success');
        $this->redirect('default', array(
            'idPage' => $this->actualPage->getId(),
        ));
    }

    public function actionEditAttachment($id, $idPage)
    {
        $this->reloadContent();

        if (is_numeric($id)) {
            $this->attachment = $this->attachmentRepository->find($id);
        } else {
            $this->attachment = new \WebCMS\FormModule\Entity\Attachment();
        }
    }

    public function renderEditAttachment($idPage)
    {
        $this->template->attachment = $this->attachment;
        $this->template->idPage = $idPage;
    }

    public function actionDeleteAttachment($id, $idPage)
    {
        $this->attachment = $this->attachmentRepository->find($id);

        if ($this->attachment) {
            $this->em->remove($this->attachment);
            $this->em->flush();

            $this->flashMessage('attachment has been removed.', 'success');
        } else {
            $this->flashMessage('attachment not found.', 'warning');
        }

        $this->redirect('default', array(
            'idPage' => $this->actualPage->getId(),
        ));
    }
}
