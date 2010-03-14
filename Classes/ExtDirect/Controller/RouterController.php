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
 * TODO Check if we can avoid ActionController at all by going directly through a special request handler
 *
 * @version $Id: IncludeViewHelper.php 3736 2010-01-20 15:47:11Z k-fish $
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class RouterController extends \F3\FLOW3\MVC\Controller\ActionController {

	/**
	 * @inject
	 * @var \F3\ExtJS\ExtDirect\Router
	 */
	protected $router;

	protected function resolveView() {
		$this->view = $this->objectManager->get('F3\FLOW3\MVC\View\EmptyView');
	}

	/**
	 * The router entry point
	 * @return void
	 */
	public function indexAction() {
		$request = $this->getControllerContext()->getRequest();
		$response = $this->getControllerContext()->getResponse();
		return $this->router->handleRequest($request, $response);
	}
}

?>