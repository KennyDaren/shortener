<?php

namespace Shortener\Application\Filter;

/**
 * Base filter
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Filter
 */
abstract class BaseFilter implements IFilter
{
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';

	/** @var int */
	protected $max;

	/** @var int */
	protected $offset;

	/** @var  array */
	protected $order;

	/**
	 * @return int
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $max
	 *
	 * @return BaseFilter
	 */
	public function setMax($max)
	{
		$this->max = $max;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * @param int $offset
	 *
	 * @return BaseFilter
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param array $order
	 *
	 * @return BaseFilter
	 */
	public function setOrder($order)
	{
		$this->order = $order;

		return $this;
	}

	/**
	 * Adds order
	 *
	 * @param $column
	 * @param $order
	 */
	public function addOrder($column, $order)
	{
		$this->order[$column] = $order;
	}
}