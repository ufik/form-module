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
}
