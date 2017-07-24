<?php

namespace Shortener\Domain\Stats;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Nette\Http\Url;
use Nette\Utils\Validators;
use Shortener\Application\Doctrine\Entity\IdentifiedEntity;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Domain\User\UserEntity;

/**
 * Stats entity
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Domain\Stats
 * @ORM\Entity(repositoryClass="Shortener\Domain\Stats\StatsRepository")
 * @ORM\Table(name="sh_stats", indexes={
 *     @ORM\Index(name="dateTime_ix", columns={"date_time"}),
 *     @ORM\Index(name="dateTime_link_ix", columns={"date_time", "link_id"}),
 *     @ORM\Index(name="dateTime_link_user_ix", columns={"date_time", "link_id", "user_id"}),
 *     @ORM\Index(name="dateTime_user_ix", columns={"date_time", "user_id"}),
 * })
 *
 * @ORM\HasLifecycleCallbacks()
 */
class StatsEntity extends IdentifiedEntity
{
	const DEVICE_MOBILE = 0;
	const DEVICE_PC = 1;
	const DEVICE_TABLET = 2;

	/**
	 * @ORM\Column(type="datetime", name="date_time")
	 * @var DateTime
	 */
	protected $dateTime;

	/**
	 * @ORM\ManyToOne(targetEntity="Shortener\Domain\Link\LinkEntity", fetch="EXTRA_LAZY", cascade={"merge", "persist"},
	 *     inversedBy="stats")
	 * @ORM\JoinColumn(name="link_id", referencedColumnName="id")
	 * @var LinkEntity
	 */
	protected $link;

	/**
	 * @ORM\ManyToOne(targetEntity="Shortener\Domain\User\UserEntity",
	 *     fetch="EXTRA_LAZY", cascade={"merge", "persist"})
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=TRUE)
	 * @var UserEntity
	 */
	protected $user;

	/**
	 * @ORM\Column(type="smallint", name="device", options={"unsigned":TRUE}, nullable=TRUE)
	 * @var int
	 */
	protected $device = NULL;

	/**
	 * @ORM\Column(type="string", name="platform", length=30, nullable=TRUE)
	 * @var string
	 */
	protected $platform = NULL;

	/**
	 * @ORM\Column(type="string", name="browser", length=30, nullable=TRUE)
	 * @var string
	 */
	protected $browser = NULL;

	/**
	 * @ORM\Column(type="string", name="city", length=120, nullable=TRUE)
	 * @var string
	 */
	protected $city = NULL;

	/**
	 * @ORM\Column(type="string", name="country", length=120, nullable=TRUE)
	 * @var string
	 */
	protected $country = NULL;

	/**
	 * @ORM\Column(type="float", name="latitude", scale=10, precision=6, nullable=TRUE)
	 * @var float
	 */
	protected $latitude = NULL;

	/**
	 * @ORM\Column(type="float", name="longitude", scale=10, precision=6, nullable=TRUE)
	 * @var float
	 */
	protected $longitude = NULL;

	/**
	 * @ORM\Column(type="string", name="referer", length=255, nullable=TRUE)
	 * @var string
	 */
	protected $referer = NULL;

	/**
	 * @ORM\Column(type="string", name="refererBaseUrl", length=255, nullable=TRUE)
	 * @var string
	 */
	protected $refererBaseUrl = NULL;

	//data from dateTime property for statistic performance
	/**
	 * @ORM\Column(type="smallint", name="hour", options={"unsigned":TRUE})
	 * @var int
	 */
	protected $hour;

	/**
	 * @ORM\Column(type="smallint", name="week_day", options={"unsigned":TRUE})
	 * @var int
	 */
	protected $weekDay;

	/**
	 * @ORM\PreUpdate()
	 * @ORM\PrePersist()
	 */
	public function preUpdate()
	{
		$this->hour = $this->dateTime->format('H');
		$this->weekDay = $this->dateTime->format('w');

		if ($this->referer !== NULL && Validators::isUrl($this->referer)){
			$this->refererBaseUrl = (new Url($this->referer))->getHost();
		}
	}

	/**
	 * @return DateTime
	 */
	public function getDateTime(): DateTime
	{
		return $this->dateTime;
	}

	/**
	 * @param DateTime $dateTime
	 *
	 * @return StatsEntity
	 */
	public function setDateTime(DateTime $dateTime): StatsEntity
	{
		$this->dateTime = $dateTime;

		return $this;
	}

	/**
	 * @return LinkEntity
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param LinkEntity $link
	 *
	 * @return StatsEntity
	 */
	public function setLink(LinkEntity $link)
	{
		$this->link = $link;

		return $this;
	}

	/**
	 * @return UserEntity|NULL
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param UserEntity $user
	 *
	 * @return StatsEntity
	 */
	public function setUser(UserEntity $user = NULL)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDevice(): int
	{
		return $this->device;
	}

	/**
	 * @param int $device
	 *
	 * @return StatsEntity
	 */
	public function setDevice(int $device = NULL): StatsEntity
	{
		$this->device = $device;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPlatform()
	{
		return $this->platform;
	}

	/**
	 * @param string $platform
	 *
	 * @return StatsEntity
	 */
	public function setPlatform(string $platform = NULL)
	{
		$this->platform = $platform;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBrowser()
	{
		return $this->browser;
	}

	/**
	 * @param string $browser
	 *
	 * @return StatsEntity
	 */
	public function setBrowser(string $browser = NULL)
	{
		$this->browser = $browser;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @param string $city
	 *
	 * @return StatsEntity
	 */
	public function setCity(string $city = NULL): StatsEntity
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param string $country
	 *
	 * @return StatsEntity
	 */
	public function setCountry(string $country = NULL): StatsEntity
	{
		$this->country = $country;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * @param float $latitude
	 *
	 * @return StatsEntity
	 */
	public function setLatitude(float $latitude = NULL): StatsEntity
	{
		$this->latitude = $latitude;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * @param float $longitude
	 *
	 * @return StatsEntity
	 */
	public function setLongitude(float $longitude = NULL): StatsEntity
	{
		$this->longitude = $longitude;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReferer()
	{
		return $this->referer;
	}

	/**
	 * @param string $referer
	 *
	 * @return StatsEntity
	 */
	public function setReferer(string $referer = NULL): StatsEntity
	{
		$this->referer = $referer;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRefererBaseUrl()
	{
		return $this->refererBaseUrl;
	}

	/**
	 * @return int
	 */
	public function getHour(): int
	{
		return $this->hour;
	}

	/**
	 * @param int $hour
	 *
	 * @return StatsEntity
	 */
	public function setHour(int $hour): StatsEntity
	{
		$this->hour = $hour;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getWeekDay(): int
	{
		return $this->weekDay;
	}

	/**
	 * @param int $weekDay
	 *
	 * @return StatsEntity
	 */
	public function setWeekDay(int $weekDay): StatsEntity
	{
		$this->weekDay = $weekDay;

		return $this;
	}
}