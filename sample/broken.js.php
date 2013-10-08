<?php

   /**
    * this demonstrates case where you try to register a function
	* that cannot be found.
	*
	* This will result in a warning being put to the js console
	*/


   
    require __DIR__ . "/../snap.php";

    $module = new SNAPModule( "BROKENMODULE" );

	// Note, this is impossible because doesnotexist.php does not exist
	$module->registerFunction( "foo", "bar", "doesnotexist.php");

   
    // If we are in JS mode, this will put a warning to the console
	SNAPTrigger( $module );


?>
