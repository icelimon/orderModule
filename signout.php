<?php
include_once 'includes/header.php';
unset($_SESSION['loggedin']);
unset($_SESSION['emp_id']);
unset($_SESSION['emp_name']);
unset($_SESSION['company_id']);
unset($_SESSION['branch_id']);

if(isset($_SESSION['loggedin'])){
    header("Location: profile.php");
}else{
	 header("Location: index.php");
}

?>