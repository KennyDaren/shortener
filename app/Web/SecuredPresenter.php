<?php
namespace Shortener\Web;

use Shortener\Domain\User\Clients\ClientEntity;
use Shortener\Facade\Users\IClientUserFacade;

/**
 * Secured presenter
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 * @author Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Frontend
 */
class SecuredPresenter extends BasePresenter
{

	/**
	 * @inheritdoc
	 */
	protected function startup()
	{
		parent::startup();

		$user = $this->getUser();

		if ($user->isLoggedIn() === FALSE) {
			$user->logout(TRUE);
			$this->flashMessageLocalized('Nejste přihlášen', self::FLASH_MESSAGE_ERROR);
			$this->redirect(':Frontend:Homepage:default', [
				'backlink' => $this->storeRequest()
			]);
		}
	}
}