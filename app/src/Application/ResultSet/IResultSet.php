<?php

namespace Shortener\Application\ResultSet;

/**
 * Interface for result sets
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\ResultSet
 */
interface IResultSet
{
	/**
	 * Returns result of query
	 *
	 * @return array|\Iterator
	 */
	public function getResult();

	/**
	 * Returns count of rows
	 *
	 * @return int
	 */
	public function count();
}