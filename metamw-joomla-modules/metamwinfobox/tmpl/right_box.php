<?php $g = $GLOBALS['g']; ?>
<!-- <div id="explain_right_box" :class="{'d-none': (ent_list.length == 0)}"> -->
<div id="explain_right_box">
  <h4 v-if="r_pos > 0"> Suchergebnis {{r_pos}}. <a :href="url_for({key: r_key})"> {{r_key}} </a></h4>
  <h4 v-else> Suchergebnis </h4>
  <div v-for="(entry, idx) in ent_list" :class="['ent_box' ,{'border-primary border-top': idx > 0}]">
    <div class="show_entity">
      <span class="font-weight-bold"> 
        {{ent_name(entry)}}
      </span>
      <div>
        <em>{{tr_cat_sg(entry.cat)}}</em>
      </div>
      {{entry.key}}
      <br/>
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
