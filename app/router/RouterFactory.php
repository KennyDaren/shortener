<?php
namespace Shortener\Router;

use Shortener\Admin\AdminRouterFactory;
use Shortener\ApiModule\ApiRouterFactory;
use Shortener\Web\WebRouterFactory;
use Nette;
use Nette\Application\Routers\Route;
use Nette\Http\Request;
use Nette\Application\Routers\RouteList;

/**
 * System router factory
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Router
 */
class RouterFactory
{
	/** @var Request */
	private $request;

	/** @var WebRouterFactory */
	private $webRouterFactory;

	/**
	 * RouterFactory constructor.
	 *
	 * @param WebRouterFactory $webRouterFactory
	 * @param Request          $request
	 */
	public function __construct(WebRouterFactory $webRouterFactory, Request $request)
	{
		$this->request = $request;
		$this->webRouterFactory = $webRouterFactory;
	}

	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$flag = ($this->request->isSecured() ? Route::SECURED : Route::$defaultFlags);

		// web router
		$router[] = $this->webRouterFactory->createRouter();

		return $router;
	}
}
