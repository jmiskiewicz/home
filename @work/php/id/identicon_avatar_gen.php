<?php 
#
# Simple identicon generator interface
# Grabs name of variable to hash and size of avatar from URL vars
# Passes them all to identicon.php which gives you your identicon.
# Requires identicon.php here.
#
if (isset($_GET['hash'])) {
$hash = $_GET['hash'];
}
if (isset($_GET['size'])) {
$size = $_GET['size'];
}
$hash=md5($hash);
header("Location: identicon.php?size=$size&hash=$hash");
?>
