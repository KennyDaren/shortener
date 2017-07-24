<?php

namespace Shortener\Facade\Stats;

use Shortener\Application\Doctrine\Facade\ICRUDFacade;
use Shortener\Filters\Stats\StatsGraphFilter;

/**
 * Interface for Stats facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Stats
 */
interface IStatsFacade extends ICRUDFacade
{
	/**
	 * Count stat by link ids
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function countsBy(StatsGraphFilter $statsGraphFilter);

	/**
	 * Get daily stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByDay(StatsGraphFilter $statsGraphFilter);

	/**
	 * Platform stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByPlatform(StatsGraphFilter $statsGraphFilter);

	/**
	 * Browser stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByBrowser(StatsGraphFilter $statsGraphFilter);

	/**
	 * Referer stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByRefererBaseUrl(StatsGraphFilter $statsGraphFilter);

	/**
	 * Device stats
	 *
	 * @param StatsGraphFilter $statsGraphFilter
	 *
	 * @return array
	 */
	public function statsByDevice(StatsGraphFilter $statsGraphFilter);
}