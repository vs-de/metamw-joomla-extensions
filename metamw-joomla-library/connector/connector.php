<?php
/*
 * @license: GPL 2, see plugin root XML
 * @author: Valentin Schulte
 * @copyright: Valentin Schulte
 */

define('META_MW_NAME', 'mmw');
define('META_MW_PATH', '/mmwlib');

/**
 * Main Connector Library Class
 */
class MetamwConnector {

  static $inst;
  static function getInst()  {
          return self::$inst = self::$inst ?: new MetamwConnector();
  }

  public function set_config($cfg) {
    $this->config = $cfg;
  }

  /* 
   * sets the protag creation entity with the given key if fetchable
   */
  function set_protag_creation($key) {
    $this->fetch_protag_creation($key);
  }
  function fetch_protag_creation($key) {
    $j = $this->api_call('protag_creations', $key);
    if ($j) {
      $this->protag_creation = json_decode($j, true);
    }
    
  }

  /*
   * sets the creation with the given key if fetchable
   */
  function set_creation($key) {
    $this->fetch_creation($key);
  }
  function fetch_creation($key) {
    $j = $this->api_call('creations', $key);
    if ($j) {
      $this->creation = json_decode($j, true);
    }
  }

  /*
   * returns the url that represents the entity, identified by type and key
   */
  function entity_url($type, $key) {
    $type = preg_replace('/_/', '-', $type);
    return '/dashboard/'.$type.'/'.$key;
  }

  /*
   * sets the person entity with the given key if fetchable
   */
  function set_person($key) {
    $this->fetch_person($key);
  }

  function fetch_person($key) {
    $j = $this->api_call('people', $key);
    if ($j) {
      $this->person = json_decode($j, true);
    }
  }
  
  /*
   * sets the place entity with the given key if fetchable
   */
  function set_place($key) {
    $j = $this->api_call('places', $key);
    if ($j) {
      $this->place = json_decode($j, true);
    }
  }
  
  /*
   * sets the letter with the given id if fetchable
   */
  function set_letter($key) {
    //header('Content-Type: application/xhtml+xml');
    $doc = JFactory::getDocument();
    $doc->setMimeEncoding('application/xhtml+xml');
    $this->letter_name = $key;
  }

  function has_letter() {
    return isset($this->letter) && !!$this->letter;
  }

  function fetch_letter() {
    if (isset($this->letter_name)) {
      $j = $this->api_call('letters', $this->letter_name);
      if ($j) {
        $this->letter = json_decode($j, true);
        if ($this->dbg) {
          file_put_contents('/tmp/_last_letter_asw.txt', $j);
          file_put_contents('/tmp/_last_letter.xml', $this->letter['xml']);
        }
      }
    }
  }

  function load_vue_tpl($name) {
    $p = JPATH_SITE.'/libraries'.META_MW_PATH.'/tpls/'.$name.'.vue.html';
    if (!file_exists($p)) {
      echo "warning, tpl file not found: '".$p."'";
    }
    include($p);
  }

  /*
   * returns the base api endpoint url
   */
  function api_base() {
    return $this->config->get('api_base_url');
  }

  /*
   * API connection function
   * API should only be called through this method
   */
  function api_call(...$args) {
    //$url = MMW_API_BASE.implode('/', $args);
    $url = $this->api_base().'/'.implode('/', $args);
    return file_get_contents($url);
  }

  /*
  function init($app) {
    $this->app = $app;
    $router = $app->getRouter();
    $router->attachParseRule([$this, 'parse_route']);
  }
  */


  function __construct() {
          //$this->mId = $GLOBALS['menu_id'];
          //$this->mName = $GLOBALS['menu_name'];
          $this->dbg = array_key_exists('dbg', $_GET) && $_GET['dbg'] == 1;
          $this->js_loaded = false;
          $this->msg = false;
          $this->cnt = false;
          $this->_app_name = null;
          $this->app = JFactory::getApplication();
          //$this->php_loaded = [];
  }

  function app_name() {
          //return $this->mName;
          if (!($this->_app_name)) {
            $this->_app_name = $this->app->getMenu()->getActive()->alias;
          }
          return $this->_app_name;
  }

  /*
   * returns the current mmw-site type, if any
   */
  function get_site_type() {
    $comp = $this->app->input->get('option');
    //$tsk = $this->app->input->getCmd('task');
    if ($comp = 'com_metamwmaincontent') {
      $vw = $this->app->input->get('view');
      if ($vw == 'entity') {
        $ent_type = $this->app->input->get('entity_type');
        $ent_type = str_replace('-', '_', $ent_type);
        return $ent_type;
      }
      return $vw;
    }
    return null;
  }
  /*
  function set_mod_name($s) {
          $this->mod_name = $s;
  }
  function mod_name() {
          return $this->mod_name;
  }
  */
  /*
  function after_body() {
    $path = JPATH_SITE.META_MW_PATH.'/'.$this->app_name().'/after_body.php';
    if (file_exists($path)) {
      require_once($path);
    }
  }
  */

  /*
  function load_php_file($mm) {
    $m_path = JPATH_LIBRARIES.META_MW_PATH.'/'.$this->app_name().'/'.$mm->name.".php";
    $this->msg = "";
    $this->cnt = "";
    if (file_exists($m_path)) {
      ob_start();
      require_once($m_path);
      $this->cnt = ob_get_contents();
      ob_end_clean();
      $this->php_loaded[] = $m_path;
    } else {
      $this->msg = "file '".$m_path."' not found.";
    }
  }
  */

  function has_msg() {
    return !!$this->msg;
  }

  function show_content() {
    echo $this->cnt;
  }

}

/*
 * MMW Module Class
 */
class MMW_Module {
  function __construct($m) {
    $this->g = MetamwConnector::getInst();
    $this->m = $m;
    $this->name = $m->title;
  }

  function comp_name() {
          return implode('_', [$this->g->app_name(),$this->name]);
  }

}


?>
