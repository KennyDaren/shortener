<?php

namespace Shortener\Application\Doctrine\Facade;

use Kdyby\Doctrine\QueryBuilder;
use Shortener\Application\Doctrine\Entity\IdentifiedStatusEntity;
use Shortener\Application\Filter\BaseStatusFilter;
use Shortener\Application\Filter\IFilter;
use Shortener\Exception\RecordNotFoundException;

/**
 * Base facade for entities with status
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Doctrine\Facade
 */
abstract class BaseStatusFacade extends BaseFacade
{
	/**
	 * @inheritdoc
	 */
	public function get($id)
	{
		/** @var IdentifiedStatusEntity $entity */
		$entity = parent::get($id);

		if ($entity->getStatus() === $entity::STATUS_DELETED) {
			throw new RecordNotFoundException(sprintf('Record with id %s not found!', $id));
		}

		return $entity;
	}

	/**
	 * @inheritdoc
	 */
	public function remove($id)
	{
		/** @var IdentifiedStatusEntity $entity */
		$entity = $this->get($id);

		$entity->setStatus(IdentifiedStatusEntity::STATUS_DELETED);

		$this->getEntityManager()->merge($entity);
		$this->getEntityManager()->flush();
	}

	/**
	 * Creates query builder
	 *
	 * @param string                   $alias
	 * @param IFilter|BaseStatusFilter $filter
	 *
	 * @return QueryBuilder
	 */
	protected function createQueryBuilder($alias, IFilter $filter = NULL): QueryBuilder
	{
		$qb = parent::createQueryBuilder($alias, $filter);

		if ($filter instanceof BaseStatusFilter && $filter->getStatus() !== NULL) {
			$qb->andWhere("{$alias}.status = :status")
				->setParameter('status', IdentifiedStatusEntity::STATUS_ACTIVE);
		} else {
			$qb->andWhere("{$alias}.status <> :deletedStatus")
				->setParameter('deletedStatus', IdentifiedStatusEntity::STATUS_DELETED);
		}

		return $qb;
	}

}