<?php
session_start();
$con=mysqli_connect("localhost","root","","studentdashboard");
if(!$con){
    die('Connection Error');
}
if(isset($_POST['btn'])){
    $a=$_POST['ff'];
    $b=$_POST['gg'];
    $qu="Select * from sign where uname='$a' and pass='$b' ";
    $res=mysqli_query($con,$qu);
    if(mysqli_num_rows($res)==1){
        $user = mysqli_fetch_assoc($res);
        $_SESSION['name']=$user['name'];
        header("Location:1stTask.php");
    } 
    else{
        echo "Invalid credentials.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="ff" id="" placeholder="Enter username">
        <input type="text" name="gg" id="" placeholder="Enter password">
        <button name="btn">Login</button>
    </form>
</body>
</html>