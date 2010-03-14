<?php
declare(ENCODING = 'utf-8');
namespace F3\ExtJS\ExtDirect;

/*                                                                        *
 * This script belongs to the FFLOW3 package "ExtJS".                     *
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
 *
 * @version $Id: EmptyView.php 2813 2009-07-16 14:02:34Z k-fish $
 */

/**
 * An Ext Direct transaction
 *
 * @version $Id: EmptyView.php 2813 2009-07-16 14:02:34Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class Transaction {
	/**
	 * The direct request this transaction belongs to
	 *
	 * @var \F3\ExtJS\ExtDirect\DirectRequest
	 */
	protected $directRequest;

	/**
	 * The controller / class to use
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The action / method to execute
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * The arguments to be passed to the method
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The type of the request (currently "rpc" for all remoting requests)
	 *
	 * @var string
	 */
	protected $type = 'rpc';

	/**
	 * The transaction ID to associate with this request
	 *
	 * @var int
	 */
	protected $tid;

	/**
	 * Extended transaction attribute for the package key of the controller
	 *
	 * @var string
	 */
	protected $packageKey;

	/**
	 * Extended transaction attribute for the subpackage key of the controller
	 *
	 * @var string
	 */
	protected $subpackageKey;

	/**
	 *
	 * @param DirectRequest $directRequest The direct request this transaction belongs to
	 */
	public function __construct(\F3\ExtJS\ExtDirect\DirectRequest $directRequest) {
		$this->directRequest = $directRequest;
	}

	public function mapDataToParameters(array $parameters) {
		$arguments = array();
		// TODO Add checks for parameters
		foreach ($parameters as $name => $options) {
			$parameterIndex = $options['position'];
			$arguments[$name] = $this->data[$parameterIndex];
		}
		return $arguments;
	}

	public function getDirectRequest() {
		return $this->directRequest;
	}

	/**
	 * @return string The action
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Set the action
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getTid() {
		return $this->tid;
	}

	public function setTid($tid) {
		$this->tid = $tid;
	}

	public function getPackageKey() {
		return $this->packageKey;
	}

	public function setPackageKey($packageKey) {
		$this->packageKey = $packageKey;
	}

	public function getSubpackageKey() {
		return $this->subpackageKey;
	}

	public function setSubpackageKey($subpackageKey) {
		$this->subpackageKey = $subpackageKey;
	}
}
?>