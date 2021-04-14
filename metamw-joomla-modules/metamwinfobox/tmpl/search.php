<?php 
/*
 * MetaMw Joomla info box for SRP
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
*/
// No direct access
defined('_JEXEC') or die; ?>
<?php //$g = $GLOBALS['g']; ?>
<?php $g = MetamwConnector::getInst(); ?>
<!-- <div id="explain_right_box" :class="{'d-none': (ent_list.length == 0)}"> -->
<div id="meta_mw_info_box">
  <h4 v-if="r_pos > 0"> Suchergebnis {{r_pos}}.
  <template v-if="r_ent">
    <a :target="mf.cfg['box_links_blank'] ? '_blank' : null" :href="url_for(r_ent)"> {{r_key}} </a>
  </template>
  <template v-else>
    <a :target="mf.cfg['box_links_blank'] ? '_blank' : null" :href="url_for({cat: r_cat, key: r_key})"> {{r_key}} </a>
  </template>
  </h4>
  <!-- <h4 v-else> Suchergebnis </h4> -->

  <div v-if="spinner_active" class="spinner-cnt text-center">
    <div class="spinner-crcl"></div>
    <div class=""> rekonstruiere Trefferkontext, lade Details ... </div>
  </div>
  <div v-for="(entry, idx) in ent_list" :class="['ent_box' ,{'border-primary border-top': idx > 0}]">
    <div class="show_entity">
      <span class="font-weight-bold"> 
        {{ent_name(entry)}}
      </span>
      <div>
        <em>{{tr_cat_sg(entry.cat)}}</em>
      </div>
      <div v-for="(k_name, k) in tr_fields_for(entry, 'short')">
        <label class="box-label"> {{ k_name }}: </label>
        <span v-if="k == 'key'" class="box-value" v-html="entity_link(entry, show_value(entry, k))"> </span>
        <span v-else="" class="box-value"> {{ show_value(entry, k) }} </span>

      </div>
      <br/>
      <div v-for="hl in entry['hl'].slice(0,2)">
        <div v-for="(hx, key) in hl">
          <div class="snip" v-for="snip in build_snips(hx).slice(0,2)" v-html="snip">
          </div>
        </div>
      </div>
    </div>
    <div class="show_spot">
      <template v-if="entry.spot_hl">
        <div class="snip_list" v-for="shl in entry['spot_hl'].slice(0,2)">
          <div class="snip" v-for="_snip in build_snips(shl).slice(0,2)" v-html="_snip">
          </div>
        </div>
      </template>
      <template v-else>
        <div class="btn btn-sm btn-info m-1 corresp is_receiver" v-if="entry.meta && entry.meta == 'is_receiver'">
          Empfänger
        </div>
        <div class="btn btn-sm btn-info m-1 corresp is_author" v-if="entry.meta && entry.meta == 'is_author'">
          Autor
        </div>
      </template>
    </div>
  </div>
  <div v-if="letter_hl" class="letter_hl">
    <div>Brieftext:</div>
    <div class="ltr_snip" v-for="_snip in build_snips(letter_hl)" v-html="_snip">
    </div>
  </div>
  <?php $g->load_vue_tpl('entity_box'); ?>
</div>
