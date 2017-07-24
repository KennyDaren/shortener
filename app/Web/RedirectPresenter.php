<?php

namespace Shortener\Web;

use Nette\Application\UI\Presenter;
use Shortener\Exception\RecordNotFoundException;
use Shortener\Facade\Link\LinkFacade;

/**
 * Presenter for performing redirects
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web
 */
class RedirectPresenter extends Presenter
{
	/** @var  LinkFacade @inject */
	public $linkFacade;

	/**
	 * @param string $hash
	 */
	public function actionDefault($hash)
	{
		try {
			$url = $this->linkFacade->getRedirect($hash, $this->getHttpRequest());
		} catch (RecordNotFoundException $e) {
			$this->error();

			return;
		}

		$this->redirectUrl($url);
	}
}