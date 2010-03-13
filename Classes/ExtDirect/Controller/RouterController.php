<?php
declare(ENCODING = 'utf-8');
namespace F3\ExtJS\ExtDirect\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "ExtJS".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Ext Direct router controller
 *
 * @version $Id: IncludeViewHelper.php 3736 2010-01-20 15:47:11Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
use F3\FLOW3\MVC\Controller;

class RouterController extends \F3\FLOW3\MVC\Controller\ActionController {

	/**
	 * @inject
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @inject
	 * @var \F3\FLOW3\Utility\Environment
	 */
	protected $environment;

	/**
	 * @inject
	 * @var \F3\FLOW3\MVC\Web\RequestBuilder
	 */
	protected $requestBuilder;	

	/**
	 * @inject
	 * @var \F3\FLOW3\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @inject
	 * @var \F3\FLOW3\MVC\Dispatcher
	 */
	protected $dispatcher;

	protected $isForm = FALSE;

	protected $isUpload = FALSE;

	protected $packageKey;
	
	protected $subpackageKey;
	
	public function indexAction() {
		$this->parseRequest();
		return $this->dispatchAndCollectResponse();
	}

	protected function parseRequest() {
		$request = $this->getControllerContext()->getRequest();
		$this->packageKey = $request->getArgument('packageKey');
		$this->subpackageKey = $request->hasArgument('subpackageKey') ? $request->getArgument('subpackageKey') : '';
		
		// TODO Don't use Globals here
		if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			$this->data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
		} elseif ($request->hasArgument(extAction)) {
			$this->isForm = TRUE;
			$this->isUpload = $request->getArgument('extUpload') === 'true';

			$this->data = new stdClass();
			$this->data->action = $request->getArgument('extAction');
			$this->data->method = $request->getArgument('extMethod');
			$this->data->tid = $request->getArgument('extTID');
			// TODO Set correct data for form post / upload
			$this->data['data'] = array('POST', 'FILES');
		} else {
			throw new \F3\ExtJs\ExtDirect\Exception\InvalidExtDirectRequestException('The request is not a valid Ext Direct request', 1268490738);
		}
	}

	protected function dispatchAndCollectResponse() {
		if (is_array($this->data)) {
			$response = array();
			foreach ($this->data as $directCall) {
				$directCall->packageKey = $this->packageKey;
				$directCall->subpackageKey = $this->subpackageKey;
				$response[] = $this->dispatchDirectCall($directCall);
			}
		} else {
			$response = $this->dispatchDirectCall($this->data);
		}

		if ($this->isForm && $this->isUpload) {
			// TODO Encode HTML with form
		} else {
			$this->getControllerContext()->getResponse()->setHeader('Content-Type', 'text/javascript');
			return json_encode($response);
		}
	}

	protected function dispatchDirectCall($directCall) {
		$controllerName = $directCall->action;
		$controllerAction = $directCall->method;

		$request = $this->objectManager->create('F3\FLOW3\MVC\Web\Request');
		$request->injectEnvironment($this->environment);
		$request->setControllerPackageKey($directCall->packageKey);
		$request->setControllerSubpackageKey($directCall->subpackageKey);
		$request->setControllerName($controllerName);
		$request->setControllerActionName($controllerAction);
		$request->setFormat('extdirect');
		
		// TODO Set controller arguments from directCall->data by using reflection to map indexed array to argument names
		
		$controllerClass = $request->getControllerObjectName();
		$parameters = $this->reflectionService->getMethodParameters($controllerClass, $this->request->getControllerActionName() . 'Action');

		$arguments = array();
		// TODO Add checks
		foreach ($parameters as $name => $options) {
			$parameterIndex = $options['position'];
			$arguments[$name] = $directCall->data[$parameterIndex];
		}
		$request->setArguments($arguments);
		
		$response = $this->objectManager->create('F3\ExtJS\ExtDirect\Response');

		$this->dispatcher->dispatch($request, $response);
		
		return array(
			'type' => 'rpc',
			'tid' => $directCall->tid,
			'action' => $directCall->action,
			'method' => $directCall->method,
			'result' => $response->getResult(),
			'success' => $response->getSuccess()
		);
	}
}

?>