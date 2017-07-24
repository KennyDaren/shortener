<?php

namespace Shortener\Facade\Link\Doctrine;

use Doctrine\ORM\Query\Expr\Join;
use DoctrineMapper\ArrayAccessEntityMapper;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Shortener\Application\Doctrine\Facade\BaseStatusFacade;
use Shortener\Application\Filter\IFilter;
use Shortener\Application\ResultSet\DoctrineResultSet;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Domain\Link\LinkRepository;
use Shortener\Filters\Link\LinkFilter;

/**
 * Doctrine facade for links
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Link
 */
class LinkFacade extends BaseStatusFacade implements ILinkFacade
{
	/** @var  LinkRepository */
	private $repository;

	/**
	 * LinkFacade constructor.
	 *
	 * @param EntityManager           $entityManager
	 * @param ArrayAccessEntityMapper $accessEntityMapper
	 * @param LinkRepository          $linkStatsRepository
	 */
	public function __construct(EntityManager $entityManager,
	                            ArrayAccessEntityMapper $accessEntityMapper,
	                            LinkRepository $linkStatsRepository)
	{
		parent::__construct($entityManager, $accessEntityMapper);
		$this->repository = $linkStatsRepository;
	}

	/**
	 * Get entity class
	 *
	 * @return string
	 */
	protected function getClass(): string
	{
		return LinkEntity::class;
	}

	/**
	 * Get repository for entity
	 *
	 * @return EntityRepository
	 */
	protected function getRepository(): EntityRepository
	{
		return $this->repository;
	}

	/**
	 * Find by filter
	 *
	 * @param IFilter $filter
	 *
	 * @return DoctrineResultSet
	 */
	public function find(IFilter $filter)
	{
		$qb = $this->createQueryBuilder('l', $filter);

		if (!$filter instanceof LinkFilter) {
			return new DoctrineResultSet($qb);
		}

		if ($filter->isAnonymous() === TRUE) {
			$qb->andWhere('l.user IS NULL');
		}

		if (!empty($filter->getOrderByStatCount())) {
			$qb->leftJoin('l.stats', 's', 'l.id = s.link')
				->addSelect('COUNT(s.id) AS statsCount')
				->addOrderBy('statsCount', $filter->getOrderByStatCount())
				->addGroupBy('l.id');
		}

		if ($filter->getUser() !== NULL) {
			$qb->andWhere('l.user = :userId')
				->setParameter('userId', $filter->getUser());
		}

		return new DoctrineResultSet($qb);
	}

	/**
	 * Find by url
	 *
	 * @param string $url
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByUrl(string $url)
	{
		return $this->createQueryBuilder('l')
			->andWhere('l.url = :url')
			->setParameter('url', $url)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * Find by url and user id
	 *
	 * @param string   $url
	 *
	 * @param int|NULL $userId
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByUrlUser(string $url, int $userId = NULL)
	{
		$qb = $this->createQueryBuilder('l')
			->andWhere('l.url = :url')
			->setParameter('url', $url);

		if ($userId === NULL) {
			$qb->andWhere('l.user IS NULL');
		} else {
			$qb->andWhere('l.user = :userId')
				->setParameter('userId', $userId);
		}

		return $qb->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param string $hash
	 *
	 * @return LinkEntity|NULL
	 */
	public function findByHash(string $hash)
	{
		return $this->createQueryBuilder('l')
			->andWhere('l.hash = :hash')
			->setParameter('hash', $hash)
			->getQuery()->getOneOrNullResult();
	}
}