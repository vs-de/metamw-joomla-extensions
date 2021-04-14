<?php
/**
 *
 * @copyright   Copyright Valentin Schulte. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<?php
  switch($this->g->get_site_type()) {
    case 'person':
      require('person.php');
    break;
    case 'creation':
      require('creation.php');
    break;
    case 'protag_creation':
      require('protag_creation.php');
    break;
    case 'sight':
      require('sight.php');
    break;
    case 'institution':
      require('institution.php');
    break;
    case 'settlement':
      require('settlement.php');
    break;
    default:
      echo 'unrecognized entity type: '.$this->g->get_site_type();
  }
?>
