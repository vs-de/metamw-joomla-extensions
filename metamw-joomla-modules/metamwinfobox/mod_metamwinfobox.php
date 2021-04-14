<?php
/*
 * MetaMw Joomla info box module dispatcher
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
*/
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$cnt = modMetaMwInfoBoxHelper::getTest($params);
$con = MetamwConnector::getInst();
$app = JFactory::getApplication();
$vw = $app->input->get('view');
$ent_type = $app->input->get('entity_type');
//$tsk = $app->input->getCmd('task');
$stype = $con->get_site_type();
$tpl_path = dirname(JModuleHelper::getLayoutPath('mod_metamwinfobox'));
switch($stype) {
  case 'search':
    $tpl = $tpl_path.'/search.php';
    break;
  case 'letter':
    $tpl = $tpl_path.'/letter.php';
    break;
  default:
    $tpl = JModuleHelper::getLayoutPath('mod_metamwinfobox');
}
require($tpl);
?>
