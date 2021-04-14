/*
 * Meta Front definitions
 * Meta Front connects to Meta MW
 * general definitions
 *
 * @author: Valentin Schulte, VS-Coding
 *
*/

MF = function() {
  this.cfg = {};
};
var mf = new MF();

MF.prototype.add_cfg = function(cfg) {
  Object.assign(this.cfg, cfg);
}

mf.api_base = 'http://'+location.host+'/_api/';
//mf.web_base = '/dashboard/';
mf.reg_base = '/register/';

mf.cur_search = {
  sort_by: 'date',
  sort_dir: 'asc',
  doc_types: []
};

mf.facet_names = {
  author: 'Autor',
  receiver: 'Empfänger',
  send_place: 'Schreibort',
  recv_place: 'Empfangsort',
  person: 'Personen',
  protag_creation: 'FMB-Werke',
  creation: 'Werke',
  sight: 'Sehenswürdigkeiten',
  institution: 'Institutionen',
  settlement: 'Orte'
};

mf.entity_field_names = {
  all: {
    key: 'Key'
  },
  Person: {
    gnd: 'GND-Nummer',
    birth_date: 'Geboren',
    baptism_date: 'Getauft',
    death_date: 'Gestorben',
    gender: 'Geschlecht',
    occupations: 'Tätigkeiten',
    places: 'Wirkungsstätten',
    //background: 'Kurzbiographie',
    description: 'Kurzbiographie',
    bibl_entries: 'Bibliographie',
    web_links: 'Links',
  },
  ProtagCreation: {
    protag_creation_category: 'Kategorie',
    info: 'Beschreibung',
    mwv: 'MWV',
    op: 'Op.'
  },
  Creation: {
    author_names: 'Autoren',
    kind: 'Werkgattung',
    info: 'Beschreibung'
  },
  Institution: {
    settlement_name: 'Ort',
    country_name: 'Land'
  },
  Sight: {
    settlement_name: 'Ort',
    country_name: 'Land'
  },
  Settlement: {
    country_name: 'Land'
  },
  Note: {
    letter_name: 'Brief',
    content: 'Kommentar'
  }
};

mf.field_orders = {
  std: {
    Person: [
      'key',
      'gnd',
      //'',
      'birth_date',
      'baptism_date',
      'death_date',
      'gender',
      'occupations',
      'places',
      //'',
      //'background',
      'description',
      'bibl_entries',
      'web_links',
    ],
    Creation: [
      'key',
      'author_names',
      'kind',
      'info'
    ],
    ProtagCreation: [
      'key',
      'protag_creation_category',
      'mwv',
      'op',
      'info'
    ],
    Letter: [
      'key',
      'title',
      'incipit'
    ],
    Sight: [
      'key',
      'settlement_name',
      'country_name'
    ],
    Institution: [
      'key',
      'settlement_name',
      'country_name'
    ],
    Settlement: [
      'key',
      'country_name'
    ],
    Note: [
      'key',
      'letter_name',
      'content'
    ]
  },
  short: {
    Person: [
      'key',
      'gnd',
      'birth_date'
    ],
    Creation: [
      'key',
      'author_names'
    ],
    ProtagCreation: [
      'key',
      'mwv',
      'op'
    ],
    Letter: [
      'key',
      'title',
      'incipit'
    ],
    Sight: [
      'key',
      'settlement_name',
      'country_name'
    ],
    Institution: [
      'key',
      'settlement_name',
      'country_name'
    ],
    Settlement: [
      'key',
      'country_name'
    ]
  }
};


mf.entity_value_names =  {
  Person: {
    gender: {
      m: 'männlich',
      w: 'weiblich'
    }
  }
};

//params: entity and field
mf.show_value = function(e, field) {
  var v = e[field];
  var vn = mf.entity_value_names[e['class']];
  if (vn && vn[field]) {
    var r = vn[field][v];
    if (r) {return r;}
  }
  switch(field) {
    case 'web_links':
      //return '[-]';
      return v.join(' ')
    break;
    case 'bibl_entries':
      if (Array.isArray(v)) {
        var len = v.length;
        var max = 5;
        var list = v.map(function(o) {return o['content'];});
        str = list.slice(0,max).join(', ');
        if (len > max) {
          str += '... ('+len+' Angaben)';
        }
        return str;
      }
    break;
    case 'protag_creation_category':
      var pr = v.parents;
      var arr = [v.name];
      if (pr.length > 0) {
        arr.push(pr[pr.length-1].name);
      }
      return arr.join(', ')
      
    break;
    default:
      if (Array.isArray(v)) {
        return v.join(', ');
      }
  }
  return v;
}

mf.pluralize = function (str) {
  exc = {
    person: 'people'
  };
  return exc[str] || str+'s';
}

mf.entity_resource_map = {
  sight: 'place',
  institution: 'place',
  settlement: 'place'
}
mf.cat2rs_path = function(cat) {
  var g_cat = mf.entity_resource_map[cat];
  if (!g_cat) {g_cat = cat}
  return mf.pluralize(g_cat);
}

// mf.entity_elem_names = ['title', 'persName', 'placeName', 'settlement', 'note'];
// no notes, since these have often no width
mf.entity_elem_names = ['title', 'persName', 'placeName', 'settlement'];


//returns the field index names as well as the translated names
//for the given kind of display.
mf.tr_fields_for = function(o, kind_) {
  var kind = kind_ || 'std';
  var dict = null;
  var klass = mf.get_entry_class(o);
  if (!(dict = mf.entity_field_names[klass])) {
    dict = { key: 'Key', name: 'Name'};
  }
  var f_list = mf.field_orders[kind][klass];
  Object.assign(dict, mf.entity_field_names['all']);
  res = {};
  var k;
  for (var i in f_list) {
    k = f_list[i];
    res[k] = dict[k];
  }
  //TODO:  field_orders 
  return res;
};


mf.load_css = function(path) {
  var lnk = document.createElement("link");
  lnk.setAttribute("rel", "stylesheet");
  lnk.setAttribute("type", "text/css");
  lnk.setAttribute("href", path);
  document.head.appendChild(lnk);
};
mf.url_for = function(entry) {
  var k = entry.key || entry.name;
  var tp = this.entry_type(entry);
  return this.entity_url(tp, k);
};

mf.get_entry_class = function(o) {
  var klass = o['class'];
  if (!klass) {
    etp = mf.entry_type(o);
    if (etp) {
      klass = mf.camelize(etp);
    }
  }
  return klass;
};
mf.camelize = function(s) {
  var arr = s.split('_');
  arr = arr.map(function(e) {return e.length == 0 ? "" : e[0].toUpperCase()+e.slice(1);});
  return arr.join('');
};

mf.entry_type = function(entry) {
  if (entry.cat) {
    return entry.cat;
  }
  var k = entry.key || entry.name;
  if (k.match(/(fmb-1|gb-1)/)) {
    return 'letter';
  }
  if (k.match(/^PSN[0-9]{7}/)) {
    return 'person';
  }
  if (k.match(/^CRT[0-9]{7}/)) {
    return 'creation';
  }
  if (k.match(/^PRC[0-9]{7}/)) {
    return 'protag_creation';
  }
  if (k.match(/^STM[0-9]{7}/)) {
    return 'settlement';
  }
  if (k.match(/^SGH[0-9]{7}/)) {
    return 'sight';
  }
  if (k.match(/^NST[0-9]{7}/)) {
    return 'institution';
  }
  if (k.match(/^note_[0-9a-f\-]+/)) {
    return 'note';
  }
};

/*
 * @param cat category
 * @param key key of the entity
 * @return the url leading to the entity
 */
mf.entity_url = function(cat, key) {
  //return mf.web_base+cat.replace(/_/g, '-')+'/'+key;
  //return mf.cfg.base_path+cat.replace(/_/g, '-')+'/'+key;
  return mf.cfg.base_path+mf.cfg[cat+'_path']+'/'+key;
};

mf.reg_url = function(type, id) {
  return mf.reg_base+mf.pluralize(type)+'/'+id
}

mf.nw_link = function(text, url) {
  return '<a target="_blank" href="'+url+'">'+text+'</a>';
};
mf.entity_link = function(ent, text) {
  var u = mf.url_for(ent);
  return mf.nw_link(text ? text : ent.name, u);
}

