<?php 
/**
 * This is a Mithridates pagecontroller.
 *
 */
// Include the essential config-file which also creates the $mithridates variable with its defaults.
include(__DIR__.'/config.php'); 

// Do it and store it all in variables in the Mithridates container.
$mithridates['title'] = "Välkommen!";
 
$mithridates['main'] = <<<EOD
<p>Hello World!</p>

EOD;
 

 
 
// Finally, leave it all to the rendering phase of Mithridates.
include(MITHRIDATES_THEME_PATH);
