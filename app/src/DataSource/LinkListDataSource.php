<?php

namespace Shortener\DataSource;
use Shortener\Application\DataSource\IDataSource;
use Shortener\Application\Filter\IFilter;
use Shortener\Facade\Link\Doctrine\ILinkFacade;
use Shortener\Filters\Link\LinkFilter;

/**
 * Data source for link list
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\DataSource
 */
class LinkListDataSource implements IDataSource
{
	/** @var ILinkFacade */
	private $linkFacade;

	/** @var  IFilter */
	private $filter;

	public function __construct(LinkFilter $filter, ILinkFacade $linkFacade)
	{

		$this->filter = $filter;
		$this->linkFacade = $linkFacade;
	}

	public function getData(): array
	{
		return $this->linkFacade->find($this->filter)->getResult();
	}
}