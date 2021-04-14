<?php
// modal tpl
<<<HERE
<div class="modal" id="TEMPLATE_ROOT_ID">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">TEMPLATE TITLE</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="list-unstyled" style="margin: 10px">
          <li>
          </li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-primary">Übernehmen</button>
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>-->
      </div>
    </div>
  </div>
</div>
HERE
?>
<div class="modal" id="facet_qs_modal">
  <div class="modal-dialog h-75 modal-lg">
    <div class="modal-content h-100 w-100">
      <div class="modal-header">
        <h5 class="modal-title">Einschränken auf {{facet_names[cur_lv]}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text" v-model="lv_q" v-on:keyup.enter="lv_search(1)" id="lv_search_q" name="lv_search_q"></input>
        <button class="btn btn-outline-info" v-on:click="lv_search(1)" >Suchen</button>
        <p>
        <button class="btn btn-outline-info btn-sm" v-on:click="lv_select_all">Alle auswählen</button>
        <button class="btn btn-outline-info btn-sm" v-on:click="lv_deselect_all">Alle entfernen</button>
        </p>
        <div class="no-lv-results" v-if="lv_res.length == 0">
          keine Einträge gefunden.
        </div>
        <ul class="list-unstyled" id="qs_results" style="margin: 10px">
          <li v-for="res in lv_res">
            <div class="w-50 d-inline-block">
              <input type="checkbox" :id="'lv_res'+res['key']" v-model="selected_facets[cur_lv]" :value="res"> 
              <label style="width: 93%" class="" :for="'lv_res'+res['key']" >{{ shorten(res['name'], 80) }} </label>
            </div>
            <div class="d-inline-block" v-if="res['cat'] == 'settlement' && res['country_name']"> {{res['country_name']}}  </div>
            <div class="d-inline-block" v-if="res['authors']"> {{res['authors'][0].last_name}}, {{res['authors'][0].first_name}} </div>
            <div style="width: 22%" class="d-inline-block" v-if="res['settlement']">{{res['settlement'].name}}</div>
            <div class="d-inline-block w-25" v-if="res['kind']">{{res['kind']}}</div>
          </li>
        </ul>
      </div>
      <div class="modal-footer">
        <div class="pagination text-center w-50" v-if="lv_page">
          <span v-if="lv_page > 1" v-on:click="lv_search(1)" class="pag_left_dpl_arrow btn bg-primary text-light"> &lt;&lt; </span>
          <span v-else class="pag_left_dbl_arrow btn bg-dark text-light"> &lt;&lt; </span>
          &nbsp;
          &nbsp;
          <span v-if="lv_page > 1" v-on:click="lv_search(lv_page-1)" class="pag_left_arrow btn bg-primary text-light"> &lt; </span>
          <span v-else class="pag_left_arrow btn bg-dark text-light"> &lt; </span>
          &nbsp;
          <span class="pag_page">{{lv_page}}</span>/
          <span class="pag_page">{{lv_pages}}</span>
          &nbsp;
          &nbsp;
          <span v-if="lv_page < lv_pages" v-on:click="lv_search(lv_page+1)" class="pag_right_arrow btn bg-primary text-light"> &gt; </span>
          <span v-else class="pag_right_arrow btn bg-dark text-light"> &gt; </span>
          &nbsp;
          &nbsp;
          <span v-if="lv_page < lv_pages" v-on:click="lv_search(lv_pages)" class="pag_right_dbl_arrow btn bg-primary text-light"> &gt;&gt; </span>
          <span v-else class="pag_right_dbl_arrow btn bg-dark text-light"> &gt;&gt; </span>
          
        </div>
        <button type="button" data-dismiss="modal" class="btn btn-primary">Übernehmen</button>
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>-->
      </div>
    </div>
  </div>
</div>
