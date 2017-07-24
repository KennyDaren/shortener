<?php

namespace Shortener\Application\Doctrine\Facade;

use Shortener\Application\Filter\IFilter;
use Shortener\Application\ResultSet\IResultSet;
use Shortener\Exception\RecordNotFoundException;

/**
 * Interface for CRUD facades
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Doctrine\Facade
 */
interface ICRUDFacade
{
	/**
	 * Find by filter
	 *
	 * @param IFilter $filter
	 *
	 * @return IResultSet
	 */
	public function find(IFilter $filter);

	/**
	 * Save values
	 *
	 * @param array $values
	 *
	 * @return mixed
	 * @throws RecordNotFoundException
	 */
	public function save(array $values);

	/**
	 * Find by id
	 *
	 * @param $id
	 *
	 * @return mixed
	 * @throws RecordNotFoundException
	 */
	public function get($id);

	/**
	 * Removes by id
	 *
	 * @param $id
	 *
	 * @throws RecordNotFoundException
	 */
	public function remove($id);
}