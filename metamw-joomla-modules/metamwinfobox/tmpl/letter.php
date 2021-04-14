<div id="letter_right_box" xmlns:v-bind="fix-ns" xmlns:v-on="fix-ns2" xmlns:v-html="fix-ns3">

  <ul class="nav nav-tabs" id="box_tabs" role="DIStablist">
    <template v-for="(cat, idx) in tabs" class="list-group">
      <li class="nav-item">
        <a v-bind:class="['nav-link', { 'active' : cur_tab == cat }]" v-bind:href="'#'+cat+'-tab'" v-on:click.prevent="set_tab(cat)" role="DIStab">
          {{tr_cat(cat)}}
        </a>
      </li>
    </template>
  </ul>
  <div class="tab-content" id="right_tab_content">
    <div v-bind:class="['tab-pane', { 'active' : cur_tab == 'spot'}]" id="spot-tab" role="DIStabpanel">
      <template v-for="e in cur_elems">
        <template>
          
          <div class="entity_name entity_hl">
            <h3> {{ e.name }} </h3>
          </div>
          <div class="field" v-for="(k_name, k) in tr_fields_for(e)">
            <label class="box-label"> {{ k_name }}: </label>
            <span v-if="k == 'key'" class="box-value" v-html="entity_link(e, show_value(e, k))"> </span>
            <span v-else="" class="box-value"> {{ show_value(e, k) }} </span>
          </div>
        </template>
        <template v-if="e.class == 'Person'">
        </template>
        <template v-if="e.class == 'ProtagCreation'">
        </template>

      </template>
    </div>
    <div v-bind:class="['tab-pane', { 'active' : cur_tab == 'comments'}]" id="comments-tab" role="DIStabpanel">
      <div v-for="(cm, cid) in comments" class="comment_box border-top">
        <div class="comment_info_type">
          {{ get_attr(cm, 'type') }}
        </div>
        <div v-on:click="choose_note(cm, cid)" v-bind:class="['spot_link', 'note_link', cid]">
          → Stelle <!-- {{ get_attr(cm, 'xml:id') }} -->
        </div>
        <div class="comment_info_body" v-html="cm.innerHTML">
        </div>
      </div>
    </div>
    <div v-bind:class="['tab-pane', { 'active' : cur_tab == 'entities'}]" id="entities-tab" role="DIStabpanel">
      <div v-for="(egrp, g_name) in entities">
        <div v-if="egrp.length > 0">
          <h3> {{tr_cat(g_name)}} </h3>
          <ul>
            <li v-for="ent in egrp" v-html="entity_link(ent)">
            </li>
          </ul>
        </div>
        
      </div>
    </div>
    
    <div v-bind:class="['tab-pane', { 'active' : cur_tab == 'meta'}]" id="meta-tab">
      <div id="letter_meta_fields" v-if="info">
        <div class="field" v-for="lst in meta_fields()">
          <template v-if="lst[2] > 0">
            <div>
              <label class="box-label"> {{ meta_label(lst[0], lst[1]) }}: </label>
            </div>
            <template v-for="(block,i) in meta_list(lst[0], lst[1])">
              {{i+1}})
              <div v-for="(val, lbl) in block" class="ml-2">
                <label class="box-label"> {{lbl}}: </label>
                <span class="box-value" v-html="entityEnc(val)"></span> 
              </div>
            </template>
          </template>
          <div v-else="">
            <label class="box-label"> {{ meta_label(lst[0], lst[1]) }}: </label>
            <span class="box-value" v-html="entityEnc(meta_value(lst[0], lst[1]))"></span> 
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
