<?php

namespace Shortener\Web;

use Shortener\Application\UI\Presenter;
use Shortener\DataSource\LinkListDataSource;
use Shortener\Domain\Link\LinkEntity;
use Shortener\Facade\Link\Doctrine\ILinkFacade;
use Shortener\Facade\Users\IUserFacade;
use Shortener\Filters\Link\LinkFilter;
use Shortener\Web\Controls\LinkList\ILinkListControlFactory;
use Shortener\Web\Controls\LinkShortener\ILinkShortenerControlFactory;
use Shortener\Web\Controls\Sign\In\ISignInControlFactory;
use Shortener\Web\Controls\Sign\In\SignInControl;
use Shortener\Web\Controls\Sign\Up\ISignUpControlFactory;
use Shortener\Web\Controls\Sign\Up\SignUpControl;

/**
 * Homepage presenter
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Frontend
 */
class HomepagePresenter extends Presenter
{

	/** @var  ISignInControlFactory @inject */
	public $signInControlFactory;

	/** @var  ISignUpControlFactory @inject */
	public $signUpControlFactory;

	/** @var  IUserFacade @inject */
	public $userFacade;

	/** @var  ILinkShortenerControlFactory @inject */
	public $linkShortenerControlFactory;

	/** @var  ILinkListControlFactory @inject */
	public $linkListControlFactory;

	/** @var  ILinkFacade @inject */
	public $linkFacade;

	/** @var  LinkListDataSource */
	private $dataSource;

	public function actionDefault()
	{
		$filter = new LinkFilter();
		$filter->setOrderByStatCount(LinkFilter::ORDER_DESC)
			->setStatus(LinkEntity::STATUS_ACTIVE)
			->setMax(10);

		if ($this->getUser()->isLoggedIn()){
			$filter->setUser($this->getUser()->getId());
		}else{
			$filter->setAnonymous(TRUE);
		}

		$this->dataSource = new LinkListDataSource($filter, $this->linkFacade);
	}

	/**
	 * @return Controls\LinkShortener\LinkShortenerControl
	 */
	protected function createComponentLinkShortener()
	{
		return $this->linkShortenerControlFactory->create();
	}

	/**
	 * Logout action
	 */
	public function actionLogout()
	{
		$this->getUser()->logout(TRUE);
		$this->redirect('Homepage:default');
	}


	/**
	 * @return SignInControl
	 */
	protected function createComponentLogin()
	{
		$control = $this->signInControlFactory->create();
		$presenter = $this;

		$control->onSuccess[] = function () use ($presenter) {
			$presenter->flashMessageTrans('Succesfully logged in');
			$presenter->redirect('this');
		};

		return $control;
	}

	/**
	 * @return SignUpControl
	 */
	protected function createComponentRegister()
	{
		$control = $this->signUpControlFactory->create();
		$presenter = $this;

		$control->onSuccess[] = function () use($presenter){
			$presenter->flashMessageTrans('Succesfully registered');
			$presenter->redirect('this');
		};

		return $control;
	}

	/**
	 * @return \Shortener\Controls\LinkList\LinkListControl
	 */
	protected function createComponentLinkList()
	{
		$control = $this->linkListControlFactory->create();

		$control->setDataSource($this->dataSource);

		return $control;
	}
}