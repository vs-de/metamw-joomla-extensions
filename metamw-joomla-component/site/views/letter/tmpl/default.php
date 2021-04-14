<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_metamwmaincontent
 *
 * @copyright   Copyright (C) 2020 Valentin Schulte. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<?php
  $g = $this->g;
  $g->fetch_letter();
?>
<?php
if ($g->has_letter()) {
?>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
  <div id="mmw_letter_container" data-letter_id="<?php echo $g->letter_name ;?>">
    <h2>
    <?php echo $g->letter_name ;?>
    </h2>
    <br/>
    <ul class="nav nav-tabs" id="maintabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#main-tab" role="tab">
          Philologische Version
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#text-tab" role="tab">
          Textversion
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#source-tab" role="tab">
          XML/TEI
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#extended-tab" role="tab">
          Download
        </a>
      </li>
    </ul>
    <div class="tab-content" id="letter_tab_content">
      <div class="tab-pane show active" id="main-tab" role="tabpanel">
        <div id="letter_xml">
          <?php echo $g->letter['xml']; ?>
        </div>
      </div>
      <div class="tab-pane" id="text-tab" role="tabpanel">
        <div id="letter_text">
          <pre style="white-space: pre-wrap">
<?php echo htmlspecialchars($g->letter['default_text_content'], ENT_XML1, 'UTF-8'); ?>
          </pre>
        </div>
      </div>
      <div class="tab-pane" id="source-tab" role="tabpanel">
        <div id="letter_source">
          <pre style="white-space: pre-wrap">
<?php echo htmlspecialchars($g->letter['xml'], ENT_XML1, 'UTF-8'); ?>
          </pre>
        </div>
      </div>
      <div class="tab-pane" id="extended-tab" role="tabpanel">
        <div id="letter_options">
          <a href="/_api/letters/<?php echo $g->letter_name ?>.xml" id="download-letter-xml" class="btn btn-primary" role="button" download="<?php echo $g->letter_name ?>.xml">Download Brief (TEI-XML)</a>
          <a href="/_api/letters/<?php echo $g->letter_name ?>.pdf" id="download-letter-xml" class="btn btn-primary" role="button" download="<?php echo $g->letter_name ?>.pdf">Download Brief (PDF)</a>
        </div>
      </div>
    </div>
  </div>
<?php
} else {
?>
  <div> Brief nicht gefunden. </div>
<?php
}
