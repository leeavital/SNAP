<?php

require __DIR__ . "/../snap.php";

$module = new SNAPModule("FOOMODULE");
$module->registerFunction("foo", "bar", __DIR__ . "/bar.php");

SNAPTrigger ($module);
?>
