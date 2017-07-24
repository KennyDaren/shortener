<?php

namespace Shortener\Application\Filter;

/**
 * Base status filter
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Filter
 */
abstract class BaseStatusFilter extends BaseFilter
{
	/** @var  int */
	protected $status;

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 *
	 * @return BaseStatusFilter
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}
}