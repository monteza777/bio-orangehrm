<?php
$conn = mysqli_connect("localhost", "root", "","orangehrm_mysql");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}