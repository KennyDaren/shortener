<?php

namespace Shortener\Application\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;

/**
 * Entity with identifier
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Doctrine\Entity
 */
class IdentifiedEntity extends Object
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(name="id", type="integer", options={"unsigned":TRUE})
	 * @var int
	 */
	protected $id;

	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
}