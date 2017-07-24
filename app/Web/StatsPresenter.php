<?php

namespace Shortener\Web;

use Nette\Application\UI\Multiplier;
use Shortener\Application\UI\Presenter;
use Shortener\DataSource\LinkGraphDataSource;
use Shortener\Facade\Link\Doctrine\ILinkFacade;
use Shortener\Facade\Stats\IStatsFacade;
use Shortener\Filters\Stats\StatsGraphFilter;
use Shortener\Web\Controls\LinkGraph\ILinkGraphControlFactory;

/**
 * Presenter for link statistics
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web
 */
class StatsPresenter extends Presenter
{
	/** @var  ILinkGraphControlFactory @inject */
	public $linkGraphControlFactory;

	/** @var  IStatsFacade @inject */
	public $statsFacade;

	/** @var  ILinkFacade @inject */
	public $linkFacade;

	/** @var  LinkGraphDataSource */
	private $dataSource;

	public function actionLink($id)
	{
		$link = $this->linkFacade->findByHash($id);

		$userId = $this->getUser()->isLoggedIn() ? $this->getUser()->getId() : NULL;
		$linkUser = $link->getUser() ? $link->getUser()->getId() : NULL;

		if ($link === NULL || ($linkUser !== NULL && $linkUser !== $userId)) {
			$this->error();
		}

		$filter = new StatsGraphFilter();
		$filter->setLinkIds([$link->getId()]);
		$this->dataSource = new LinkGraphDataSource($filter, $this->statsFacade);

		$template = $this->getTemplate();
		/** @noinspection PhpUndefinedFieldInspection */
		$template->row = $link;
	}

	/**
	 * Multiplier with Link Graph control (pass dataset name)
	 *
	 * @return Multiplier
	 */
	protected function createComponentGraphControl()
	{
		return new Multiplier(function ($dataset) {
			$control = $this->linkGraphControlFactory->create();

			$control->setDataSource($this->dataSource)
				->setDataSet($dataset);

			return $control;
		});
	}
}