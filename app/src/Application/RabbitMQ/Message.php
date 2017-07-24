<?php

namespace Shortener\Application\RabbitMQ;

use DateTime;
use Nette\Object;
use Nette\Reflection\Property;
use Nette\Utils\Json;

/**
 * Abstract message value object for RabbitMQ
 *
 * NOTE: Class properties must be accessible!
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Application\RabbitMQ
 */
abstract class Message extends Object
{
	/**
	 * Serialize object to JSON
	 *
	 * @return string
	 */
	public function serialize(): string
	{
		$reflection = $this->getReflection();
		$data = [];

		/** @var Property $property */
		foreach ($reflection->getProperties() as $property) {

			$value = $property->getValue($this);

			if ($value instanceof Message) {
				$value = $value->serialize();
			} elseif ($value instanceof DateTime) {
				$value = $value->format(DATE_ISO8601);
			} elseif (is_object($value)) {
				$value = Json::encode($value);
			}

			$data[$property->getName()] = $value;
		}

		return Json::encode($data);

	}

	/**
	 * Fill object with given data in JSON
	 *
	 * @param string $data
	 *
	 * @return $this
	 */
	public function unserialize(string $data)
	{
		$array = Json::decode($data, Json::FORCE_ARRAY);

		$reflection = $this->getReflection();
		/** @var Property $property */
		foreach ($reflection->getProperties() as $property) {
			if (array_key_exists($property->getName(), $array)) {
				$property->setValue($this, $array[$property->getName()]);
			}
		}

		return $this;
	}
}