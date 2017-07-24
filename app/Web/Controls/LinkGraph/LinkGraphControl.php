<?php

namespace Shortener\Web\Controls\LinkGraph;

use Shortener\Application\DataSource\IGraphDataSource;
use Shortener\Application\UI\VisualControl;

/**
 * Control for link graphs
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web\Controls\LinkGraph
 */
class LinkGraphControl extends VisualControl
{
	const GRAPH_FLAT = 'flat';
	const GRAPH_BARS = 'bars';
	const GRAPH_DONUT = 'donut';

	/** @var IGraphDataSource */
	private $dataSource;

	/** @var string */
	private $dataSet;

	/** @var int */
	private $id;

	/**
	 * @param IGraphDataSource $dataSource
	 *
	 * @return LinkGraphControl
	 */
	public function setDataSource($dataSource): LinkGraphControl
	{
		$this->dataSource = $dataSource;

		return $this;
	}

	/**
	 * @param string $dataSet
	 *
	 * @return LinkGraphControl
	 */
	public function setDataSet($dataSet): LinkGraphControl
	{
		$this->dataSet = $dataSet;

		return $this;
	}

	/**
	 * Render method
	 */
	public function render()
	{
		$template = $this->getTemplate();
		/** @noinspection PhpUndefinedFieldInspection */
		$template->data = $data = $this->dataSource->getData($this->dataSet, $this->id);
		if (!empty($data)){
			$templateFile = __DIR__ . DIRECTORY_SEPARATOR . ucfirst($data['type']) . '.latte';
			$this->doRender($templateFile);
		}
	}

}