<?php /** @noinspection PhpUnusedLocalVariableInspection */

namespace SiViN\RolePanel;

use Nette\Application\Application;
use Nette\Http\IRequest;
use Nette\Http\UrlScript;
use Nette\Security\Identity;
use Nette\Security\Permission;
use Nette\Security\User;
use Tracy\IBarPanel;

class RolePanelTracy implements IBarPanel
{

	/** @var string */
	const PREFIX = 'rp';

	/** @var UrlScript */
	private $url;

	/** @var Application */
	private $application;

	/** @var User */
	private $user;

	/** @var Permission */
	private $permission;

	/** @var IRequest */
	private $request;

	/** @var array */
	private $roleNames;

	public function __construct(array $roleNames, Permission $permission, User $user, IRequest $request, Application $application = NULL) {
		$this->roleNames = $roleNames;
		$this->permission = $permission;
		$this->user = $user;
		$this->request = $request;
		$this->application = $application;
		$this->url = $request->getUrl();

		if ($request->getQuery(self::PREFIX . '-form') === '1'){
			$roles = $request->getQuery(self::PREFIX . '-roles');
			/** @var Identity $identify */
			$identify = $user->getIdentity();
			$identify->setRoles($roles ?: []);
			$this->redirectBack();
		}

		if ($request->getQuery(self::PREFIX . '-logout') === '1'){
			$user->logout(true);
			$this->redirectBack();
		}
	}

	protected function redirectBack()
	{
		$query = $this->url->getQueryParameters();
		$query[self::PREFIX . '-roles'] = null;
		$query[self::PREFIX . '-form'] = null;
		$query[self::PREFIX . '-logout'] = null;
		header('Location: ' . $this->url->setQuery($query));
		exit(1);
	}

	/**
	 * @param string $param
	 * @param mixed $val
	 * @return string
	 */
	public function fastLink($param, $val = '')
	{
		$url = clone $this->url;
		$query = $url->getQueryParameters();
		$query[self::PREFIX . $param] = $val;
		return (string) $url->setQuery($query);
	}

	/**
	 * @inheritDoc
	 */
	function getTab()
	{
		$user = $this->user;

		ob_start();
		require __DIR__ . '/RolePanelTracy.tab.phtml';
		return ob_get_clean();
	}

	/**
	 * @inheritDoc
	 */
	function getPanel()
	{
		$baseUrl = 'abc';
		$prefix = self::PREFIX;
		$user = $this->user;
		$permission = $this->permission;
		$request = $this->request;
		$roleNames = $this->roleNames;

		ob_start();
		require __DIR__ . '/RolePanelTracy.panel.phtml';
		return ob_get_clean();
	}

}
