<?php

namespace Shortener\Templating;

use Shortener\Domain\Link\LinkEntity;
use Nette\Application\LinkGenerator;
use Nette\Object;
use Nette\Utils\Callback;

/**
 * Templating latte helpers
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 */
class LatteHelpers extends Object
{
	/** @var LinkGenerator */
	private $linkGenerator;

	/**
	 * LatteHelpers constructor.
	 *
	 * @param LinkGenerator $linkGenerator
	 */
	public function __construct(LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * Call helper
	 *
	 * @param string $filter
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public function loader($filter, $value)
	{
		if (method_exists(__CLASS__, $filter)) {
			$args = func_get_args();
			array_shift($args);

			return Callback::invokeArgs([$this, $filter], $args);
		}
	}

	/**
	 * @param LinkEntity $linkEntity
	 *
	 * @return string
	 */
	public function linkDetailLink(LinkEntity $linkEntity)
	{
		return $this->linkGenerator->link('Web:Stats:link', ['id' => $linkEntity->getHash()]);
	}
}