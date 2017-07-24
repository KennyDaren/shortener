<?php

namespace Shortener\Web\Controls\Sign\Up;

use Nette\Forms\Controls\TextInput;
use Shortener\Application\UI\VisualControl;
use Shortener\Configuration\Configuration;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Shortener\Exception\EmailExistsException;
use Shortener\Exception\UsernameExistsException;
use Shortener\Facade\Users\IUserFacade;
use Shortener\Security\Roles;
use Shortener\Templating\LatteHelpers;

/**
 * Build login form
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Forms\Sign\In
 */
class SignUpControl extends VisualControl
{
	/** @var Configuration */
	private $configuration;
	/** @var IUserFacade */
	private $userFacade;

	/**
	 * RegisterForm constructor.
	 *
	 * @param LatteHelpers  $latteHelpers
	 *
	 * @param Translator    $translator
	 * @param Configuration $configuration
	 *
	 * @param IUserFacade   $userFacade
	 *
	 * @internal param Configuration $configuration
	 */
	public function __construct(LatteHelpers $latteHelpers,
	                            Translator $translator,
	                            Configuration $configuration,
	                            IUserFacade $userFacade)
	{
		parent::__construct($latteHelpers, $translator);
		$this->configuration = $configuration;
		$this->userFacade = $userFacade;
	}

	/**
	 * Render method
	 */
	public function render()
	{
		$this->doRender();
	}

	/**
	 * Build filter
	 *
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$container = new Form();

		$passMinLen = $this->configuration->getPasswordMinLength();

		$container->addText('email')
			->setAttribute('placeholder', 'Email')
			->addRule(Form::EMAIL, 'Email is not valid');

		$container->addText('username')
			->setAttribute('placeholder', 'Username')
			->setRequired('Username is not valid');

		$container->addPassword('password')
			->setAttribute('placeholder', 'Password')
			->addRule(Form::MIN_LENGTH, 'Password too short, atleast %s character', $passMinLen);

		$container->addPassword('password2')
			->setAttribute('placeholder', 'Password verify')
			->addRule(Form::EQUAL, 'Passwords are not same', $container['password']);

		$container->addSubmit('submit', ('Sign up'));

		$control = $this;
		$container->onSuccess[] = function (Form $form) use ($control) {
			try {
				$values = $form->getValues(TRUE);
				$values['roles'] = [Roles::CLIENT];
				$control->userFacade->create($values);
			} catch (EmailExistsException $e) {
				/** @var TextInput $emailInput */
				$emailInput = $form->getComponent('email');
				$emailInput->addError('Email already exists');

				return;
			} catch (UsernameExistsException $e) {
				/** @var TextInput $usernameInput */
				$usernameInput = $form->getComponent('username');
				$usernameInput->addError('Username already exists');

				return;
			}

			$this->processSuccess();
		};

		return $container;
	}


}