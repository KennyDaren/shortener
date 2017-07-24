<?php
namespace Shortener\Exception;

/**
 * @author Hynek Nerad <iam@kennydaren.me>
 */
class InvalidStateException extends BaseException
{
	/**
	 * @param string $message
	 */
	public function __construct($message)
	{
		parent::__construct($message, 500);
	}
} 