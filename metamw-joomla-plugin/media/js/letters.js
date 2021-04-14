mf.facets = {
  letter_data: ['author','receiver'],
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
  person: 'Person',
  protag_creation: 'ProtagCreations',
  creation: 'Creation',
  sight: 'Sight',
  institution: 'Institution',
  settlement: 'Settlement'
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
  institution: ['kind', 'settlement', 'country'],
  sight: ['kind', 'settlement', 'country']
};
mf.cfg = {
  search: {
    per_page: 20
  }
};

mf.filterApp = new Vue({
  el: '#letters_left_box',
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
    selCats: ['letter', 'person', 'creation'],
    sel_place_cats: null,
    selected_facets: {
      author: [],
      receiver: [],
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
    to_date: null
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
      return !!this.selected_facets[obj.cat].find(function(o){return o['key'] == key});
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
    do_search: function() {
      this.is_new_search = true;
      this.search(1, null, true);
    },

    fetch: function(page, opts) {
      this.$http.get(mf.api_base+'search/reg_results/letters/'+mf.qid).then(function(r) {
        mf.searchResults.update(r.body);
      });
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
      this.$http.get('/_api/search/in_reg_search/letters/'+mf.qid, { headers: {'Content-Type': 'application/json'}}).then(function(rsp) {
        mf.searchResults.update(rsp.body, is_new);
      });
      //grrr
      //$('#maintabs li:first-child a').tab('show');
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
    }
  },
  mounted: function() {
    //this.load_facets();

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

mf.searchResults = new Vue({
  el: '#search_results',
  data: {
    r: {
      letter: null,
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
      //var f_res = Object.fromEntries(Object.entries(q_res).filter(function(a){return this.resource_order.includes(a[0])}.bind(this)));
      //this.resource_order.filter(function(e) {return q_res[e];}).forEach(function(k) {
      var f_res = {letter: q_res['letter']};
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
      if (is_new) {
        var nr = Object.assign({}, f_res);
      } else {
        var nr = Object.assign({}, this.r, f_res);
      }
      //for (k in q_res) {this.r[k] = q_res[k];}
      //this.r = q_res;
      this.r = nr;
      //this.r.letter = q_res.letter;
      if (q_res['letter']) {
        this.transform_letters();
      }
      this.spinner_active = false;
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
        snips = mf.build_snips(e.hl)
        letters.push({
          key: e.name, 
          //desc: [this.shorten(e.title, 90)],
          desc: [e.title],
          excps: snips
        });
      }
      this.r.letter.entries = letters;
    },
    explain: function(cat, l, num) {
      this.hl_num = num;
      mf.detailBox.explain_result(l, this.r_pos(cat, num));
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
        settlement: 'Ortschaften'
      }[str] || str;
    },
    url_for: mf.url_for,
    reg_url: mf.reg_url,
    entry_type: mf.entry_type,
    entity_url: mf.entity_url
  },
  mounted: function() {
    var el = document.getElementById('search_results');
    mf.qid = el.getAttribute('data-qid');
    mf.filterApp.fetch(1);
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

mf.detailBox = new Vue({
  el: '#explain_right_box',
  data: {
    r_pos: null,
    r_key: null,
    ent_list: [], //entity list
    letter_hl: null
  },
  methods: {
    explain_result: function(key, r_pos) {
      this.r_key = key;
      this.r_pos = r_pos;
      this.ent_list = [];
      this.letter_hl = null;
      var cats = mf.filterApp.selCats.join(',');
      var q = mf.filterApp.q || '';
      this.$http.get('/_api/search/explain/'+key+'/'+cats+'/'+q, {}).then(function (resp) {
        this.ent_list = resp.body['entities'];
        this.letter_hl = resp.body['letter_hl'];
      }.bind(this));
    },
    ent_name: function(e) {
      if (e.name) {
        return e.name;
      } else {
        return e.last_name+', '+e.first_name;
      }
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
        settlement: 'Ortschaft'
      }[str] || str;
    },
    build_snips: function(hl_data) {
      return mf.build_snips(hl_data);
    },
    url_for: mf.url_for,
    entry_type: mf.entry_type,
    entity_url: mf.entity_url
  }
});
