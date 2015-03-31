<?php

namespace AdminModule\FormModule;

/**
 * Description of BasePresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class BasePresenter extends \AdminModule\BasePresenter
{
    protected $repository;

    protected $elementRepository;

    protected $contactRepository;

    protected $placeRepository;

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

    public function actionDefault($idPage)
    {
    }

    public function renderDefault($idPage)
    {
        $this->reloadContent();

        $this->template->idPage = $idPage;
    }
}
