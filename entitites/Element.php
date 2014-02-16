<?php

namespace WebCMS\FormModule\Doctrine;

use Doctrine\ORM\Mapping as orm;
use Gedmo\Mapping\Annotation as gedmo;

/**
 * Description of Post
 * @orm\Entity
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Element extends \WebCMS\Entity\Entity {
	
	/**
	 * @orm\Column
	 */
	private $name;
	
	/**
	 * @orm\Column
	 */
	private $label;
	
	/**
	 * @orm\Column(type="text")
	 */
	private $description;
	
	/**
	 * @orm\Column
	 */
	private $type;
	
	/**
	 * @orm\Column(type="boolean")
	 */
	private $required;
	
	/**
	 * @orm\Column(nullable=true)
	 */
	private $value;
	
	/**
	 * @orm\ManyToOne(targetEntity="WebCMS\Entity\Page")
	 * @orm\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $page;
	
	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getType() {
		return $this->type;
	}

	public function getRequired() {
		return $this->required;
	}

	public function getPage() {
		return $this->page;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setRequired($required) {
		$this->required = $required;
	}

	public function setPage($page) {
		$this->page = $page;
	}
}