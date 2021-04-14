<?php
/*
 * @license: GPL 2, see plugin root XML
 * @author: Valentin Schulte
 * @copyright: Valentin Schulte
 */

defined('_JEXEC') or die('Restricted access');

$controller = JControllerLegacy::getInstance('MetaMWMainContent');

$input = JFactory::getApplication()->input;
//*
$controller->execute($input->getCmd('task'));

/*
$controller->redirect();
//*/

?>
