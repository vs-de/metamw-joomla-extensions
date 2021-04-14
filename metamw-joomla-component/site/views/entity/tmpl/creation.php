<?php
  $g = $this->g;
  if (isset($g->creation)) {
    $c = $g->creation;
    $key = $c['key'];
  ?>
  <div id="creation-container" data-key="<?php echo $key;?>">
    <?php echo '['.$key.']'; ?>
    <h1>
    <?php echo $c['name']; ?>
    </h1>
    <ul>
    <?php
      foreach($c['authors'] as $atr) {
        ?> 
      <li class="list-unstyled">
        <a href="<?php echo $g->config['base_path'].$g->config['person_path'].'/'.$atr['key'] ?>">
          <?php echo $atr['last_name'].', '.$atr['first_name'];?>
        </a>
      </li>
      <?php
      }
    ?>
    </ul>
    <h2> in Briefen: </h2> 
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
          <li class="list-group-item">
            <ul class="list-group">
              <li v-for="spot in l.spots" v-html="spot" class="list-group-item"></li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </div>
<?php } else { ?>
  Dashboard Werke
<?php } ?>
