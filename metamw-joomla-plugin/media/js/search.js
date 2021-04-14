/*
 * @author      Valentin Schulte
 * @copyright   (C) 2020 Valentin Schulte Coding, Worms. All rights reserved.
 */

/*
 * main search vue apps for MetaMW connected digital edition software
 */

mf.facets = {
  letter_data: ['author','receiver', 'send_place', 'recv_place'],
  mentions: ['person', 'protag_creation', 'creation', 'sight', 'institution', 'settlement'],
  mentions_of_protag_creations: {}
};

mf.resource_map = {
  /*
  author: 'people',
  receiver: 'people',
  protag_creation: 'protag_creations',
  creation: 'creations',
  sight: 'sights',
  institution: 'institutions',
  settlement: 'settlements'
  */
  author: 'Author',
  receiver: 'Receiver',
  send_place: 'SendPlace',
  recv_place: 'RecvPlace',
  person: 'Person',
  protag_creation: 'ProtagCreations',
  creation: 'Creation',
  sight: 'Sight',
  institution: 'Institution',
  settlement: 'Settlement',
  note: 'Note'
};
mf.helper = {
  shorten: function(s, l) {
    if (!s)
      return null;
    if (s.length > l) {
      return s.slice(0,l-3)+'...';
    }
    return s;
  }
};
mf.descFields = {
  person: 'description', //background
  //see desc_handler
  //creation: ['info', 'authors'],
  protag_creation: 'info',
  institution: ['kind', 'settlement_name', 'country'],
  sight: ['kind', 'settlement_name', 'country'],
  note: ['letter_name', 'content']
};
mf.cfg.search = {
  per_page: 20
};


/*
 * main search app intended  to be used in a left side search box
 */

mf.filterApp = new Vue({
  el: '#meta_mw_main_search_box',
  data: {
    cfg: mf.cfg,
    lv_per_page: 10,
    cur_lv: null,
    lv_type: null,
    lv_subtype: null,
    lv_q: "",
    lv_res: [],
    _lv_selected: [],
    lv_pages: null,
    lv_page: null,
    q: "",
    cur_q_data: null,
    selCats: ['letter', 'meta', 'note', 'person', 'creation', 'protag_creation'],
    //selCats: [],
    sel_place_cats: null,
    sel_letter_cats: true,
    selected_facets: {
      author: [],
      receiver: [],
      send_place: [],
      recv_place: [],
      person: [],
      protag_creation: [],
      creation: [],
      sight: [],
      institution: [],
      settlement: []
    },
    is_new_search: null,
    facets: mf.facets,
    facet_names: mf.facet_names,
    de: vdp_translation_de.js,
    default_date: new Date(1830, 06, 15),
    from_date: null,
    to_date: null,
    use_fmb: true,
    use_gb: true,
  },
  components: {
    vuejsDatepicker
  },
  computed: {
    /*
    lv_selected: {
      get: function() {
        return this._lv_selected;
      },
      set: function(nv) {
        facets_selected[cur_lv].push(nv);
      }
    }
    */
  },
  methods: {
    dateFormatter(date) {
      //return date.toLocaleDateString('DE');
      d = date.getDate();
      m = date.getMonth() + 1;
      y = date.getFullYear();
      d = d < 10 ? "0"+d : d;
      m = m < 10 ? "0"+m : m;
      return [d,m,y].join('.');
    },
    remove_facet_obj(idx, obj) {
      var key = obj['key'];
      //console.log("remove", idx, key);
      this.selected_facets[idx] = this.selected_facets[idx].filter(function(o){return o['key'] != key});
    },
    remove_facet(obj) {
      var key = obj['key'];
      this.remove_facet_obj(obj.cat, obj);
    },
    has_sel_facet(obj) {
      var key = obj['key'];
      f_list = this.selected_facets[obj.cat]
      return f_list && !!f_list.find(function(o){return o['key'] == key});
    },
    lv_select_all() {
      //console.log(this.lv_res);
      this.selected_facets[this.cur_lv] = Array.from(new Set(this.selected_facets[this.cur_lv].concat(this.lv_res)));
    },
    lv_deselect_all() {
      for (i in this.lv_res) {
        this.remove_facet_obj(this.cur_lv, this.lv_res[i]);
      }
    },
    reset_lv: function() {
      this.lv_type = null;
      this.lv_subtype = null;
      this.lv_q = "";
      this.lv_res = [];
      this.lv_page = null;
      this.lv_pages = null;
    },
    obj2param: function(o) {
      return o['key'] || o['id'];
      //return o['id'];
    },
    // only handles objs of the form {a: [...], b: [...], ...}
    l2_obj2param: function(o) {
      var no = {};
      for(i in o) {
        l = o[i];
        no[i] = l.map(this.obj2param);
      }
      return no;
    },
    add_facet: function(cat, entry) {
      var fcts = this.selected_facets[cat] || [];
      entry['cat'] = cat;
      fcts.push(entry);
      this.selected_facets[cat] = Array.from(new Set(fcts));
      //console.log("add facet ", cat, entry);
    },
    shorten: function(s, l) {return window.mf.helper.shorten(s, l);},
    basic_search: function(ev) {
      mf.searchResults.basic_query(this.q);
    },
    do_search: function() {
      this.is_new_search = true;
      this.search(1, null, true);
    },
    search: function(page, opts, is_new) {
      //console.log(page, data);
      var q = this.q;
      if (!page) {
        var page = 1;
      }
      var data = Object.assign({}, this.cfg.search);
      if (!opts) {
        mf.searchResults.spinner_active = true;
        Object.assign(data, {
          q,
          //cat: this.selCats
        });
        Object.assign(data, this.l2_obj2param(this.selected_facets));
        if (this.from_date) {
          Object.assign(data, {from_date: this.from_date});
        }
        if (this.to_date) {
          Object.assign(data, {to_date: this.to_date});
        }
        this.cur_q_data = data;
      } else {
        var data = this.cur_q_data;
        Object.assign(data, opts);
        //for (k in opts) {data[k] = opts[k];}
      }
      Object.assign(data, {page});
      data['cat'] = this.selCats;
      if (!this.use_fmb) {
        data['no_fmb'] = true;
      }
      if (!this.use_gb) {
        data['no_gb'] = true;
      }
      this.$http.post('/_api/search/main', data, { headers: {'Content-Type': 'application/json'}}).then(function(rsp) {
        /*
        console.log(rsp);
        console.log("---");
        console.log(rsp.body);
        //*/
        mf.searchResults.update(rsp.body, is_new);
      });
      //grrr
      //$('#maintabs li:first-child a').tab('show');
    },
    open_qs_modal: function(k, opts, ev) {
      this.reset_lv();
      this.cur_lv = k;
      this.lv_type = mf.resource_map[k];
      if (opts) {
        this.lv_subtype = opts['sub_cat'];
      }
      var el = document.getElementById('facet_qs_modal');
      $(el).modal();
    },
    pc_facets: function(prnt) {
      var obj = {}
      for (var k in this.facets.mentions_of_protag_creations) {
        facet = this.facets.mentions_of_protag_creations[k];
        if (facet['parent'] == prnt) {
          obj[k] = facet;
        }
      }
      return obj;
    },
    lv_search: function(pg) {
      var page = pg;
      if (!page) {
        var page = 1;
      }
      /*
      console.log(page);
      console.log(this.lv_q);
      console.log(this.lv_type);
      //*/
      var q = this.lv_q;
      //this.$http.get('/_api/search/lv_'+this.lv_type+'?per_page='+this.lv_per_page+'&q='+q).then(function(rsp) {
      //this.$http.get('/_api/search/lv_'+this.lv_type, {params: {page, per_page: this.lv_per_page, q}}).then(function(rsp) {
      this.$http.get('/_api/search/lv', {params: {resource: this.lv_type, subtype: this.lv_subtype, page, per_page: this.lv_per_page, q}}).then(function(rsp) {
        /*
        console.log(rsp);
        console.log("---");
        console.log(rsp.body);
        //*/
        var asw = rsp.body;
        this.lv_page = asw['page'];
        this.lv_pages = asw['pages'];
        this.lv_res = asw['result'];
      });
    },
    load_facets: function() {
      this.$http.get(mf.api_base+'search/pcg_facets').then(function(r) {
        this.facets['mentions_of_protag_creations'] = r.body;
      }.bind(this));
    }
  },
  watch: {
    sel_place_cats: function(arg) {
      var add = arg;
      ['institution', 'sight', 'settlement'].forEach(function(c) {
        if (this.selCats.includes(c)) {
          if (!add) {
            this.selCats.splice(this.selCats.indexOf(c), 1);
          }
        } else {
          if (add) {
            this.selCats.push(c);
          }
        }
      }.bind(this));
    },
    sel_letter_cats: function(arg) {
      var add = arg;
      ['letter', 'meta', 'note'].forEach(function(c) {
        if (this.selCats.includes(c)) {
          if (!add) {
            this.selCats.splice(this.selCats.indexOf(c), 1);
          }
        } else {
          if (add) {
            this.selCats.push(c);
          }
        }
      }.bind(this));
    },
    use_fmb: function(v) {
      if (!v) {this.use_gb = true;}
    },
    use_gb: function(v) {
      if (!v) {this.use_fmb = true;}
    }
  },
  mounted: function() {
    this.load_facets();
  }
});

mf.resultMap = {
  letter: {
    key: 'name'
  }
}

mf.desc_handler = {
  creation: function(data) {
    return [data['info']].concat(
      data['authors'].map(function(a){return a.name})
    );
  }
}

mf.build_snips = function(hl) {
  var snips = [];
  if (hl) {
    var kws = hl.kws.split(' ');
    for (j in hl.snippets) {
      var snip = hl.snippets[j];
      //console.log(kws);
      snips.push(
        snip.replace(
          new RegExp(kws.join('|'), 'ig'), 
          '<span class="font-weight-bold">$&</span>'
        )
      );
    }
  }
  return snips;
};

/*
 * progressive search result content application for main content-area
 *
 */
mf.searchResults = new Vue({
  el: '#meta_mw_search_results',
  data: {
    resource_order: ['letter', 'person', 'creation', 'protag_creation', 'institution', 'sight', 'settlement', 'note'],
    r: {
      letter: null,
      person: null,
      creation: null,
      protag_creation: null
    },
    cfg: mf.cfg,
    spinner_active: false,
    hl_num: null,
    query_id: null
  },
  methods: {
    //ordered results
    ord_res: function() {
      var obj={};
      this.resource_order.forEach(function(e){
        obj[e] = null;
      });
      return Object.assign(obj, this.r);
    },
    basic_query: function(q) {
      this.$http.get('/_api/search/simple_fts', {params: {q}}).then(function (resp) {
        var list = resp.body;
        window.list = list;
        var qrs = list.results; // query results
        this.r.letters = [];
        var letters = [];
        var e;
        for (i in qrs) {
          e = qrs[i];
          var kws = e.hl.kws.split(' ');
          var snips = e.hl.snippets;
          var snips = [];
          for (j in e.hl.snippets) {
            var snip = e.hl.snippets[j];
            snips.push(
              snip.replace(new RegExp(kws.join('|'), 'ig'), '<span class="font-weight-bold">$&</span>')
            );
          }
          letters.push({
            name: e.name, 
            desc: this.shorten(e.title, 90),
            excps: snips
          });
        }
        this.r.letters = letters;
      });
    },
    paginate: function(p, cat) {
      /*
      if (cat == 'letter') {
        mf.filterApp.search(p, {});
      } else {
        mf.filterApp.search(p, {cat: [cat]});
      }
      */
      mf.filterApp.is_new_search = false;
      mf.filterApp.search(p, {result_cat: [cat]}, false);
    },
    r_pos: function(cat, p_idx) {
      return 1 + p_idx + (this.cfg.search.per_page * (this.r[cat].page-1))
    },
    update: function(q_res, is_new) {
      this.query_id = q_res.id;
      this.hl_num = null;
      //add the category inside the facet for convenience
      //sets the desc field
      //this.r = 
      //Object.keys(q_res).forEach(function(k) {
      var f_res = Object.fromEntries(Object.entries(q_res).filter(function(a){return this.resource_order.includes(a[0])}.bind(this)));
      //this.resource_order.filter(function(e) {return q_res[e];}).forEach(function(k) {
      Object.keys(f_res).forEach(function(k) {
        var entrs = q_res[k]['entries'];
        q_res[k]['entries'] = entrs.map(function(e) {
          if (!e['desc']) {
            if (mf.desc_handler[k]) {
              e['desc'] = mf.desc_handler[k](e);
            } else {
              var fields = [mf.descFields[k]].flat();
              e['desc'] = fields.map(function(f) {return this.shorten(e[f], 90);}.bind(this)).filter(function(e) {return e;});
            }
          } else {
            
            //maybe this when desc given:
            //if (typeof(e['desc']) == 'string') {
            //  e['desc'] = [e['desc']];
            //}
          }
          e['cat'] = k; return e
        }.bind(this));
      }.bind(this));
      //if (mf.filterApp.is_new_search) {
      //console.log("new: "+is_new, "f_res: ", f_res);
      if (is_new) {
        var nr = Object.assign({}, f_res);
      } else {
        var nr = Object.assign({}, this.r, f_res);
      }
      //console.log("nr: ", nr);
      //for (k in q_res) {this.r[k] = q_res[k];}
      //this.r = q_res;
      this.r = nr;
      //this.r.letter = q_res.letter;
      if (q_res['letter']) {
        this.transform_letters();
      }
      this.spinner_active = false;
    },
    add_facet: function(cat, entry) {
      mf.filterApp.add_facet(cat, entry);
    },
    remove_facet: function(entry) {
      mf.filterApp.remove_facet(entry);
    },
    has_sel_facet: function(obj) {
      return mf.filterApp.has_sel_facet(obj);
    },
    transform_letters() {
      var letters = [];
      var entries = this.r.letter.entries;
      for (i in entries) {
        e = entries[i];
        var snips = [];
        if (e.hl) {
          /*
          var kws = e.hl.kws.split(' ');
          for (j in e.hl.snippets) {
            var snip = e.hl.snippets[j];
            console.log("snip: "+snip);
            snips.push(
              snip.replace(new RegExp(kws.join('|'), 'ig'), '<span class="font-weight-bold">$&</span>')
            );
          }
          */
        }
        if (e.hl) {
          var qa = mf.filterApp.q.split(' ');
          var kwa = e.hl.kws.split(' ');
          var w;
          for (i in qa) {
            w = qa[i];
            if (kwa.includes(w)) {
              snips = mf.build_snips(e.hl);
              break;
            }
          }
        }
        letters.push({
          key: e.name, 
          //desc: [this.shorten(e.title, 90)],
          desc: [e.title],
          excps: snips
        });
      }
      this.r.letter.entries = letters;
    },
    explain: function(cat, entry, num) {
      this.hl_num = num;
      if (cat == 'letter') {
        mf.detailBox.explain_result(entry.key, this.r_pos(cat, num));
      } else {
        mf.detailBox.entity_info(cat, entry, this.r_pos(cat, num));
      }
    },
    shorten: function(s, l) {return window.mf.helper.shorten(s, l);},
    /*
    shorten: function(s, l) {
      if (!s)
        return null;
      if (s.length > l) {
        return s.slice(0,l-3)+'...';
      }
      return s;
    },
    */
    tr_cat: function(str) {
      return {
        letter: 'Briefe',
        person: 'Personen',
        creation: 'Werke',
        protag_creation: 'FMB-Werke',
        institution: 'Institutionen',
        sight: 'Sehenswürdigkeiten',
        settlement: 'Ortschaften',
        note: 'Kommentare',
        meta: 'Metadaten'
      }[str] || str;
    },
    //url_for: mf.url_for,
    url_for: function(data) {
      if (data.cat == 'note') {
        return mf.url_for({cat: 'letter', key: data.letter_name});
      }
      return mf.url_for(data);
    },
    reg_url: mf.reg_url,
    entry_type: mf.entry_type,
    entity_url: mf.entity_url
  },
  updated: function() {
    //console.log('vue updated!');
    //$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      //e.relatedTarget // previous active tab
      //var tab = $(e.target).attr('href').slice(1).replace(/-tab$/, '');
      //mf.personApp.change_tab(tab);
      //console.log("tab", tab);
    //});
    if (mf.filterApp.is_new_search) {
      var t = $('#maintabs li:first-child a');
      if (t.length > 0) {
        t.tab('show');
        mf.filterApp.is_new_search = false;
      }
    }
  }
});

/*
 * progressive info box app for showing details of the search result entries
 *
 */
mf.detailBox = new Vue({
  el: '#meta_mw_info_box',
  data: {
    r_pos: null,
    r_key: null,
    r_cat: null,
    ent_list: [], //entity list
    letter_hl: null,
    ent: null, //single entity
    spinner_active: false
  },
  methods: {
    init_explain: function(key, r_pos, cat) {
      this.r_cat = cat || 'letter';
      this.r_key = key;
      this.r_pos = r_pos;
      this.r_ent = null;
      this.ent_list = [];
      this.letter_hl = null;
      this.ent = null;
    },
    explain_result: function(key, r_pos) {
      this.r_ent = null;
      this.init_explain(key, r_pos);
      var cats = mf.filterApp.selCats.join(',');
      var q = mf.filterApp.q || '';
      this.spinner_active = true;
      this.$http.get('/_api/search/explain/'+key+'/'+cats+'/'+q, {}).then(function (resp) {
        this.ent_list = resp.body['entities'];
        this.letter_hl = resp.body['letter_hl'];
        this.spinner_active = false;
      }.bind(this));
    },
    entity_info: function(cat, entry, r_pos) {
      key = entry.key;
      //entry.cat ||= cat;
      this.init_explain(key, r_pos, cat);
      this.r_ent = entry;
      var res_path = mf.cat2rs_path(cat);
      this.spinner_active = true;
      this.$http.get('/_api/'+res_path+'/'+key).then(function (resp) {
        this.ent = resp.body;
        //this.ent.cat = this.r_cat;
        this.spinner_active = false;
      }.bind(this));
    },
    ent_name: function(e) {
      /*
      if (e.name) {
        return e.name;
      } else {
        return e.last_name+', '+e.first_name;
      }
      */
      return e.name;
    },
    // singular transl
    tr_cat_sg: function(str) {
      return {
        letter: 'Brief',
        person: 'Person',
        creation: 'Werk',
        protag_creation: 'FMB-Werk',
        institution: 'Institution',
        sight: 'Sehenswürdigkeit',
        settlement: 'Ortschaft',
        //note: 'Kommentar',
        explanation_note: "Worterklärung",
        constitution_note: "Textkonstitution",
        passage_note: "Einzelstellenkommentar",
        topic_note: "Themenkommentar",
        document_note: "Gesamtkommentar",
        translation_note: "Übersetzung"
      }[str] || str;
    },
    build_snips: function(hl_data) {
      return mf.build_snips(hl_data);
    },
    url_for: function(data) {
      if (data.cat == 'note') {
        return mf.url_for({cat: 'letter', key: data.letter_name});
      }
      return mf.url_for(data);
    },
    entry_type: mf.entry_type,
    entity_url: mf.entity_url,
    tr_fields_for: mf.tr_fields_for,
    entity_link: mf.entity_link,
    show_value: function(e,k) {return mf.show_value(e,k)}
  }
});
