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
 * An Ext Direct request
 *
 * @version $Id: EmptyView.php 2813 2009-07-16 14:02:34Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class DirectRequest {
	/**
	 * The transactions inside this request
	 *
	 * @var array
	 */
	protected $transactions = array();

	/**
	 * True if this request is a form post
	 *
	 * @var boolean
	 */
	protected $formPost = FALSE;

	/**
	 * True if this request is containing a file upload
	 *
	 * @var boolean
	 */
	protected $fileUpload = FALSE;

	/**
	 * @inject
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	public function getTransactions() {
		return $this->transactions;
	}

	public function addTransaction($action, $method, $data, $tid, $packageKey, $subpackageKey) {
		$transaction = $this->objectManager->create('F3\ExtJS\ExtDirect\Transaction', $this);

		$transaction->setAction($action);
		$transaction->setMethod($method);
		$transaction->setData($data);
		$transaction->setTid($tid);
		$transaction->setPackageKey($packageKey);
		$transaction->setSubpackageKey($subpackageKey);

		$this->transactions[] = $transaction;
	}

	public function isFormPost() {
		return $this->formPost;
	}

	public function setFormPost($formPost) {
		$this->formPost = $formPost;
	}

	public function isFileUpload() {
		return $this->fileUpload;
	}

	public function setFileUpload($fileUpload) {
		$this->fileUpload = $fileUpload;
	}


}
?>