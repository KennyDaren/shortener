<?php

namespace Shortener\Application\DataSource;

/**
 * Graph data source interface
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\DataSource
 */
interface IGraphDataSource
{
	/**
	 * Get result data
	 *
	 * @param string   $dataSet
	 * @param int|NULL $id
	 *
	 * @return array
	 */
	public function getData($dataSet, $id = NULL): array;

}