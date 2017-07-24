<?php

namespace Shortener\Web\Controls\LinkShortener;

use Kdyby\Translation\Translator;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Security\User;
use Nette\Utils\Random;
use Nette\Utils\Validators;
use Shortener\Application\UI\VisualControl;
use Shortener\Facade\Link\ILinkFacade;
use Shortener\Templating\LatteHelpers;

/**
 * List shortener control
 * with simple ajax call token secure element
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Web\Controls\LinkShortener
 */
class LinkShortenerControl extends VisualControl
{
	/** @var  SessionSection */
	private $sessionSection;
	/** @var ILinkFacade */
	private $linkFacade;
	/** @var User */
	private $user;
	/** @var Request */
	private $request;

	public function __construct(LatteHelpers $latteHelpers,
	                            Translator $translator,
	                            Session $session,
	                            ILinkFacade $linkFacade,
	                            User $user,
	                            Request $request)
	{
		parent::__construct($latteHelpers, $translator);

		$this->sessionSection = $session->getSection(self::class);
		$this->linkFacade = $linkFacade;
		$this->user = $user;
		$this->request = $request;
	}

	/**
	 * Render method
	 */
	public function render()
	{

		$template = $this->getTemplate();
		/** @noinspection PhpUndefinedFieldInspection */
		$template->token = $this->generateToken();

		$this->doRender();
	}

	/**
	 * Handle for shorting url
	 *
	 * @param string $url
	 * @param string $token
	 */
	public function handleShort($url, $token)
	{
		if ($this->getToken() !== $token) {
			$this->getPresenter('Failed, try again');
			$this->getPresenter()->redirect('this');

			return;
		}

		$userId = $this->user->isLoggedIn() ? $this->user->getId() : NULL;

		if (!Validators::isUrl($url)) {
			$url = 'http://' . $url;
		}

		$payload = $this->getPresenter()->getPayload();

		if (!Validators::isUrl($url)) {
			$payload->status = 'URL is not valid';
			$this->getPresenter()->sendPayload();

			return;
		}

		$payload->status = 'Copy and enjoy!';

		$link = $this->linkFacade->create($url, $userId);
		$payload->url = $this->request->getUrl()->getBaseUrl() . $link->getHash();
		$this->getPresenter()->sendPayload();
	}

	/**
	 * Generate and save token to session
	 *
	 * @return string
	 */
	private function generateToken(): string
	{
		$token = Random::generate(32);
		$this->sessionSection->offsetSet('token', $token);
		$this->sessionSection->setExpiration('5 minutes', 'token');

		return $token;
	}

	/**
	 * Get token from session
	 *
	 * @return string
	 */
	private function getToken(): string
	{
		$token = $this->sessionSection->offsetGet('token');

		return !empty($token) ? $token : '';
	}
}