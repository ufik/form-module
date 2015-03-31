<?php

namespace WebCMS\FormModule\Entity;

use Doctrine\ORM\Mapping as orm;
use Gedmo\Mapping\Annotation as gedmo;

/**
 * Description of Post
 * @orm\Entity
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Entry extends \WebCMS\Entity\Entity
{
    /**
     * @gedmo\Timestampable(on="create")
     * @orm\Column(type="datetime")
     */
    private $date;

    /**
     * @orm\Column(type="text")
     */
    private $data;

    /**
     * @orm\ManyToOne(targetEntity="WebCMS\Entity\Page")
     * @orm\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $page;

    public function getDate()
    {
        return $this->date;
    }

    public function getData()
    {
        return unserialize($this->data);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setData($data)
    {
        $this->data = serialize($data);
    }

    public function setPage($page)
    {
        $this->page = $page;
    }
}
