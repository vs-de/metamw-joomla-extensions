mf.placetApp = new Vue({
  el: '#place-container',
  data: {
    key: document.getElementById('place-container').getAttribute('data-key'),
    letters: [],
    letter_attrs: mf.field_orders['short']['Letter']
  },
  methods: {
    get_letters: function() {
      this.$http.get('/_api/places/mentions/'+this.key+'?with_spots=1').then(function(rsp) {
        this.letters = rsp.body;
      });
    },
    url_for: function(obj) {
      return mf.url_for(obj);
    }
  },
  created: function() {
    this.get_letters();
  }
});
