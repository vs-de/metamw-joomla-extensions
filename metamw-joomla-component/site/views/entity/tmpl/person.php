<?php
/**
 * @copyright   Copyright (C) Valentin Schulte Coding
 * @author      Valentin Schulte
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
$g = $this->g;
if (isset($g->person)) { ?>
<h2>
  <?php echo $g->person['name']; ?>
</h2>

<div id="person-container" data-key="<?php echo $g->person['key']?>">
  <ul class="nav nav-tabs" id="maintabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#main-tab" role="tab">
        Biographie
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#corresp-tab" role="tab">
        Korrespondenz
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" data-toggle="tab" href="#contact-tab" role="tab">
        Briefkontakte
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#mention-tab" role="tab">
        Erwähnungen
      </a>
    </li>
  </ul>
  <div class="tab-content" id="person_tab_content">
    <div class="tab-pane show active" id="main-tab" role="tabpanel">
      <?php echo $g->person['name']; ?>
      <?php
        echo '<ul class="list-unstyled">';
          //foreach ($g->person as $k => $v) {
          foreach (static::$attr_order as $k) {
            //if (!empty($v) && $k != 'data') {
            //if (!empty($v)) {
            if ($k == "") {
              echo '<li>&nbsp;</li>';
            } else {
              $v = $g->person[$k];
              if ($k == 'gender' && !empty($v)) {
                $v = static::$gender[$v];
              }
              //if ($v instanceof Traversable) {
              if (is_array($v)) {
                $v = $this->expand_list_attr($k, $v);
              }
              echo '<li>';
                echo '<label class="font-weight-bold">';
                  echo static::$attr_tr[$k].":";
                echo "</label> &nbsp; ";
                echo "<span>";
                echo "$v";
                echo "</span>";
              echo '</li>';
              //echo "<br/>";
            }
            //}
          }
        echo '</ul>';
      ?>

    </div>
    <div class="tab-pane" id="corresp-tab" role="tabpanel">
      Sämtliche Korrespondenzen mit der Person
      <div id="corresp-tab-cnt">
        <div v-if="!has_loaded('corresp')" class="spinner-cnt text-center">
          <div class="spinner-crcl"></div>
        </div>
        <table class="table">
          <template v-for="(entry, idx) in r.corresp">
            <tr class="">
              <td colspan="2" class="border-top border-primary">
                {{idx+1}}. {{ entry.title }}
              </td>
            </tr>
            <tr >
              <td class="font-weight-bold">
                Incipit:
              </td>
              <td>
                {{ entry.incipit }}
              </td>
            </tr>
            <tr>
              <td>
                <label class="font-weight-bold">
                  Provenienz:
                </label>
              </td>
              <td>
              <span>
                {{ null }}
              </span>
              </td>
            </tr>
            <tr>
              <td>
              <label class="font-weight-bold">
                Schlagwörter:
              </label>
              </td>
              <td>
              <span>
                {{ entry.tags }}
              </span>
              </td>
            </tr>
            <tr>
              <td>
              <label class="font-weight-bold">
                Brief:
              </label>
              </td>
              <td>
              <span>
                <a :href="url_for(entry)">
                  {{ entry.key }}
                </a>
              </span>
              </td>
            </tr>
          </template>
        </table>
        <!--
        <ul class="list-unstyled">
          <li v-for="entry in r.corresp">
            <label class="font-weight-bold">
              Incipit:
            </label>
            <span>
              {{ entry.incipit }}
            </span>
            <label class="font-weight-bold">
              Provenienz:
            </label>
            <span>
              {{ null }}
            </span>
            <label class="font-weight-bold">
              Schlagwörter:
            </label>
            <span>
              {{ entry.tags }}
            </span>
            <label class="font-weight-bold">
              Brief:
            </label>
            <span>
              <a :href="'/dashboard/letter/'+entry.key">
                {{ entry.key }}
              </a>
            </span>
          </li>
        </ul>
        -->
      </div>
    </div>
    <div class="tab-pane" id="contact-tab" role="tabpanel">
      Sämtliche Korrespondenzpartner der Person
      <div id="contact-tab-cnt">
        <div v-if="false && !has_loaded('contact')" class="spinner-cnt text-center">
          <div class="spinner-crcl"></div>
        </div>
        <!--
          <div class="spinner-border d-inline-block">
            <span class="sr-only">Lade...</span>
          </div>
        -->
      </div>
    </div>
    <div class="tab-pane" id="mention-tab" role="tabpanel">
      Sämtliche Erwähnungen der Person in Briefen
      <div id="mention-tab-cnt">
        <div v-if="!has_loaded('mention')" class="spinner-cnt text-center">
          <div class="spinner-crcl"></div>
        </div>
      
        <table class="table">
          <template v-for="(entry, idx) in r.mention">
            <tr class="">
              <td colspan="2" class="border-top border-primary">
                {{idx+1}}. {{ entry.title }}
              </td>
            </tr>
            <tr >
              <td class="font-weight-bold">
                Incipit:
              </td>
              <td>
                {{ entry.incipit }}
              </td>
            </tr>
            <tr>
              <td>
                <label class="font-weight-bold">
                  Provenienz:
                </label>
              </td>
              <td>
              <span>
                {{ null }}
              </span>
              </td>
            </tr>
            <tr>
              <td>
                <label class="font-weight-bold">
                  Stellen:
                </label>
              </td>
              <td>
                <div v-for="spot in entry.spots" v-html="spot">
                </div>
              </td>
            </tr>
            <tr>
              <td>
              <label class="font-weight-bold">
                Schlagwörter:
              </label>
              </td>
              <td>
              <span>
                {{ entry.tags }}
              </span>
              </td>
            </tr>
            <tr>
              <td>
              <label class="font-weight-bold">
                Brief:
              </label>
              </td>
              <td>
              <span>
                <a :href="'<?php echo $g->config['base_path'].$g->config['letter_path'].'/'?>'+entry.key">
                  {{ entry.key }}
                </a>
              </span>
              </td>
            </tr>
          </template>
        </table>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>
  Dashboard Person
<?php } ?>
