/*
mf.letterApp = new Vue({
  el: '#letter_mainResults',
});
//*/
//mf.load_css('/mmw/css/tei/tei_weblayout.css');

mf.load_css('/_api/web/assets/css/tei_weblayout');

mf.note_sign = '[→]';
mf.cfg.letter_box_scroll_speed = 500;
mf.cfg.letter_scroll_speed = 500;


mf.srcD2DispObj = function(srcD) {
  if (!srcD.msDesc) {
    return {};
  }
  var msd = srcD.msDesc[0];
  var msi = msd.msIdentifier[0];
  var msc = msd.msContents[0];
  var msp = msd.physDesc[0];
  var msh = msd.history[0];
  var msa = msd.additional;
  var dv = function(o, k) {
    return (o[k] && o[k][0]) || '';
  }
  return {
    Standort: [dv(msi,'settlement'),dv(msi, 'country')].join(', '),
    'RISM-Sigel':  [dv(msi,'institution'),dv(msi, 'repository'), dv(msi,'collection')].join(', '),
    Signatur: dv(msi, 'idno'),
    'physische Beschr.': msp,
    Typ: dv(msc.msItem[0], 'idno')
  }
};

mf.letter_meta_fields = {
  title: [
    {name: 'Titel'}
  ],
  key: [{}],
  pred: [{
    name: 'Vorgängerbrief',
    handler: function(o) {return o ? o.title : '-' ;}
  }, {
    name: "Key",
    handler:function(o) {return o ? mf.entity_link(o) : '-';}
  }],
  succ: [{
    name: 'Folgebrief',
    handler: function(o) {return o ? o.title : '-' ;}
  }, {
    name: "Key",
    handler:function(o) {return o ? mf.entity_link(o) : '-';}
  }],
  incipit: [{
    name: 'Incipit'
  }],
  fileDesc: [{
    name: 'Ausgabe',
    handler: function(o) {return o.publicationStmt && o.publicationStmt[0].publisher.join(', ');}
  },
  {
    name: 'Quellen',
    count: function(o) {return o.sourceDesc.length;},
    handler: function(o) {
      var sds = o.sourceDesc;
      var sd;
      var ret = [];
      for(var i in sds) {
        sd = sds[i];
        ret.push(mf.srcD2DispObj(sd));
      }
      return ret;
    }
  }],
  profileDesc: [{
    name: 'Verwendete Sprachen',
    handler: function(o) {return o && o.langUsage && o.langUsage[0].language.join(', ')}
  }, {
    name: 'Schreibedatum',
    handler: function(o) {return o.creation && (o.creation[0].date ? o.creation[0].date.join(', ') : o.creation.join(', '));}
  }],
  authors: [{
    name: 'Autoren',
    count: function(o) {return o.length},
    handler: function(o) {
      var ret = [];
      var p;
      for (var i in o) {
        p = o[i];
        ret.push({
          'Name': p.name,
          'Key': mf.entity_link(p, p.key)
        });
      }
      return ret;
    }
  }],
  send_places: [{
    name: 'Schreiborte',
    count: function(o) {return o.length},
    handler: function(o) {
      var ret = [];
      var p;
      for (var i in o) {
        p = o[i];
        ret.push({
          'Name': p.name,
          'Key': mf.entity_link(p, p.key)
        });
      }
      return ret;
    }
  }],
  receivers: [{
    name: 'Empfänger',
    count: function(o) {return o.length},
    handler: function(o) {
      var ret = [];
      var p;
      for (var i in o) {
        p = o[i];
        ret.push({
          'Name': p.name,
          'Key': mf.entity_link(p, p.key)
        });
      }
      return ret;
    }
  }],
  recv_places: [{
    name: 'Empfangsorte',
    count: function(o) {return o.length},
    handler: function(o) {
      var ret = [];
      var p;
      for (var i in o) {
        p = o[i];
        ret.push({
          'Name': p.name,
          'Key': mf.entity_link(p, p.key)
        });
      }
      return ret;
    }
  }],
};

mf.letterBox = new Vue({
  el: '#letter_right_box',
  data: {
    cur_elems: [],
    //tabs: ['comments', 'meta']
    tabs: ['meta', 'comments', 'spot', 'entities'],
    cur_tab: 'meta',
    cur_note: null,
    comments: {},
    letter_id: null,
    info: null,
    entities: []
  },
  created: function() {
    var ce = document.getElementById('mmw_letter_container');
    if (ce) {
     this.letter_id = ce.attributes['data-letter_id'].nodeValue;
    } else {
      console.log("could not determine letter id. letter component maybe not active");
    }
    this.set_tab(this.cur_tab);
  },
  methods: {
    set_comment: function(e, xid) {
      this.comments[xid] = e;
    },
    comments_ready: function() {
      this.comments = Object.assign({}, this.comments);
    },
    set_elems: function(data) {
      this.cur_tab = 'spot';
      this.cur_elems = data;
    },
    tr_cat: function(c) {
      var dict = {
        meta: 'Metadaten',
        spot: 'Briefstellen',
        comments: 'Kommentare',
        entities: 'Erwähnungen',
        people: 'Personen',
        places: 'Orte',
        protag_creations: 'FMB-Werke',
        creations: 'Werke'
      };
      return dict[c] || c;
    },
    tr_fields_for: function(o) {
      
      /*
      var dict = null;
      if (!(dict = mf.entity_field_names[o['class']])) {
        dict = { key: 'Key', name: 'Name'};
      }
      return dict;
      */
      return mf.tr_fields_for(o);
      /*
      var obj = {};
      for (k in dict) {
        obj[dict[k]] = o[k];
      }
      return obj;
      */
    },
    //hl_note: function (e, ev) {
    choose_note: function(e, xid) {
      this.hl_note(e, xid);
      mf.scroll_to_elem(e);
    },
    hl_note: function (e, xid) {
      this.cur_tab = 'comments';
      var doc = window.document;
      var cur_list;
      var ce;
      cur_list = doc.getElementsByClassName('note_link');
      for (var i = 0; i < cur_list.length; i++) {
        ce = cur_list[i];
        ce.classList.remove('active');
      }
      var cur_lnk = doc.getElementsByClassName(xid)[0];
      cur_lnk.classList.add('active');

      //var xid = this.get_attr(e, 'xml:id');
      //window.cur_hl = mf.find_x_elem(xid, 'note');
      //console.log(e.innerHTML.length);
      //window.cur_hl = e;
      cur_list = doc.getElementsByClassName('in_doc_note');
      for (var i = 0; i < cur_list.length; i++) {
        ce = cur_list[i];
        ce.classList.remove('active');
      }
      mf.activate_note(e);
    },
    show_value: function(e,k) {return mf.show_value(e,k)},
    get_attr: function (e, name) {return mf.get_attr(e, name);},
    load_meta: function() {
      this.$http.get('/_api/letters/info/'+this.letter_id, {}).then(function (resp) {
        this.info = resp.body;
      });
    },
    load_entities: function() {
      this.$http.get('/_api/letters/entities/'+this.letter_id, {}).then(function(resp) {
        this.entities = resp.body;
      })
    },
    set_tab: function (name) {
      this.cur_tab = name;
      if (name == 'meta' && !this.info) {
        this.load_meta();
      }
      if (name == 'entities' && this.entities.length == 0) {
        this.load_entities();
      }
    },
    meta_fields: function() {
      var list = [];
      var e, l;
      var cnt;
      for (var k in mf.letter_meta_fields) {
        l = mf.letter_meta_fields[k];
        for(var i=0; i < l.length; i++) {
          if (l[i].count) {
            cnt = l[i].count(this.info[k]);
          } else {cnt = 0;}
          list.push([k,i,cnt]);
        }
      }
      return list;
    },
    meta_list: function(k,i) {
      //console.log(k,i);
      var o = mf.letter_meta_fields[k][i];
      var list = o.handler(this.info[k]);
      return list;
    },
    meta_label: function(k, i) {
      var o = mf.letter_meta_fields[k][i];
      return o.name != null ? o.name : k;
    },
    meta_value: function(k, i) {
      var o = mf.letter_meta_fields[k][i];
      var o_info = this.info[k];
      /*
      if (Array.isArray(o_info)) {
        o_info = o_info[0];
      }
      */
      // return o.handler != null ? o.handler(o_info) : mf.entityEnc(o_info);
      return o.handler != null ? o.handler(o_info) : o_info;
    },
    entityEnc: function(s) {return mf.entityEnc(s);},
    entity_link: function(x,y) {return mf.entity_link(x,y);}
  }
});

mf.create_note_sign = function(e, klass) {
  var doc = window.document;
  var cnt = doc.createElement("span");
  cnt.classList.add('in_doc_note');
  if (klass) {
    cnt.classList.add(klass);
  }
  var nt = doc.createTextNode(mf.note_sign);
  cnt.appendChild(nt);
  e.parentNode.insertBefore(cnt, e);
  var xid;
  $(cnt).on('click', function(ev) {
    //console.log(ev);
    xid = $(e).attr('xml:id');
    var ct = mf.letterBox.cur_tab;
    mf.letterBox.hl_note(e, xid);
    if (ct != 'comments') {
      setTimeout( function() {
        mf.letterBox.$scrollTo('.'+xid, mf.cfg.letter_box_scroll_speed, {container: '#sp-right > div'});
      }, 150);
    } else {
      mf.letterBox.$scrollTo('.'+xid, mf.cfg.letter_box_scroll_speed, {container: '#sp-right > div'});
    }
  });
};

mf.activate_note = function(e) {
  //console.log(e);
  e.previousSibling.classList.add('active');
};

mf.get_attr = function (e, name) {
  if (e && e.attributes[name]) {
    return e.attributes[name].nodeValue;
  }
  return null;
};

mf.api_get_ref = function(path, cb) {
  $.get(mf.api_base+path, function(asw) {
    cb(asw);
  });
};

mf.clear_all_classes = function(klass) {
  mf.entity_elem_names.forEach(function(e) {
    $(e).removeClass(klass);
  });
};

mf.find_x_elem = function(xid, tag) {
  var hlist = document.getElementsByTagName(tag);
  var e, eid;
  for (var i = 0; i < hlist.length; i++) {
    e = hlist[i];
    eid = mf.get_attr(e, 'xml:id');
    if (eid == xid) return e;
  }
}

mf.scroll_to_elem = function(el) {
  var mark = $(el).prev();
  var t_ofs = mark.offset().top;
  /*
  var st = $(el).attr('style');
  $(el).attr('style', '');
  var t_ofs = $(el).offset().top;
  $(el).attr('style', st);
  */
  /*
  $('html, body').animate({
    scrollTop: t_ofs
  }, mf.cfg.letter_scroll_speed);
  */
  $('html, body').scrollTop(t_ofs - 150);
  //$(el)[0].scrollIntoView();
}

mf.cfg.tooltip = {}
mf.cfg.tooltip.ignore_attrs = ['type', 'resp', 'rend', 'style', 'n' ];
mf.cfg.tooltip.prefer_attrs = ['reason', 'name'];
mf.cfg.tooltip.ignore_tags = ['div', 'p'];

mf.clean_str = function(s) {
  var ns = s.replaceAll(/[\s]{2,}/g, ' ');
  return ns.replaceAll(/[^\p{L}\p{Dash} \()\-;\.,\[\]0-9]/gu, '');
}

mf.letter = {
  initTooltips: function() {
    $('TEI body *').each(function(i, e) {
      var tag = e.tagName;
      var pref = [];
      var list = [];
      if (mf.cfg.tooltip.ignore_tags.includes(tag)) {
        return;
      }
      var chs = $(e).find('name');

      var t;
      if (chs.length > 0) {
        if (!(t = e.getAttribute('title'))) {
          t = $.map(chs, function(e){return $(e).text();}).join(' ; ');
          //e.setAttribute('title', mf.clean_str(t));
          tippy(e, {content: mf.clean_str(t)});
        } else { 
          tippy(e, {content: mf.clean_str(t)});
        }
        return;
      }
      $.each(e.attributes, function(i, attr) {
        var name = attr.name;
        if (mf.cfg.tooltip.ignore_attrs.includes(name)) {
          return;
        }
        if (mf.cfg.tooltip.prefer_attrs.includes(name)) {
          pref.push(name);
        } else {
          list.push(name);
        }
      });
      if (!(t = e.getAttribute('title'))) {
        var src_attr = null;
        if (pref.length > 0) {
          src_attr = pref[0];
        } else if (list.length > 0) {
          src_attr = list[0];
        }
        if (src_attr) {
          //e.setAttribute('title', mf.clean_str(e.getAttribute(src_attr)));
          tippy(e, {content: mf.clean_str(e.getAttribute(src_attr))});
        }
      } else {
        tippy(e, {content: mf.clean_str(t)});
      }

    });
  }
};


$(function() {
  console.log('loading MMW Letter Extensions...');
  
  mf.entity_elem_names.forEach(function(e) {
    $('TEI body '+e).on('click', function(ev) {
      var e = $(this);
      var xid = e.attr('xml:id');
      //e.attr('id', xid);
      //e.css('background', '#e0e011');
      mf.clear_all_classes('entity_hl');
      e.addClass('entity_hl');
      mf.api_get_ref('spots/'+xid, function(asw) {
        //console.log(asw);
        mf.letterBox.set_elems(asw);
      });
    });
  });
  mf.letter.initTooltips();

  // notes
  $('note').each(function(ev) {
    mf.letterBox.set_comment(this, $(this).attr('xml:id'));
    mf.create_note_sign(this);
  });
  mf.letterBox.comments_ready();

  /*
  $('#box_tabs a').on('click', function (e) {
    e.preventDefault();
    $(this).tab('show');
  })
  */

  //TODO: real encoding solution
  mf.entityEnc = function(s) {
    if (!s) {
      console.log("warning: enc with no string!");
      return '';
    }
    //console.log(typeof(s));
    var es = s.replaceAll('&amp;', '&'); // stupid double enc prevention
    es = es.replaceAll('&', '&amp;');
    return es;
  }
  // bug fixes for tabs on XHTML
  //$('#maintabs a[data-toggle="tab"]').on('show.bs.tab', function(e) {console.log('show', e)});
  $('#maintabs a[data-toggle="tab"]').on('hide.bs.tab', function(e) {
    //console.log('hide', e);
    $(e.target).removeClass('active');
  });
  //$('#maintabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {console.log('shown', e)});
  //$('#maintabs a[data-toggle="tab"]').on('hiddenn.bs.tab', function(e) {console.log('hidden', e)});
  
  $('#box_tabs a[data-toggle="tab"]').on('hide.bs.tab', function(e) {
    //console.log('hide', e);
    $(e.target).removeClass('active');
  });
});
