<?php

namespace Shortener\Web\User;
use Shortener\Application\UI\Presenter;
use Shortener\DataSource\LinkListDataSource;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Facade\Link\Doctrine\ILinkFacade;
use Shortener\Filters\Link\LinkFilter;
use Shortener\Web\Controls\LinkList\ILinkListControlFactory;

/**
 * Links presenter
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web\User
 */
class LinksPresenter extends Presenter
{

	/** @var  ILinkFacade @inject */
	public $linkFacade;

	/** @var  ILinkListControlFactory @inject */
	public $linkListControlFactory;

	/** @var  LinkListDataSource */
	private $dataSource;


	public function actionList()
	{
		$filter = new LinkFilter();
		$filter->setUser($this->getUser()->getId())
			->setOrderByStatCount(LinkFilter::ORDER_DESC)
			->setStatus(LinkEntity::STATUS_ACTIVE)
			->setMax(10);

		$this->dataSource = new LinkListDataSource($filter, $this->linkFacade);

	}

	protected function createComponentLinkList()
	{
		$control = $this->linkListControlFactory->create();

		$control->setDataSource($this->dataSource);

		return $control;
	}
}