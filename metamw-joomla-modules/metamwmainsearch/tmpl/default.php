<?php 
/*
 * MetaMw Joomla search box
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
*/
// No direct access
defined('_JEXEC') or die; ?>
<div id="meta_mw_main_search_box">
<?php require_once($modals); ?>
  Suche
  <input class="mb-1" placeholder="Textsuche" type="text" v-model="q" v-on:keyup.enter="do_search()" id="search_q" name="search_q"></input>
  <div class="d-inline mt-1">
    <input type="checkbox" id="chkFmb" name="use_fmb" value="settlement" v-model="use_fmb"></input>
    <label title="Briefe von F.M.B." class="mr-1" for="chkFmb"> FMB</label>
    <input type="checkbox" id="chkGb" name="use_gb" value="settlement" v-model="use_gb"></input>
    <label title="Gegenbriefe (an F.M.B.)" class="" for="chkGb"> GB</label>
  </div>
  <button class="btn btn-outline btn-info" style="float: right" v-on:click="do_search()" >Suchen</button>
  <p>
    <!-- <br/> -->
  </p>
  <h5> Ressourcen </h5>
  <ul class="list-unstyled" id="search_opts_list">
    <li>
      <input type="checkbox" id="chkLetterData" name="letter_data" value="letter_data" v-model="sel_letter_cats"></input>
      <label class="w-75" for="chkLetterData"> Briefe </label>
    </li>
    <li>
      <ul class="list-unstyled">
        <li>
          <input type="checkbox" id="chkLetter" name="letter" value="letter" v-model="selCats"></input>
          <label class="w-75" for="chkLetter"> Brieftexte </label>
        </li>
        <li>
          <input type="checkbox" id="chkMeta" name="meta" value="meta" v-model="selCats"></input>
          <label class="w-75" for="chkMeta"> Metadaten </label>
        </li>
        <li>
          <input type="checkbox" id="chkNote" name="note" value="note" v-model="selCats"></input>
          <label class="w-75" for="chkNote"> Kommentare </label>
        </li>
      </ul>
    </li>
    <li>
      <input type="checkbox" id="chkPerson" name="person" value="person" v-model="selCats"></input>
      <label class="w-75" for="chkPerson"> Personen </label>
    </li>
    <li>
      <input type="checkbox" id="chkCreation" name="creation" value="creation" v-model="selCats"></input>
      <label class="w-75" for="chkCreation"> Werke </label>
    </li>
    <li>
      <input type="checkbox" id="chkProtagCreation" name="protag_creation" value="protag_creation" v-model="selCats"></input>
      <label class="w-75" for="chkProtagCreation"> FMB-Werke </label>
    </li>
    <li>
      <input type="checkbox" id="chkPlace" name="place" value="place" v-model="sel_place_cats"></input>
      <label class="w-75" for="chkPlace"> Orte </label>
    </li>
    <li>
      <ul class="list-unstyled">
        <li>
          <input type="checkbox" id="chkInstitution" name="institution" value="institution" v-model="selCats"></input>
          <label class="w-75" for="chkInstitution"> Institutionen </label>
        </li>
        <li>
          <input type="checkbox" id="chkSight" name="sight" value="sight" v-model="selCats"></input>
          <label class="w-75" for="chkSight"> Sehenswürdigkeiten </label>
        </li>
        <li>
          <input type="checkbox" id="chkSettlement" name="settlement" value="settlement" v-model="selCats"></input>
          <label class="w-75" for="chkSettlement"> Ortschaften </label>
        </li>
      </ul>
    </li>
    <li>
      <input type="checkbox" id="chkAttached"></input>
      <label for="chkAttached"> Briefbeilagen  </label>
    </li>
  </ul>
  <span>
  </span>
  <!-- format="dd.MM.yyyy" -->
  <vuejs-datepicker placeholder="Briefe ab..." :format="dateFormatter" v-model="from_date" :language="de" :monday-first="true" :calendar-button="false" :clear-button="true" __typeable="true" :open-date="default_date">
  </vuejs-datepicker>
  <vuejs-datepicker placeholder="Briefe bis..." :format="dateFormatter" v-model="to_date" :language="de" :monday-first="true" :calendar-button="false" :clear-button="true" __typeable="true" :open-date="default_date">
  </vuejs-datepicker>

  <p>
  </p>
    <h5 title="Mit Klick auf eine Facettenkategorie können Sie direkt nach konkreten Entitäten suchen um dann mit diesen die Hauptsuche zu filtern"> Facetten </h5>

  <div id="facets">
    <ul class="list-unstyled">
      <li class="border-top border-bottom pt-2 pb-2 text-info">
        Korrespondenz
      </li>
      <li class="" style="cursor: pointer" v-for="idx in facets.letter_data" v-on:click="open_qs_modal(idx, {}, $event)">
        {{facet_names[idx]}}
        <ul v-on:click.stop style="padding-left: 5px">
          <li v-on:click="" v-for="obj in selected_facets[idx]" class="list-unstyled" style="font-size: 0.8em; padding-left: 0px">
            <span v-on:click="remove_facet_obj(idx, obj)" class="text-warning">&times;</span>
            <a>{{shorten(obj['name'], 30)}}</a>
          </li>
        </ul>
      </li>
      <li class="border-top border-bottom pt-2 pb-2 text-info">
        Erwähnungen
      </li>
      <li class="" style="cursor: pointer" v-for="idx in facets.mentions" v-on:click="open_qs_modal(idx)">
      {{facet_names[idx]}}
      <template v-if="idx == 'protag_creation'">
        <ul class="list-unstyled">
          <li v-for="(facet, pc_idx) in pc_facets(null)" v-on:click.stop="open_qs_modal(idx, {sub_cat: pc_idx})">
            {{facet.name}}
            <ul class="list-unstyled">
              <li v-for="(subfacet, pc_sub_idx) in pc_facets(pc_idx)" v-on:click.stop="open_qs_modal(idx, {sub_cat: pc_sub_idx})">
                {{subfacet.name}}
              </li>
            </ul>
          </li>
        </ul>
      </template>
      <ul v-on:click.stop class="list-unstyled" style="padding-left: 5px">
        <li v-on:click="" v-for="obj in selected_facets[idx]" class="" style="font-size: 0.8em; padding-left: 0px">
          <span v-on:click="remove_facet_obj(idx, obj)" class="text-warning">&times;</span>
          <a>{{shorten(obj['name'], 30)}}</a>
        </li>
      </ul>
      </li>
    </ul>

  </div>
</div>
