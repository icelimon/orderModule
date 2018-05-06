
<?php
include_once 'includes/header.php';
?>
<title><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name']: "Oracle";?></title>
</head>
<body>

    <?php
    include_once 'includes/navbar.php';

    include_once 'connect.php';
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }



if(!isset($_SESSION['loggedin'])){
    header("Location: index.php");
}

/*
if(isset($_POST['signup'])){
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['youremail'];
    $verifyemail = $_POST['reenteremail'];
    $password = md5($_POST['password']);

    $dob_month = $_POST['dob-month'];
    $dob_day = $_POST['dob-day'];
    $dob_year = $_POST['dob-year'];

    $gender = $_POST['sex'];

    $fullname = $fname.' '.$lname;
    $dob = $dob_day.' '.$dob_month.' '.$dob_year;

    $sql = "INSERT INTO user(fullname,email,password,dob,gender) VALUES('$fullname','$email', '$password', '$dob', '$gender')"; 
    $query = mysqli_query($conn, $sql);
    if($query){
        echo '<br>Signup Successful';
        echo '<a href="signin.php">Sign in</a>';
    }else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }
}
*/

if(isset($_POST['signin'])){

    $emp_id         = $_POST['emp-id'];
    $branch_id      = $_POST['branch-id'];
    $password       = $_POST['password'];
    $rand_string    = $_POST['rand'];

    if($rand_string != $_SESSION['rand_str']){
        die('Stop gentle man!!');
    }
    //$password = 'admin';
    $sql = "SELECT EMP_ID, EMP_NICKNAME, COMPANY_ID, BRANCH_ID FROM sndms.SOFTWARE_USER_INFO WHERE USER_ID = :emp_id AND BRANCH_ID = :branch_id AND USER_PASS_WORD=:password";
    $query = oci_parse($conn, $sql);
    oci_bind_by_name($query, ':emp_id', $emp_id);
    oci_bind_by_name($query, ':branch_id', $branch_id);
    oci_bind_by_name($query, ':password', $password);
    $exe=oci_execute($query);
    $emp_id = 0;
    $emp_name = '';
    while ($row = oci_fetch_array($query, OCI_RETURN_NULLS+OCI_ASSOC)) {
            $emp_id = $row['EMP_ID'];
            $emp_name = $row['EMP_NICKNAME'];
            $company_id = $row['COMPANY_ID'];
            $branch_id = $row['BRANCH_ID'];
    }
    if($emp_id>0 && !empty($emp_name)){
        $_SESSION['loggedin'] = 'loggedin';
        $_SESSION['emp_id'] = $emp_id;
        $_SESSION['emp_name'] = $emp_name;
        $_SESSION['company_id'] = $company_id;
        $_SESSION['branch_id'] = $branch_id;
        
        unset($_SESSION['rand_str']);
        unset($_SESSION['msg']);
        header("Location: profile.php");
    }else{
        $_SESSION['msg'] = 'Failed to log in!<br>Wrong Employee id or Brach id or Password.';
    }

    oci_free_statement($query);
   
    /*
    $nickname = $_POST['nickname'];
    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

// Prepare the statement
    $stid = oci_parse($conn, 'SELECT * FROM pmis.EMPLOYEE_INFO WHERE EMP_NICKNAME = :nickname ');
    if (!$stid) {
        $e = oci_error($conn);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }
    oci_bind_by_name($stid, ':nickname', $nickname);
// Perform the logic of the query
    $r = oci_execute($stid);
    if (!$r) {
        $e = oci_error($stid);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

// Fetch the results of the query
    print "<table border='1'>\n";
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        print "<tr>\n";
        foreach ($row as $item) {
            print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        }
        print "</tr>\n";
    }
    print "</table>\n";

    oci_free_statement($stid);
    */
    //oci_close($conn);



}





include_once 'includes/footer.php';
?>
