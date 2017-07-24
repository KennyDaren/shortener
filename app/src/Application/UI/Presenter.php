<?php

namespace Shortener\Application\UI;

use Kdyby\Translation\Translator;
use Shortener\Exception\InvalidStateException;
use Shortener\Templating\LatteHelpers;
use Shortener\Security\Identity\UserIdentity;
use Shortener\Security\UserHolder;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\Permission;
use Nette\Utils\Strings;
use Tracy\ILogger;
use WebLoader\Nette\LoaderFactory;

/**
 * Base presenter
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 */
abstract class Presenter extends \Nette\Application\UI\Presenter
{
	/** @var \Shortener\Configuration\Configuration @inject */
	public $configuration;

	/** @var LatteHelpers  @inject */
	public $helpers;

	/** @var ILogger @inject */
	public $logger;

	/** @var Translator @inject */
	public $translator;

	/** @var string @persistent */
	public $backlink = '';

	/**
	 * @inheritdoc
	 */
	protected function startup()
	{
		/** @noinspection PhpUndefinedFieldInspection */
		$this->getTemplate()->locale = $this->translator->getLocale();
		parent::startup();
	}

	/**
	 * Check permissions for action
	 *
	 * @param $element
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);

		$permission = (array)$element->getAnnotation('security');

		if ($permission) {
			if (count($permission) === 1) {
				$resource = $permission[0];
				$privilege = Permission::ALL;
			} else {
				list($resource, $privilege) = array_values($permission);
			}

			$this->isAllowed($resource, $privilege);
		}
	}

	/**
	 * @return UserIdentity|NULL
	 */
	protected function getUserIdentity()
	{
		return $this->getUser()->getIdentity();
	}

	/**
	 * @return Template
	 * @throws InvalidStateException
	 */
	protected function createTemplate()
	{
		/** @var Template $template */
		$template = parent::createTemplate();

		if ($this->helpers === NULL) {
			throw new InvalidStateException('Please register presenter in config as Service, for autowiring dependencies!, ' . $this->getReflection()->getName());
		}

		$template->getLatte()->addFilter(NULL, callback($this->helpers, 'loader'));

		return $template;
	}

	/**
	 * Check requirement
	 *
	 * @param string $resource
	 * @param string $privilege
	 */
	protected function isAllowed($resource, $privilege = NULL)
	{
		if (!$this->getUser()->isAllowed($resource, $privilege)) {
			$this->flashMessage('Lituji, ale nemáte přístup do této sekce!');
			$this->redirect(':Frontend:Dashboard:default');
		}
	}

	/**
	 * @param string $message
	 * @param string $type
	 *
	 * @return \stdClass
	 */
	protected function flashMessageTrans($message, $type = 'info')
	{
		return $this->flashMessage($this->translator->trans($message), $type);
	}

}