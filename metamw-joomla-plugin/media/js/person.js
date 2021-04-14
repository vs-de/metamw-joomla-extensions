$(function() {
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    //e.relatedTarget // previous active tab
    var tab = $(e.target).attr('href').slice(1).replace(/-tab$/, '');
    mf.personApp.change_tab(tab);
  });
});

mf.personActions = {
  corresp: 'corresp_letters',
  contact: 'corresp_people',
  mention: 'mentions'
}
mf.personApp = new Vue({
//el: '#person_mainResults',
el: '#person-container',
data: {
  r: {
    corresp: null,
    contact: null,
    mention: null
  },
  key: document.getElementById('person-container').getAttribute('data-key')
},

computed: {
},
methods: {
  change_tab(name) {
    if ( name != 'main' && !this.has_loaded(name)) {
      var arg_str = '';
      if (name == 'mention') {
        arg_str = '?with_spots=1'
      }
      this.$http.get('/_api/people/'+mf.personActions[name]+'/'+this.key+arg_str).then(function(rsp){
        this.r[name] = rsp.body;
      });
      /*
      switch (name) {
        case 'corresp':
          if (!this.has_loaded(name)) {
            this.$http.get('/_api/people/corresp_letters/'+this.key).then(function(rsp){
              this.r[name] = rsp.body;
            });
          }
        break;
      }
      */
    }
  },
  has_loaded(name) {
    return !!this.r[name];
  },
  url_for: mf.url_for,
  entry_type: mf.entry_type,
  entity_url: mf.entity_url
}
});
