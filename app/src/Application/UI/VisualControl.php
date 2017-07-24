<?php

namespace Shortener\Application\UI;

use Kdyby\Translation\Translator;
use Latte\Template;
use Nette\Application\UI\Control as BaseControl;
use Nette\Utils\Strings;
use Shortener\Exception\InvalidStateException;
use Shortener\Templating\LatteHelpers;

/**
 * Base control class
 *
 * @author Hynek Nerad <iam@kennydaren.me>
 */
abstract class VisualControl extends BaseControl
{
	/** @var LatteHelpers */
	protected $latteHelpers;
	/** @var Translator */
	private $translator;

	/** @var array|callable[] */
	public $onSuccess = [];

	/**
	 * @param LatteHelpers $latteHelpers
	 * @param Translator   $translator
	 */
	public function __construct(LatteHelpers $latteHelpers,
	                            Translator $translator)
	{
		parent::__construct();

		$this->latteHelpers = $latteHelpers;
		$this->translator = $translator;
	}

	/**
	 * Renders visual control, if template is null is rendered file with name of extending class in same directory
	 *
	 * @param string|null $templateFile
	 */
	public function doRender($templateFile = NULL)
	{
		$templateObject = $this->getTemplate();

		if (empty($templateFile)) {
			// short class name
			$reflection = new \ReflectionObject($this);
			$templateFile = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR . $reflection->getShortName();;
		}

		if (Strings::endsWith($templateFile, '.latte') === FALSE) {
			$templateFile .= '.latte';
		}

		$templateObject->setFile($templateFile);
		$templateObject->render();
	}

	/**
	 * Process onSucess callbacks
	 */
	public function processSuccess()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		$this->onSuccess(func_get_args()); // process callbacks via magic of Nette Object
	}

	/**
	 * @inheritdoc
	 */
	protected function createTemplate()
	{
		/** @var Template $template */
		$template = parent::createTemplate();

		if ($this->latteHelpers === NULL) {
			throw new InvalidStateException('Please register control in config as Service, for autowiring dependencies!, ' . $this->getReflection()->getName());
		}

		$template->getLatte()->addFilter(NULL, callback($this->latteHelpers, 'loader'));

		return $template;
	}

	/**
	 * @return Translator
	 */
	protected function getTranslator()
	{
		return $this->translator;
	}
}