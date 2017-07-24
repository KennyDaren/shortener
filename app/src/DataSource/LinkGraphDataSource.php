<?php

namespace Shortener\DataSource;

use Shortener\Application\DataSource\IGraphDataSource;
use Shortener\Domain\Stats\StatsEntity;
use Shortener\Exception\InvalidStateException;
use Shortener\Facade\Stats\IStatsFacade;
use Shortener\Filters\Stats\StatsGraphFilter;
use Shortener\Web\Controls\LinkGraph\LinkGraphControl;
use Tracy\Debugger;

/**
 * Data source for graph control
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\DataSource
 */
class LinkGraphDataSource implements IGraphDataSource
{
	const DATASET_REDIRECTS_COUNT = 'redirects_count';
	const DATASET_LAST_7DAYS_REDIRECTS = 'last_week_redirects';
	const DATASET_PLATFORM = 'platform';
	const DATASET_BROWSER = 'browser';
	const DATASET_REFERER = 'referer';
	const DATASET_DEVICE = 'device';

	/** @var IStatsFacade */
	private $statsFacade;

	private $dataSet = NULL;

	private $filter;

	/**
	 * LinkGraphDataSource constructor.
	 *
	 * @param StatsGraphFilter $filter
	 * @param IStatsFacade     $statsFacade
	 */
	public function __construct(StatsGraphFilter $filter, IStatsFacade $statsFacade)
	{

		$this->statsFacade = $statsFacade;
		$this->filter = $filter;
	}

	/**
	 * @param string   $dataSet
	 * @param int|NULL $id
	 *
	 * @return array
	 */
	public function getData($dataSet, $id = NULL): array
	{
		$linkIds = $this->filter->getLinkIds();
		if ($id === NULL && count($linkIds) === 1) {
			$id = reset($linkIds);
		}

		if (isset($this->dataSet[$dataSet]) && isset($this->dataSet[$dataSet][$id])) {
			return $this->dataSet[$dataSet][$id];
		}

		if ($dataSet === NULL || !isset($this->dataSet[$dataSet])) {
			$this->dataSet[$dataSet] = [];
		}

		$this->dataSet[$dataSet] += $this->resolveDataSetMethod($dataSet);

		return array_key_exists($id, $this->dataSet[$dataSet]) ? $this->dataSet[$dataSet][$id] : [];
	}


	/**
	 * Fill data based on data set
	 *
	 * @param $dataSet
	 *
	 * @return array
	 * @throws InvalidStateException
	 */
	private function resolveDataSetMethod($dataSet): array
	{
		$nullModifier = function ($row) {
			if (empty($row['label'])) {
				$row['label'] = 'Unknown';
			}

			return $row;
		};

		switch ($dataSet) {
			//flat
			case self::DATASET_REDIRECTS_COUNT:
				$data = $this->statsFacade->countsBy($this->filter);
				$addData['type'] = LinkGraphControl::GRAPH_FLAT;

				return $this->formatCount($data, $addData);

			//bars
			case self::DATASET_LAST_7DAYS_REDIRECTS:
				$filter = clone $this->filter;

				$endDate = new \DateTime();
				$startDate = (clone  $endDate)->modify('- 7 days');

				$filter->setStartDate($startDate)
					->setEndDate($endDate);

				$data = $this->statsFacade->statsByDay($filter);

				return $this->formatBars($data, ['Redirects'], function ($row) {
					$row['label'] = self::getDayName($row['label']);

					return $row;
				});

			//donut graphs
			case self::DATASET_PLATFORM:
				$data = $this->statsFacade->statsByPlatform($this->filter);

				return $this->formatDonut($data, $nullModifier);

			case self::DATASET_BROWSER:
				$data = $this->statsFacade->statsByBrowser($this->filter);

				return $this->formatDonut($data, $nullModifier);
			case self::DATASET_REFERER:
				$data = $this->statsFacade->statsByRefererBaseUrl($this->filter);

				return $this->formatDonut($data, $nullModifier);
			case self::DATASET_DEVICE:
				$data = $this->statsFacade->statsByDevice($this->filter);

				return $this->formatDonut($data, function ($row) {
					if ($row['label'] === NULL) {
						$row['label'] = 'Unknown';
					} else {
						$row['label'] = self::getDevice($row['label']);
					}

					return $row;
				});
			default:
				throw new InvalidStateException(sprintf('Invalid dataset type, %s given', $dataSet));
		}
	}

	/**
	 * Format data with count
	 *
	 * @param array $data
	 * @param array $addData
	 *
	 * @return array
	 */
	private function formatCount(array $data, array $addData): array
	{
		$returnData = [];

		//fill with 0 values
		foreach ($this->filter->getLinkIds() as $linkId) {
			$returnData[$linkId] = [
					'data' => 0
				] + $addData;
		}

		foreach ($data as $row) {
			$returnData[$row['link']] = [
					'data' => $row['value']
				] + $addData;
		}

		return $returnData;
	}

	/**
	 * Format data for bar graph
	 *
	 * @param array         $data
	 * @param array         $labels
	 *
	 * @param callable|null $modifyRow
	 *
	 * @return array
	 */
	private function formatBars(array $data, array $labels = [], callable $modifyRow = NULL): array
	{
		$addData = [
			'type'   => LinkGraphControl::GRAPH_BARS,
			'xkey'   => 'label',
			'ykeys'  => ['value'],
			'labels' => $labels
		];

		$result = [];
		foreach ($data as $row) {
			if ($modifyRow !== NULL) {
				$row = $modifyRow($row);
			}

			if (!isset($result[$row['link']]) || !isset($result[$row['link']]['data'])) {
				$result[$row['link']]['data'] = [];
			}

			$result[$row['link']]['data'][] = $row;
			$result[$row['link']] += $addData;
		}

		return $result;
	}

	/**
	 *
	 * @param array         $data
	 * @param callable|NULL $modifyRow
	 *
	 * @return array
	 */
	private function formatDonut(array $data, callable $modifyRow = NULL): array
	{
		$result = [];

		foreach ($data as $row) {
			if ($modifyRow !== NULL) {
				$row = $modifyRow($row);
			}

			if (!isset($result[$row['link']]) || !isset($result[$row['link']]['data'])) {
				$result[$row['link']]['data'] = [];
			}

			$result[$row['link']]['data'][] = $row;
			$result[$row['link']] += ['type' => LinkGraphControl::GRAPH_DONUT];
		}

		return $result;
	}

	/**
	 * @param array $addData
	 *
	 * @return array
	 */
	private function fillWithZeroes(array $addData)
	{
		$returnData = [];

		foreach ($this->filter->getLinkIds() as $linkId) {
			$returnData[$linkId] = [
					'data' => [
						[
							'link'  => $linkId,
							'value' => 0
						]
					]
				] + $addData;
		}

		return $returnData;
	}

	/**
	 * Get day name by week day number
	 *
	 * @param $weekDay
	 *
	 * @return string
	 */
	private static function getDayName($weekDay): string
	{
		$dowMap = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

		return $dowMap[$weekDay];
	}

	/**
	 * @param int $deviceInt
	 *
	 * @return string
	 */
	private static function getDevice($deviceInt): string
	{
		$map = [
			StatsEntity::DEVICE_MOBILE => 'Mobile',
			StatsEntity::DEVICE_PC     => 'Desktop',
			StatsEntity::DEVICE_TABLET => 'Tablet',
		];

		return $map[$deviceInt];
	}
}