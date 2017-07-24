<?php

namespace Shortener\Application\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity with status and identifier
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Doctrine\Entity
 */
class IdentifiedStatusEntity extends IdentifiedEntity
{
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_DELETED = 2;

	/**
	 * @ORM\Column(name="status", type="integer", options={"unsigned":TRUE, "default":0})
	 * @var int
	 */
	protected $status = self::STATUS_INACTIVE;

	/**
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 *
	 * @return IdentifiedStatusEntity
	 */
	public function setStatus($status): IdentifiedStatusEntity
	{
		$this->status = $status;

		return $this;
	}
}