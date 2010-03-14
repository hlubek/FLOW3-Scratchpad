<?php
declare(ENCODING = 'utf-8');
namespace F3\ExtJS\ExtDirect;

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
 * Ext Direct request router that dispatches to multiple actions.
 * Currently only JSON requests are fully supported.
 *
 * @version $Id: IncludeViewHelper.php 3736 2010-01-20 15:47:11Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Router {

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
	 * @var \F3\FLOW3\MVC\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @inject
	 * @var \F3\FLOW3\Reflection\ReflectionService
	 */
	protected $reflectionService;


	/**
	 * Handle and route a request to multiple controller actions
	 *
	 * @param \F3\FLOW3\MVC\RequestInterface $request
	 * @return string The JSON encoded result for Ext Direct processing
	 */
	public function handleRequest(\F3\FLOW3\MVC\RequestInterface $request, \F3\FLOW3\MVC\Web\Response $response) {
		$directRequest = $this->parseRequest($request);
		$results = $this->dispatchAndCollectResponse($directRequest);
		return $this->buildResponse($directRequest, $results, $response);
	}

	/**
	 *
	 * @param \F3\FLOW3\MVC\RequestInterface $request The request to parse
	 * @return \F3\ExtJS\ExtDirect\DirectRequest The parsed request
	 */
	protected function parseRequest(\F3\FLOW3\MVC\RequestInterface $request) {
		$directRequest = $this->objectManager->create('F3\ExtJS\ExtDirect\DirectRequest');

		// TODO Don't use Globals here, create method in environment?
		if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
			parseJsonRequest($request, $directRequest);
		} elseif ($request->hasArgument('extAction')) {
			parseFormPostRequest($request, $directRequest);
		} else {
			throw new \F3\ExtJs\ExtDirect\Exception\InvalidExtDirectRequestException('The request is not a valid Ext Direct request', 1268490738);
		}
		return $directRequest;
	}

	/**
	 *
	 * @param \F3\FLOW3\MVC\RequestInterface $request
	 * @param \F3\ExtJS\ExtDirect\DirectRequest $directRequest
	 */
	protected function parseJsonRequest(\F3\FLOW3\MVC\RequestInterface $request, \F3\ExtJS\ExtDirect\DirectRequest $directRequest) {
		// TODO Implement special Ext Direct remoting extended with package keys per transaction
		$packageKey = $request->getArgument('packageKey');
		$subpackageKey = $request->hasArgument('subpackageKey') ? $request->getArgument('subpackageKey') : '';

		$data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
		if (!is_array($data)) {
			$data = array($data);
		}
		foreach ($data as $transactionData) {
			$directRequest->addTransaction(
				$transactionData->action,
				$transactionData->method,
				$transactionData->data,
				$transactionData->tid,
				$packageKey,
				$subpackageKey);
		}
	}

	/**
	 *
	 * @param \F3\FLOW3\MVC\RequestInterface $request
	 * @param \F3\ExtJS\ExtDirect\DirectRequest $directRequest
	 */
	protected function parseFormPostRequest(\F3\FLOW3\MVC\RequestInterface $request, \F3\ExtJS\ExtDirect\DirectRequest $directRequest) {
		$directRequest->setFormPost(TRUE);
		$directRequest->setFileUpload($request->getArgument('extUpload') === 'true');

		$packageKey = $request->getArgument('packageKey');
		$subpackageKey = $request->hasArgument('subpackageKey') ? $request->getArgument('subpackageKey') : '';

		$directRequest->addTransaction(
				$request->getArgument('extAction'),
				$request->getArgument('extMethod'),
				NULL,
				$request->getArgument('extTID'),
				$packageKey,
				$subpackageKey);
	}

	/**
	 *
	 * @param \F3\ExtJS\ExtDirect\DirectRequest $directRequest
	 * @return array The transaction results
	 */
	protected function dispatchAndCollectResponse(\F3\ExtJS\ExtDirect\DirectRequest $directRequest) {
		$results = array();
		foreach ($directRequest->getTransactions() as $transaction) {
			$results[] = $this->dispatchTransaction($transaction);
		}
		return $results;
	}

	/**
	 *
	 * @param \F3\ExtJS\ExtDirect\DirectRequest $directRequest
	 * @param array $results
	 * @return string The Ext Direct response either as JSON or JSON wrapped in HTML for file uploads
	 */
	protected function buildResponse(\F3\ExtJS\ExtDirect\DirectRequest $directRequest, array $results, \F3\FLOW3\MVC\Web\Response $response) {
		$jsonResponse = json_encode(count($results) === 1 ? $results[0] : $results);
		if ($directRequest->isFormPost() && $directRequest->isFileUpload()) {
			$jsonResponse = preg_replace('/&quot;/', '\\&quot;', $jsonResponse);
			return '<html><body><textarea>' . $jsonResponse . '</textarea></body></html>';
		} else {
			$response->setHeader('Content-Type', 'text/javascript');
			return $jsonResponse;
		}
	}

	/**
	 * Dispatch a transaction to the main dispatcher by building intermediate
	 * request and responses.
	 *
	 * @param \F3\ExtJS\ExtDirect\Transaction $transaction
	 * @return void
	 */
	protected function dispatchTransaction(\F3\ExtJS\ExtDirect\Transaction $transaction) {
		$dispatchRequest = $this->buildDispatchRequest($transaction);
		$dispatchResponse = $this->objectManager->create('F3\ExtJS\ExtDirect\Response');

		$this->dispatcher->dispatch($dispatchRequest, $dispatchResponse);

		return array(
			'type' => 'rpc',
			'tid' => $transaction->getTid(),
			'action' => $transaction->getAction(),
			'method' => $transaction->getMethod(),
			'result' => $dispatchResponse->getResult(),
			'success' => $dispatchResponse->getSuccess()
		);
	}

	/**
	 *
	 * @param \F3\ExtJS\ExtDirect\Transaction $transaction
	 * @return F3\FLOW3\MVC\Web\Request A request for dispatching the transaction
	 */
	protected function buildDispatchRequest(\F3\ExtJS\ExtDirect\Transaction $transaction) {
		$dispatchRequest = $this->objectManager->create('F3\FLOW3\MVC\Web\Request');
		$dispatchRequest->injectEnvironment($this->environment);
		$dispatchRequest->setControllerPackageKey($transaction->getPackageKey());
		$dispatchRequest->setControllerSubpackageKey($transaction->getSubpackageKey());
		$dispatchRequest->setControllerName($transaction->getAction());
		$dispatchRequest->setControllerActionName($transaction->getMethod());
		$dispatchRequest->setFormat('extdirect');

		$arguments = $this->getArgumentsFromTransaction($dispatchRequest, $transaction);
		$dispatchRequest->setArguments($arguments);

		return $dispatchRequest;
	}

	/**
	 * Ext Direct does not provide named arguments by now, so we have
	 * to map them by reflecting on the action parameters.
	 *
	 * @param RequestInterface $dispatchRequest
	 * @param Transaction $transaction
	 * @return array The mapped arguments
	 */
	protected function getArgumentsFromTransaction(\F3\FLOW3\MVC\RequestInterface $dispatchRequest, \F3\ExtJS\ExtDirect\Transaction $transaction) {
		if (!$transaction->getDirectRequest()->isFormPost()) {
			$controllerClass = $dispatchRequest->getControllerObjectName();

			$parameters = $this->reflectionService->getMethodParameters($controllerClass, $dispatchRequest->getControllerActionName() . 'Action');
			return $transaction->mapDataToParameters($parameters);
		} else {
			// TODO Reuse setArgumentsFromRawRequestData from Web/RequestBuilder
		}
	}
}

?>