<?php

namespace WebCMS\GuestbookModule\Doctrine;

use Doctrine\ORM\Mapping as orm;
use Gedmo\Mapping\Annotation as gedmo;

/**
 * Description of Post
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Example extends \AdminModule\Doctrine\Entity {
	
	/**
	 * @orm\Column
	 */
	private $name;
	
	/**
	 * @orm\Column
	 */
	private $email;
	
	/**
	 * @orm\Column
	 */
	private $phone;
	
	/**
	 * @orm\Column(type="text")
	 */
	private $post;
	
	/**
	 * @orm\Column(type="text", nullable=true)
	 */
	private $postResponse;
	
	/**
	 * @orm\ManyToOne(targetEntity="AdminModule\Page")
	 * @orm\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $page;
	
	/**
	 * @gedmo\Timestampable(on="create")
	 * @orm\Column(type="datetime")
	 */
	private $date;
	
	public function getName() {
		return $this->name;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPhone() {
		return $this->phone;
	}

	public function getPost() {
		return $this->post;
	}

	public function getPage() {
		return $this->page;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
	}

	public function setPost($post) {
		$this->post = $post;
	}

	public function setPage($page) {
		$this->page = $page;
	}
	
	public function getPostResponse() {
		return $this->postResponse;
	}

	public function getDate() {
		return $this->date;
	}

	public function setPostResponse($postResponse) {
		$this->postResponse = $postResponse;
	}

	public function setDate($date) {
		$this->date = $date;
	}
}