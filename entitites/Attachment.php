<?php

/**
 * Webcms2 form module package.
 */

namespace WebCMS\FormModule\Entity;

use Doctrine\ORM\Mapping as orm;

/**
 * @orm\Entity
 * @author Jakub Šanda <jakub.sanda at webcook.cz>
 */
class Attachment extends \WebCMS\Entity\Entity
{
    /**
     * @orm\Column
     * @var [type]
     */
    private $name;

    /**
     * @orm\Column(type="text")
     */
    private $path;

    /**
     * Gets the value of name.
     *
     * @return [type]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param [type] $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getPath() 
    {
        return $this->path;
    }

    public function setPath($path) 
    {
        $this->path = $path;
        
        return $this;
    }
}
