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
 * A Ext Direct specific response implementation with raw content for json encodable results
 *
 * @version $Id: Response.php 3345 2009-10-22 17:22:44Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 * @scope prototype
 */
class Response extends \F3\FLOW3\MVC\Web\Response {

	/**
	 * The Ext Direct result that will be JSON encoded
	 *
	 * @var mixed
	 */
	protected $result;

	/**
	 * The Ext Direct success code. Defaults to TRUE.
	 *
	 * @var boolean
	 */
	protected $success = TRUE;

	/**
	 * @param mixed $result The result of the called action
	 * @return void
	 */
	public function setResult($result) {
		$this->result = $result;
	}

	/**
	 * @param boolean $success The success of the called action
	 * @return void
	 */
	public function setSuccess($success) {
		$this->success = $success;
	}


	/**
	 * @return mixed The result
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * @return boolean The success
	 */
	public function getSuccess() {
		return $this->success;
	}
}
?>