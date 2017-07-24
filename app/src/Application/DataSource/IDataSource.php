<?php

namespace Shortener\Application\DataSource;

/**
 * Data source interface
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\DataSource
 */
interface IDataSource
{
	/**
	 * Get result data
	 *
	 * @return array
	 */
	public function getData(): array;
}