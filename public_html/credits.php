<?php
response_header('Credits');
?>

<h2>Credits</h2>

<h3>PEAR website team</h3>

<ul>
  <li><?php echo user_link("ssb"); ?></li>
  <li><?php echo user_link("cox"); ?></li>
  <li><?php echo user_link("mj"); ?></li>
  <li><?php echo user_link("cmv"); ?></li>
</ul>

<h3>PEAR documentation team</h3>

<ul>
  <li><?php echo user_link("cox"); ?></li>
  <li><?php echo user_link("mj"); ?></li>
  <li><?php echo user_link("alexmerz"); ?></li>
</ul>

<small>(All in alphabetic order)</small>

<?php
response_footer();
?>
