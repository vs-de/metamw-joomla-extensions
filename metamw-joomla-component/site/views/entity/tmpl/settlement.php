<?php
  $g = $this->g;
  if (isset($g->place) && $g->place['type'] == 'Settlement') {
    $p = $g->place;
    $key = $p['key'];
    $attr_order = ['country_name', 'info'];
  ?>

  <?php echo '['.$key.']'; ?>
  <h1>
  <?php echo $p['name']; ?>
  </h1>
  
  <div id="place-container" data-key="<?php echo $g->place['key']?>" data-kind="<?php echo $g->place['type']?>">
    <ul class="list-unstyled">
    <?php
    foreach($attr_order as $k ) { ?>
      <li>
        <?php echo$p[$k] ?>
      </li>
    <?php
    }
    ?>
    </ul>
    <h2 v-if="letters.length > 0"> in Briefen: </h2> 
    <ul class="list-group">
      <li v-for="l in letters" class="list-group-item">
        <a :href="url_for(l)">{{l.key}}</a>
        <ul class="list-group">
          <li v-for="attr in letter_attrs" v-if="attr != 'key'" class="list-group-item">
            <template v-if="attr == 'NEVERkey'">
              <a :href="url_for(l)"> {{l.key}} </a>
            </template>
            <template v-else>
              {{l[attr]}}
            </template>
          </li>
        </ul>
      </li>
    </ul>
  </div>

<?php } else { ?>
  Dashboard Ortschaft
<?php } ?>

