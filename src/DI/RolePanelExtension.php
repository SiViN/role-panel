<?php

namespace SiViN\RolePanel\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use SiViN\RolePanel\RolePanelTracy;
use Tracy\Bar;

class RolePanelExtension extends CompilerExtension
{
	const PARAM_ROLE_NAMES = 'roleNames';
	const PREFIX = 'rolePanel';
	
	/** @var string */
	private $barService;

	/** @var array */
	public $defaults = [
		self::PARAM_ROLE_NAMES => []
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->defaults;

		if (array_key_exists(self::PARAM_ROLE_NAMES, $this->config)) {
			$config = $this->config;
		}

		$this->barService = $this->getContainerBuilder()->getByType(Bar::class);

		$builder->addDefinition($this->prefix(self::PREFIX))
			->setFactory(RolePanelTracy::class, [ self::PARAM_ROLE_NAMES => $config[self::PARAM_ROLE_NAMES] ]);
	}

	public function afterCompile(ClassType $class)
	{
		$init = $class->getMethods()['initialize'];

		$init->addBody('if ($this->parameters["debugMode"]) $this->getService(?)->addPanel($this->getService(?));', [
			$this->barService, $this->prefix(self::PREFIX)
		]);
	}

}
