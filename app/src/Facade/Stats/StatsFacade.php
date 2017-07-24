<?php

namespace Shortener\Facade\Stats;

use Doctrine\ORM\Query\Expr\OrderBy;
use DoctrineMapper\ArrayAccessEntityMapper;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Shortener\Application\Doctrine\Facade\BaseFacade;
use Shortener\Application\Filter\IFilter;
use Shortener\Application\ResultSet\DoctrineResultSet;
use Shortener\Domain\Stats\StatsEntity;
use Shortener\Domain\Stats\StatsRepository;
use Shortener\Filters\Stats\StatsGraphFilter;

/**
 * Facade for Stats
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Stats
 */
class StatsFacade extends BaseFacade implements IStatsFacade
{
	/** @var  StatsRepository */
	private $repository;

	/**
	 * StatsFacade constructor.
	 *
	 * @param EntityManager           $entityManager
	 * @param ArrayAccessEntityMapper $accessEntityMapper
	 * @param StatsRepository         $statsRepository
	 */
	public function __construct(EntityManager $entityManager,
	                            ArrayAccessEntityMapper $accessEntityMapper,
	                            StatsRepository $statsRepository
	)
	{
		parent::__construct($entityManager, $accessEntityMapper);
		$this->repository = $statsRepository;
	}

	/**
	 * Get entity class
	 *
	 * @return string
	 */
	protected function getClass(): string
	{
		return StatsEntity::class;
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
		$qb = $this->createQueryBuilder('s', $filter);

		return new DoctrineResultSet($qb);
	}

	/**
	 * Count stat by link ids
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function countsBy(StatsGraphFilter $statsGraphFilter)
	{
		$q =  $this->prepareStatsQB($statsGraphFilter)
			->addSelect('COALESCE(COUNT(s), 0) as value');

		return $q->getQuery()->getResult();
	}

	/**
	 * Get daily stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByDay(StatsGraphFilter $statsGraphFilter)
	{
		$q = $this->prepareStatsQB($statsGraphFilter)
			->addSelect('s.weekDay AS label')
			->addSelect('COUNT(s.weekDay) AS value')
			->addGroupBy('s.weekDay')
			->addOrderBy('label', 'ASC');


		return $q->getQuery()->getResult();
	}

	/**
	 * Platform stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByPlatform(StatsGraphFilter $statsGraphFilter)
	{
		return $this->getValueCount('platform', $statsGraphFilter);
	}

	/**
	 * Browser stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByBrowser(StatsGraphFilter $statsGraphFilter)
	{
		return $this->getValueCount('browser', $statsGraphFilter);
	}

	/**
	 * Referer stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByRefererBaseUrl(StatsGraphFilter $statsGraphFilter)
	{
		return $this->getValueCount('refererBaseUrl', $statsGraphFilter);
	}

	/**
	 * Device stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByDevice(StatsGraphFilter $statsGraphFilter)
	{
		return $this->getValueCount('device', $statsGraphFilter);
	}

	/**
	 * Value count stats for column
	 *
	 * @param string           $column
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	private function getValueCount(string $column, StatsGraphFilter $statsGraphFilter)
	{
		return $this->prepareStatsQB($statsGraphFilter)
			->addSelect("s.{$column} AS label")
			->addSelect("COUNT(s.{$column}) AS value")
			->addGroupBy("s.{$column}")
			->addOrderBy('label', 'ASC')
			->getQuery()
			->getResult();
	}

	private function prepareStatsQB(StatsGraphFilter $statsGraphFilter)
	{
		$qb = $this->createQueryBuilder('s')
			->select('IDENTITY(s.link) as link')
			->addGroupBy('s.link');

		if (!empty($statsGraphFilter->getLinkIds())) {
			$qb->andWhere('s.link IN (:ids)')
				->setParameter('ids', $statsGraphFilter->getLinkIds());
		}

		if ($statsGraphFilter->getUser() !== NULL) {
			$qb->andWhere('s.user = :user')
				->setParameter('user', $statsGraphFilter->getUser());
		}

		if ($statsGraphFilter->getStartDate() !== NULL) {
			$qb->andWhere('s.dateTime >= :startDate')
				->setParameter('startDate', $statsGraphFilter->getStartDate());
		}

		if ($statsGraphFilter->getEndDate() !== NULL) {
			$qb->andWhere('s.dateTime <= :endDate')
				->setParameter('endDate', $statsGraphFilter->getEndDate());
		}

		return $qb;
	}
}