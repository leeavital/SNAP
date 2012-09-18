<?php

require "../snap.php";

$module = new SNAPModule("FOOMODULE");
$module->registerFunction("foo", "bar", "bar.php");

SNAPTrigger ($module);
?>
