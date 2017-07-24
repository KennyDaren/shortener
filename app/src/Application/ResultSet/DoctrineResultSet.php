<?php

namespace Shortener\Application\ResultSet;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\QueryBuilder;
use Shortener\Application\Filter\IFilter;

/**
 * Result set for doctrine queries
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\ResultSet
 */
class DoctrineResultSet implements IResultSet
{
	/** @var Paginator */
	private $paginator;

	/** @var QueryBuilder */
	private $queryBuilder;

	/** @var  array */
	private $data = NULL;

	/**
	 * ResultSet constructor.
	 *
	 * @param QueryBuilder $queryBuilder
	 */
	public function __construct(QueryBuilder $queryBuilder)
	{
		$this->queryBuilder = $queryBuilder;
		$this->paginator = new Paginator($queryBuilder);
	}

	/**
	 * Returns result
	 *
	 * @return array
	 */
	public function getResult(): array
	{
		$this->initialize();

		return $this->data;
	}

	/**
	 * Returns count of rows
	 *
	 * @return int
	 */
	public function count(): int
	{
		return $this->paginator->count();
	}

	/**
	 * Loads data
	 */
	private function initialize()
	{
		if ($this->data === NULL) {
			$qb = clone $this->queryBuilder;
			$data = $qb->getQuery()->getResult();

			if (empty($data)) {
				$this->data = [];

				return;
			}

			$this->data = $data;
		}
	}
}