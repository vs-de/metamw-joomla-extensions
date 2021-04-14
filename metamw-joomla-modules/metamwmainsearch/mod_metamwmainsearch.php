<?php
/*
 * MetaMw Joomla search box module loader
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
*/
defined('_JEXEC') or die;
// Include the syndicate functions only once
//require_once dirname(__FILE__) . '/helper.php';
//$cnt = modMetaMwMainSearchHelper::getTest($params);
$default_layout = JModuleHelper::getLayoutPath('mod_metamwmainsearch');

$modals = dirname($default_layout) . '/modals.php';
require $default_layout;
?>
