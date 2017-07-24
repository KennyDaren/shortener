<?php

namespace Shortener\Application\Filter;

use Nette\Application\Request;
use Nette\Utils\Callback;
use Shortener\Exception\InvalidStateException;

/**
 * Factory from creating filter from request
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\Filter
 */
class RequestFilterFactory
{
	/**
	 * @param Request $request
	 * @param string  $class
	 * @param array   $whitelist
	 *
	 * @return IFilter
	 * @throws InvalidStateException
	 */
	public static function create(Request $request, $class, $whitelist = []): IFilter
	{
		$filter = new $class;

		if (empty($whitelist)) {
			$whitelist = array_keys(get_object_vars($filter));
		}

		foreach ($whitelist as $property) {
			$propertySetter = 'set' . ucfirst($property);
			if (!method_exists($filter, $propertySetter)) {
				throw new InvalidStateException('Setter (%s::%s()) for property "%s" doesn\'t exists', $class, $propertySetter, $property);
			}

			$filter = Callback::invokeArgs([$filter, $propertySetter], [$request->getParameter($property)]);
		}

		return $filter;
	}
}