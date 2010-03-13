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
 * A transparent view that passes on special values to the Ext Direct response.
 * This view is a singleton because it doesn't hold any state itself.
 *
 * @version $Id: EmptyView.php 2813 2009-07-16 14:02:34Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class View implements \F3\FLOW3\MVC\View\ViewInterface {

	/**
	 * @var \F3\FLOW3\MVC\Controller\Context
	 */
	protected $controllerContext;

	/**
	 * Renders the Ext Direct view, does nothing
	 *
	 * @return string An empty string
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	public function render() {
		return '';
	}

	/**
	 * Sets the current controller context
	 *
	 * @param \F3\FLOW3\MVC\Controller\Context $controllerContext
	 * @return void
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	public function setControllerContext(\F3\FLOW3\MVC\Controller\Context $controllerContext) {
		$this->controllerContext = $controllerContext;
	}

	/**
	 * Add a variable to $this->variables.
	 * Can be chained, so $this->view->assign(..., ...)->assign(..., ...); is possible,
	 *
	 * @param string $key Key of variable
	 * @param object $value Value of object
	 * @return \F3\FLOW3\MVC\View\ViewInterface an instance of $this, to enable chaining.
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 * @api
	 */
	public function assign($key, $value) {
		if ($key === 'result') {
			// TODO throw up if response is not a Ext Direct response
			$this->controllerContext->getResponse()->setResult($value);
		} elseif ($key === 'success') {
			// TODO throw up if response is not a Ext Direct response
			$this->controllerContext->getResponse()->setSuccess($value);
		}
		return $this;
	}

	/**
	 * Add multiple variables to $this->variables.
	 *
	 * @param array $values array in the format array(key1 => value1, key2 => value2).
	 * @return void
	 * @author Bastian Waidelich <bastian@typo3.org>
	 * @api
	 */
	public function assignMultiple(array $values) {
		foreach($values as $key => $value) {
			$this->assign($key, $value);
		}
	}
}
?>