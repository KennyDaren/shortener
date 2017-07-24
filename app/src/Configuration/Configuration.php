<?php

namespace Shortener\Configuration;

use Nette\Object;

/**
 * Configuration (< convection)
 *
 * @package Shortener\Configuration
 * @author  Hynek Nerad <iam@kennydaren.me>
 */
class Configuration extends Object
{
	/** @var array block of configuration */
	private $config;

	/**
	 * @param array $configuration
	 */
	public function __construct(array $configuration)
	{
		$this->config = $configuration;
	}

	/**
	 * Minimum password length
	 *
	 * @return int
	 */
	public function getPasswordMinLength(): int
	{
		return $this->config['passwordMinLength'] ?? 6;
	}

	/**
	 * Get characters for generating url hashes
	 *
	 * @return string
	 */
	public function getUrlHashCharacters(): string
	{
		return $this->config['urlHashCharacters'];
	}

	public function getLinkCacheExpiration(): string
	{
		return $this->config['linkCacheExpiration'] ?? '+ 4 hours';
	}

	/**
	 * List of IPs which are user while development instead of localhost
	 * @return array
	 */
	public function getDevLocalIps(): array {
		return $this->config['devLocalIPs'];
	}
}