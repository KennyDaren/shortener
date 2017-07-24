<?php

namespace Shortener\Filters\Stats;

use DateTime;
use Shortener\Application\Filter\IFilter;

/**
 * Filter for  link stats
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Filters\Stats
 */
class StatsGraphFilter implements IFilter
{
	/** @var array */
	public $linkIds = [];

	/** @var  int */
	public $user;

	/** @var DateTime */
	public $startDate;

	/** @var DateTime */
	public $endDate;

	/**
	 * @return array
	 */
	public function getLinkIds(): array
	{
		return $this->linkIds;
	}

	/**
	 * @param array $linkIds
	 *
	 * @return StatsGraphFilter
	 */
	public function setLinkIds(array $linkIds): StatsGraphFilter
	{
		$this->linkIds = $linkIds;

		return $this;
	}

	/**
	 * @return int|NULL
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param int $user
	 *
	 * @return StatsGraphFilter
	 */
	public function setUser(int $user = NULL): StatsGraphFilter
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param DateTime $startDate
	 *
	 * @return StatsGraphFilter
	 */
	public function setStartDate(DateTime $startDate): StatsGraphFilter
	{
		$this->startDate = $startDate;

		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param DateTime $endDate
	 *
	 * @return StatsGraphFilter
	 */
	public function setEndDate(DateTime $endDate): StatsGraphFilter
	{
		$this->endDate = $endDate;

		return $this;
	}


}