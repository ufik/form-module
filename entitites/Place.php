<?php

/**
 * Webcms2 form module package.
 */

namespace WebCMS\FormModule\Entity;

use Doctrine\ORM\Mapping as orm;
use Gedmo\Mapping\Annotation as gedmo;

/**
 * @orm\Entity
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Place extends \WebCMS\Entity\Entity
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