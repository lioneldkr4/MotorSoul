<?php
session_start();
session_destroy();
header("Location: iniciarsesionadmin.php");
exit();
