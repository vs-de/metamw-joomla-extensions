<?php
  $g = $this->g;
  if (isset($g->protag_creation)) {
    $pc = $g->protag_creation;
    $key = $pc['key'];
  ?>
  <div id="protag-creation-container" data-key="<?php echo $key;?>">
    <?php echo '['.$key.']'; ?>
    <h1>
    <?php echo $pc['name']; ?>
    </h1>
    <p>
    <?php
      if (!empty($pc['op'])) {
        echo "Opus ".$pc['op'];
      }
      if (!empty($pc['mwv'])) {
        echo "<br/>MWV ".$pc['mwv'];
      }
    ?>
    </p>
    Kategorie: <?php echo $pc['protag_creation_category']['name'] ?>
    <ul>
    <?php
      foreach($pc['parent_categories'] as $pcc) {
        ?> 
      <li class="list-unstyled">
        <!--<a href="/register/.../<?php //echo $atr['key'] ?>"> -->
        in  <?php echo $pcc['name'] ?>
        <!-- </a> -->
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

