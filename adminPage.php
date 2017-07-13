<div>
<h2>tablebooker</h2>
<p>Set up the tablebooker plugin.</p>
<form action="options.php" method="post">
<?php settings_fields('tablebooker_options'); ?>
<?php do_settings_sections('tablebooker'); ?>
<?php submit_button(); ?>
</form></div>