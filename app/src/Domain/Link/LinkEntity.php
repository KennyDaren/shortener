<?php

namespace Shortener\Domain\Link;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shortener\Application\Doctrine\Entity\IdentifiedStatusEntity;
use Shortener\Domain\Stats\StatsEntity;
use Shortener\Domain\User\UserEntity;

/**
 * Link entity
 *
 * @ORM\Entity(repositoryClass="Shortener\Domain\Link\LinkRepository")
 *
 * @ORM\Table(name="sh_link", indexes={
 *     @ORM\Index(name="hash_idx", columns={"hash"}),
 *     @ORM\Index(name="url_idx", columns={"url"}),
 *     @ORM\Index(name="status_idx", columns={"status"}),
 *     @ORM\Index(name="hash_status_idx", columns={"hash", "status"}),
 *     @ORM\Index(name="url_status_idx", columns={"url", "status"})
 *     })
 * @author Hynek Nerad <iam@kennydaren.me>
 */
class LinkEntity extends IdentifiedStatusEntity
{
	/**
	 * @ORM\Column(name="hash", type="string", length=25, unique=true, nullable=true)
	 * @var string
	 */
	private $hash;

	/**
	 * @ORM\Column(name="url", type="string", length=255)
	 * @var string
	 */
	private $url;

	/**
	 * @ORM\ManyToOne(targetEntity="Shortener\Domain\User\UserEntity", fetch="EXTRA_LAZY")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var UserEntity
	 */
	private $user;

	/**
	 * @ORM\OneToMany(targetEntity="Shortener\Domain\Stats\StatsEntity", fetch="EXTRA_LAZY", mappedBy="link")
	 * @var ArrayCollection|StatsEntity[]
	 */
	private $stats;

	/**
	 * LinkEntity constructor.
	 */
	public function __construct()
	{
		$this->stats = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set hash
	 *
	 * @param string $hash
	 *
	 * @return LinkEntity
	 */
	public function setHash($hash)
	{
		$this->hash = $hash;

		return $this;
	}

	/**
	 * Get hash
	 *
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Set url
	 *
	 * @param string $url
	 *
	 * @return LinkEntity
	 */
	public function setUrl($url)
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * Get url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Set user
	 *
	 * @param UserEntity $user
	 *
	 * @return LinkEntity
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return UserEntity
	 */
	public function getUser()
	{
		return $this->user;
	}
}

