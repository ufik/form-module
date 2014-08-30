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
class Contact extends \WebCMS\Entity\Entity
{
	/**
	 * @orm\Column
	 * @var [type]
	 */
	private $name;

	/**
	 * @orm\Column
	 * @var [type]
	 */
	private $email;

    /**
     * @orm\Column
     * @var 
     */    
    private $street;

    /**
     * @orm\Column
     * @var 
     */
    private $city;

    /**
     * @orm\Column
     * @var 
     */
    private $postcode;

    /**
     * @orm\Column
     * @var 
     */
    private $web;

	/**
     * @orm\ManyToOne(targetEntity="Place")
     * @orm\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
	private $place;

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

    /**
     * Gets the value of email.
     *
     * @return [type]
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
     *
     * @param [type] $email the email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of place.
     *
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Sets the value of place.
     *
     * @param mixed $place the place
     *
     * @return self
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Gets the value of street.
     *
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the value of street.
     *
     * @param mixed $street the street
     *
     * @return self
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Gets the value of city.
     *
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the value of city.
     *
     * @param mixed $city the city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets the value of postcode.
     *
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Sets the value of postcode.
     *
     * @param mixed $postcode the postcode
     *
     * @return self
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Gets the value of web.
     *
     * @return mixed
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Sets the value of web.
     *
     * @param mixed $web the web
     *
     * @return self
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }
}