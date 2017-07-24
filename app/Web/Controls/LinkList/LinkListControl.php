<?php

namespace Shortener\Controls\LinkList;

use Shortener\Application\DataSource\IDataSource;
use Shortener\Application\UI\VisualControl;

/**
 * Control for listing links
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Controls\LinkList
 */
class LinkListControl extends VisualControl
{
	/** @var  IDataSource */
	private $dataSource;

	public function render()
	{
		$template = $this->getTemplate();
		/** @noinspection PhpUndefinedFieldInspection */
		$template->data = $this->dataSource->getData();

		$this->doRender();
	}

	/**
	 * @param IDataSource $dataSource
	 *
	 * @return LinkListControl
	 */
	public function setDataSource($dataSource): LinkListControl
	{
		$this->dataSource = $dataSource;

		return $this;
	}
}