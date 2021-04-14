<?php
/**
 * @copyright   Copyright (C) Valentin Schulte Coding
 * @author      Valentin Schulte
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Component
 *
 */
class MetaMWMainContentViewEntity extends JViewLegacy
{
  static protected $attr_tr = array(
    'key' => 'Key',
    'gnd' => 'GND-Nummer',
    'birth_date' => 'Geboren',
    'baptism_date' => 'Getauft',
    'death_date' => 'Gestorben',
    'gender' => 'Geschlecht',
    'occupations' => 'Tätigkeiten',
    'places' => 'Wirkungsstätten',
    //'background' => 'Kurzbiographie',
    'description' => 'Kurzbiographie',
    'bibl_entries' => 'Bibliographie',
    'web_links' => 'Links',
  );

  static protected $attr_order = [
    'key',
    'gnd',
    '',
    'birth_date',
    'baptism_date',
    'death_date',
    'gender',
    'occupations',
    'places',
    '',
    //'background',
    'description',
    'bibl_entries',
    'web_links',
  ];

  static protected $gender = array(
    'm' => 'männlich',
    'w' => 'weiblich'
  );

  /**
   * Display the view
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  function display($tpl = null)
  {
    // Assign data to the view
    $mwc = MetamwConnector::getInst();
    $p = $mwc->person;
    $this->g = MetamwConnector::getInst();
    // Display the view
    parent::display($tpl);
  }

  protected function expand_list_attr($type, $list) {
    switch($type) {
      case 'occupations':
        return $this->expand_occupations($list, "", "", ', ');
      case 'bibl_entries':
        return $this->field_expand($list, 'content', '<br/>');
      break;
    }
  }

  function field_expand($arr, $field, $join) {
    if ($join === null) {
      $join = ', ';
    }
    foreach($arr as $e) {
      $entries[] = $e[$field];
    }
    return implode($entries, $join); 
    
  }

  function std_expand($arr, $pref, $posf, $join) {
    if ($join === null) {
      $join = ', ';
    }
    foreach($arr as $e) {
      $entries[] = $e;
    }
    return implode($entries, $join); 
  }
  
  protected function expand_occupations($arr, $pref, $posf, $join) {
    /*
    $entries = array_map(function($e) {
      return $pref.$e.$posf;
    }, $arr);
    */
    $entries = [];
    foreach($arr as $e) {
      $entries[] = $e;
    }
    return implode($entries, ', ');
  }
}
