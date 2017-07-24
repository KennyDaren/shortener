<?php
namespace Shortener\Web;

use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;

/**
 * Frontend factory
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Frontend
 */
class WebRouterFactory
{
	/**
	 * @var Request
	 */
	private $request;

	/**
	 * RouterFactory constructor.
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @return IRouter
	 */
	public function createRouter()
	{
		$flag = ($this->request->isSecured() ? Route::SECURED : Route::$defaultFlags);
		$router = new RouteList('Web');

		$router[] = new Route('/<hash>', 'Redirect:default', $flag);
		$router[] = new Route('/<presenter>/<action>[/<id>]', 'Homepage:default', $flag);

		return $router;
	}
}