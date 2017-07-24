<?php

namespace Shortener\Application\Doctrine\Facade;

use Doctrine\ORM\UnitOfWork;
use DoctrineMapper\ArrayAccessEntityMapper;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryBuilder;
use Shortener\Application\Filter\BaseFilter;
use Shortener\Application\Filter\IFilter;
use Shortener\Exception\RecordNotFoundException;

/**
 * Base facade for doctrine
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Facade
 */
abstract class BaseFacade implements ICRUDFacade
{
	/** @var EntityManager */
	private $entityManager;

	/** @var ArrayAccessEntityMapper */
	private $accessEntityMapper;

	public function __construct(EntityManager $entityManager,
	                            ArrayAccessEntityMapper $accessEntityMapper)
	{

		$this->entityManager = $entityManager;
		$this->accessEntityMapper = $accessEntityMapper;
	}

	/**
	 * Save values
	 *
	 * @param array $values
	 *
	 * @return mixed
	 */
	public function save(array $values)
	{
		$class = $this->getClass();
		$entity = new $class;
		$meta = $this->entityManager->getClassMetadata($this->getClass());
		//find primary key andd try get entity for update
		$pk = $meta->getSingleIdentifierFieldName();

		if (isset ($values[$pk]) && !empty ($values[$pk])) {
			$entity = $this->get($values[$pk]);
		}

		$entity = $this->accessEntityMapper->setToEntity($values, $entity);

		//merge if is managed entity
		if ($this->entityManager->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_MANAGED) {
			$entity = $this->entityManager->merge($entity);
		} else {
			$this->entityManager->persist($entity);
		}

		$this->entityManager->flush();

		return $entity;
	}

	/**
	 * Find by id
	 *
	 * @param $id
	 *
	 * @return mixed
	 * @throws RecordNotFoundException
	 */
	public function get($id)
	{
		$entity = $this->getRepository()->find($id);

		if ($entity === NULL) {
			throw new RecordNotFoundException(sprintf('Record with id %s not found!', $id));
		}

		return $entity;
	}

	/**
	 * Removes by id
	 *
	 * @param $id
	 */
	public function remove($id)
	{
		$entity = $this->get($id);

		if ($entity !== NULL) {
			$this->entityManager->remove($entity);
			$this->entityManager->flush();
		}
	}

	/**
	 * Get entity manager
	 *
	 * @return EntityManager
	 */
	protected function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

	/**
	 * Creates query builder
	 *
	 * @param string             $alias
	 * @param IFilter|BaseFilter $filter
	 *
	 * @return QueryBuilder
	 */
	protected function createQueryBuilder($alias, IFilter $filter = NULL): QueryBuilder
	{
		$qb = $this->getRepository()->createQueryBuilder($alias);

		if (!$filter instanceof BaseFilter) {
			return $qb;
		}

		if ($filter->getMax() !== NULL) {
			$qb->setMaxResults($filter->getMax());
		}

		if ($filter->getOffset() !== NULL) {
			$qb->setFirstResult($filter->getOffset());
		}

		if (!empty($filter->getOrder())) {
			foreach ($filter->getOrder() as $column => $order) {
				$qb->addOrderBy($column, $order);
			}
		}

		return $qb;
	}

	/**
	 * Get entity class
	 *
	 * @return string
	 */
	abstract protected function getClass(): string;

	/**
	 * Get repository for entity
	 *
	 * @return EntityRepository
	 */
	abstract protected function getRepository(): EntityRepository;
}
