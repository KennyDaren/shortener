<?php

namespace Shortener\Filters\Link;

use Shortener\Application\Filter\BaseStatusFilter;

/**
 * Filter for link facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Filters\Link
 */
class LinkFilter extends BaseStatusFilter
{
	/** @var  bool */
	protected $anonymous = FALSE;

	/** @var  string */
	protected $orderByStatCount;

	/** @var  int */
	protected $user;

	/**
	 * @return bool
	 */
	public function isAnonymous(): bool
	{
		return $this->anonymous;
	}

	/**
	 * @param bool $anonymous
	 *
	 * @return LinkFilter
	 */
	public function setAnonymous(bool $anonymous): LinkFilter
	{
		$this->anonymous = $anonymous;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getOrderByStatCount()
	{
		return $this->orderByStatCount;
	}

	/**
	 * @param string $orderByStatCount
	 *
	 * @return LinkFilter
	 */
	public function setOrderByStatCount(string $orderByStatCount = NULL): LinkFilter
	{
		$this->orderByStatCount = $orderByStatCount;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param int $user
	 *
	 * @return LinkFilter
	 */
	public function setUser(int $user = NULL): LinkFilter
	{
		$this->user = $user;

		return $this;
	}
}