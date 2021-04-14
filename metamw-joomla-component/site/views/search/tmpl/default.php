<?php
/**
 * @package     vsc meta mw
 * @subpackage  com_metamwmaincontent
 *
 * @copyright   Copyright (C) 2020 Valentin Schulte. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div id="meta_mw_search_results">
  
  <ul class="nav nav-tabs" id="maintabs" role="tablist" v-if="query_id">
    <template v-for="(list, cat, idx) in ord_res()" class="list-group" v-if="list">
      <li class="nav-item">
        <a :class="['nav-link', { ' DISactive': idx === 0 }]" data-toggle="tab" :href="'#'+cat+'-tab'" role="tab">
          {{tr_cat(cat)}} ({{list ? list.count : 0}})
        </a>
      </li>
    </template>
  </ul>
  <div class="tab-content" id="search_result_content">
    <div v-if="spinner_active" class="spinner-cnt text-center">
      <div class="spinner-crcl"></div>
    </div>
    <div v-for="(info, cat, idx) in ord_res()" :class="['tab-pane',{ 'active show': idx === 0 }]" :id="cat+'-tab'" role="tabpanel" v-if="info && !spinner_active">
      <span :id="'result_'+cat"></span>
      <template v-if="info.count > 0">
        <span class="font-bold">{{tr_cat(cat)}}:</span>
        <!--
        &nbsp;<a :target="cfg['sr_links_blank'] ? '_blank' : null" :href="reg_url(cat, query_id)" :title="tr_cat(cat)+' in Registeransicht zeigen'">=&gt;</a>
        -->
      </template>
      <template v-else>
        <span> Ihre Suchanfrage hat in dieser Kategorie keine Treffer.
      </template>
      <ul class="list-group">
        <li v-for="(entry, e_idx) in info.entries" :class="['list-group-item', { hl: hl_num == e_idx}]" v-on:click="explain(cat, entry, e_idx)">
          <template v-if="!['letter', 'note'].includes(cat)">
            <button v-if="has_sel_facet(entry)" style="top: 1px;right: 1px;" class="position-absolute btn btn-info btn-sm" v-on:click.prevent.stop="remove_facet(entry)" title="Facette entfernen"> - </button>
            <button v-else style="top: 1px;right: 1px;" class="position-absolute btn btn-outline-info btn-sm" v-on:click.prevent.stop="add_facet(cat, entry)" title="als Facette hinzufügen"> + </button>
          </template>
          <span class="res_pos"> {{r_pos(cat, e_idx)}}. </span>
          <span class=" _ml-2 _d-inline-block" v-if="entry.key" class="r_key alert-info font-italic"> <a :target="cfg['sr_links_blank'] ? '_blank' : null" :href="url_for(entry)"> {{entry.key}} </a> </span> <br v-if="entry.key"/>
          <span v-if="entry.name" class="r_name"> {{entry.name}} </span><br/ v-if="entry.name">
          <template v-for="desc in entry.desc">
            <span class="r_desc"> {{desc}} </span><br>
          </template>
          <div class="snip_box">
          <template v-for="snip in entry.excps">
            <span v-html="'...'+snip+'...'" class="r_excp border"></span>
            <br/>
          </template>
          </div>
        </li>
      </ul>
      <div class="pagination text-center w-100 d-block" v-if="info.count > 0">
        <span v-if="r[cat].page > 1" v-on:click="paginate(1, cat)" class="pag_left_dpl_arrow btn bg-primary text-light"> &lt;&lt; </span>
        <span v-else class="pag_left_dbl_arrow btn bg-dark text-light"> &lt;&lt; </span>
        &nbsp;
        &nbsp;
        <span v-if="r[cat].page > 1" v-on:click="paginate(r[cat].page-1, cat)" class="pag_left_arrow btn bg-primary text-light"> &lt; </span>
        <span v-else class="pag_left_arrow btn bg-dark text-light"> &lt; </span>
        &nbsp;
        <span class="pag_page">{{r[cat].page}}</span>/
        <span class="pag_page">{{r[cat].pages}}</span>
        &nbsp;
        &nbsp;
        <span v-if="r[cat].page < r[cat].pages" v-on:click="paginate(r[cat].page+1, cat)" class="pag_right_arrow btn bg-primary text-light"> &gt; </span>
        <span v-else class="pag_right_arrow btn bg-dark text-light"> &gt; </span>
        &nbsp;
        &nbsp;
        <span v-if="r[cat].page < r[cat].pages" v-on:click="paginate(r[cat].pages, cat)" class="pag_right_dbl_arrow btn bg-primary text-light"> &gt;&gt; </span>
        <span v-else class="pag_right_dbl_arrow btn bg-dark text-light"> &gt;&gt; </span>
        
      </div>
    </div>
  </div>
</div>
