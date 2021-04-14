<?php

/*
 * MetaMw Joomla plugin core
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
*/

require(dirname(__FILE__).'/config.php');
class plgSystemMetamw extends JPlugin {

  protected $autoloadLanguage = true;
  protected $app;
  protected $active;

  /*
   * route dispatcher for letter and entity sites
   */
  function parse_route($router, $uri) {
    $p = $uri->getPath();

    //person
    $base = $this->mmw_con->config['base_path'];
    $re = '/'.$this->mmw_con->config['person_path'].'\/(PSN[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_person($key);
      return;
    }
    //letter
    $re = '/'.$this->mmw_con->config['letter_path'].'\/((fmb|gb)-18[0-9]{2}-[01][0-9]-[0-3][0-9]-[01][0-9])/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1]; //beware
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_letter($key);
      return;
    }
    // creation
    $re = '/'.$this->mmw_con->config['creation_path'].'\/(CRT[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_creation($key);
      return;
    }
    // protag creation
    $re = '/'.$this->mmw_con->config['protag_creation_path'].'\/(PRC[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_protag_creation($key);
      return;
    }
    // sight
    $re = '/'.$this->mmw_con->config['sight_path'].'\/(SGH[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_place($key);
      return;
    }
    // institution
    $re = '/'.$this->mmw_con->config['institution_path'].'\/(NST[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_place($key);
      return;
    }
    // settlement
    $re = '/'.$this->mmw_con->config['settlement_path'].'\/(STM[0-9]{7})/';
    if (preg_match($re, $p, $matches)) {
      $key = $matches[1];
      $np = str_replace('/'.$key, '', $p);
      $uri->setPath($np);
      $this->mmw_con->set_place($key);
      return;
    }
  }

  public function onAfterInitialise() {
    JLoader::registerPrefix('MetaMw', JPATH_LIBRARIES.'/mmwlib');
    $this->mmw_con = MetaMwConnector::getInst();
    $this->mmw_con->set_config($this->params);
    //$this->app = JFactory::getApplication();
    $router = $this->app->getRouter();
    $router->attachParseRule([$this, 'parse_route']);

  }

  public function onAfterRender() {
    //$doc = JFactory::getDocument();
    //$doc->addScript('/media/plg_metamw/js/vendor/vue.js');
    //$doc->addScript('/media/plg_metamw/js/search.js');
    //$app = JFactory::getApplication();
    $scripts = [];

    //$vt = $this->get_mmw_view_type();
    $vt = $this->mmw_con->get_site_type();
    if ($vt) {
      $p = '/media/plg_metamw/js/'.$vt.'.js';
      //if (file_exists(JX.$p))
      $scripts []= $p;
    }
    /*
    if ($this->get_mmw_view_type() == 'search') {
        $scripts []= '/media/plg_metamw/js/search.js';
    }*/

    if ($this->app->isClient('site')) {
      $html = $this->app->getBody();
      $str = "";
      foreach($scripts as $s) {
        $str .= '<script src="' . $s . '"></script>';
      }
      $str .= '<script type="text/javascript"> window.mf.add_cfg('.json_encode($this->mmw_con->config).');</script>';
      //basically //https://stackoverflow.com/questions/3835636/php-replace-last-occurrence-of-a-string-in-a-string
      $bd = '</body>';
      $pos = strrpos($html, $bd);
      if ($pos !== false) {
        $html = substr_replace($html, $str.$bd, $pos, strlen($bd));
      }
      if ($vt == 'letter') {
        $html = $this->transformBodyForLetter($html);
      }
      $this->app->setBody($html);
    }
  }

  private function transformBodyForLetter($html) {
    $xhtml = preg_replace('/(<img.*?)>/', '$1/>', $html);
    return $xhtml;
  }

  //see https://stackoverflow.com/questions/22052827/how-do-i-stop-joomla-from-including-jquery
  public function onBeforeCompileHead() {
      /*
      // Front end
      if ($this->app instanceof JApplicationSite) {
          $doc = JFactory::getDocument();
          $search = array(
              'jui/js/',
              'system/js/'
          );
          foreach ($doc->_scripts as $key => $script) {
              foreach ($search as $findme) {
                  if (stristr($key, $findme) !== false) {
                      unset($doc->_scripts[$key]);
                  }
              }
          }
      }
      */
    //if ($this->app instanceof JApplicationSite) {
    $doc = JFactory::getDocument();
    $head_data = $doc->getHeadData();
    $scripts = $head_data['scripts'];
    unset($scripts[JUri::root(true) . '/media/jui/js/jquery-noconflict.js']);
    $head_data['scripts'] = $scripts;
    $doc->setHeadData($head_data);

    //$app = JFactory::getApplication();
    //$str = print_r($app->input->get('task'), true);
    
  }

  public function onBeforeRender() {
    $doc = JFactory::getDocument();
    //if ($app->isClient('site')) {

    //$doc->addScript('/media/plg_metamw/js/vendor/jquery-3.4.1.min.js');
    //$doc->addScript('/media/plg_metamw/js/vendor/jquery-ui.min.js');
    $doc->addScript('/media/plg_metamw/js/vendor/vue.js');
    $doc->addScript('/media/plg_metamw/js/vendor/vue-resource.js');
    $doc->addScript('/media/plg_metamw/js/vendor/vuejs-datepicker.js');
    $doc->addScript('/media/plg_metamw/js/vendor/vuejs-datepicker-de.js');
    $doc->addScript('/media/plg_metamw/js/vendor/vue-scrollto.js');
    $doc->addScript('/media/plg_metamw/js/mmw.js');
    //$doc->addScript('/media/plg_metamw/js/search.js');
    $stype = $this->mmw_con->get_site_type();
    if ($stype == 'letter') {
      $doc->addStyleSheet('/media/plg_metamw/css/custom_letter.css');
    }
    $doc->addStyleSheet('/media/plg_metamw/css/spin.css');
    $doc->addStyleSheet('/media/plg_metamw/css/custom.css');
  }

  /*
  function onAfterRoute() {
    $f = fopen('/tmp/jml_plg_route', 'a');
    //var_dump($head_data);
    fwrite($f, "route\n");
    //$app = JFactory::Application();
    $str = print_r($app->input, true);
    $str .= "=(OPTION)=\n";
    $str .= print_r($app->input->get('option'), true);
    fwrite($f, $str);
    fclose($f);
  }
  //*/
    
  /*
  function onAfterDispatch() {
    $f = fopen('/tmp/jml_plg_disp', 'a');
    //var_dump($head_data);
    fwrite($f, "dispatch\n");
    $app = JFactory::getApplication();
    $str = print_r($app->input, true);
    $str .= "=(OPTION)=\n";
    $str .= print_r($app->input->get('option'), true);
    fwrite($f, $str);
    fclose($f);
  }
  //*/

  function get_mmw_view_type() {
    if ($this->is_mmw_com_active()) {
      return $this->app->input->get('view');
    } else {
      return false;
    }
  }

  function is_mmw_com_active() {
    if ($this->active) {
      return true;
    }
    $inp = $this->app->input;
    if (isset($inp)) {
      $opt = $inp->get('option');
      $this->active = $opt == 'com_metamwmaincontent';
      return $this->active;
    }
  }
}
?>
