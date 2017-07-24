<?php

namespace Shortener\Web\Controls\Sign\In;

use Kdyby\Translation\Translator;
use Nette\ArrayHash;
use Nette\Forms\Controls\TextInput;
use Nette\Security\AuthenticationException;
use Shortener\Application\UI\VisualControl;
use Shortener\Facade\Users\IUserFacade;
use Nette\Application\UI\Form;
use Shortener\Security\Authenticators\WebAuthenticator;
use Shortener\Templating\LatteHelpers;

/**
 * Build login form
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web\Controls\Sign\In
 */
class SignInControl extends VisualControl
{
	/** @var IUserFacade */
	private $userFacade;
	/** @var WebAuthenticator */
	private $webAuthenticator;

	/**
	 * SignInControl constructor.
	 *
	 * @param LatteHelpers     $latteHelpers
	 * @param Translator       $translator
	 * @param IUserFacade      $userFacade
	 * @param WebAuthenticator $webAuthenticator
	 */
	public function __construct(LatteHelpers $latteHelpers,
	                            Translator $translator,
	                            IUserFacade $userFacade,
	                            WebAuthenticator $webAuthenticator)
	{
		parent::__construct($latteHelpers, $translator);

		$this->userFacade = $userFacade;
		$this->webAuthenticator = $webAuthenticator;
	}

	/**
	 * Render method
	 */
	public function render(){
		$this->doRender();
	}

	/**
	 * Create sign in form
	 *
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->addText('username', 'Username')
			->setRequired('Username is required')
			->setAttribute('placeholder', 'Username');

		$form->addPassword('password')
			->setRequired('Password is required')
			->setAttribute('placeholder', 'Password');

		$form->addSubmit('login', 'Login');

		$control = $this;
		$form->onSuccess[] = function (Form $form, ArrayHash $values) use ($control) {

			try {
				$control->webAuthenticator->login($values->username, $values->password);
			} catch (AuthenticationException $e) {
				/** @var TextInput $userInput */
				$userInput = $form->getComponent('username');
				$userInput->addError('Invalid credentials');

				return;

			}

			$control->processSuccess($form);
		};

		return $form;
	}
}