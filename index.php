<?php
    if(isset($_POST['fname']) && isset($_FILES['profile']))
    {
        include('db_config.php');

        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $name = $fname." ".$lname;
        $email = $_POST['email'];
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $profile = $_FILES['profile'];

        $u_exists = select("SELECT * FROM user_cred WHERE username=? LIMIT 1",[$username],'s');
        $e_exists = select("SELECT * FROM user_cred WHERE email=? LIMIT 1",[$email],'s');
        $profile_name = $profile['name'];
        $profile_size = $profile['size'];
        $profile_tmp = $profile['tmp_name'];
        $profile_error = $profile['error'];
        //match password and confirm password
        if($password!=$cpassword){
            echo "<div class='alert alert-danger'>
            <strong>Passwords do not match</strong>
            </div>";

            //remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";

        } //check if username exists or not
        else if(mysqli_num_rows($u_exists)>0){
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Username already exists</strong>
            </div>";

            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";

        }//check if email exists or not
        else if(mysqli_num_rows($e_exists)>0){
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Email already exists</strong>
            </div>";
        }//if name contains any number or symbol
        else if(preg_match('/[0-9]/',$fname) || preg_match('/[0-9]/',$lname) || preg_match('/[!@#$%^&*(),.?":{}|<>]/',$fname) || preg_match('/[!@#$%^&*(),.?":{}|<>]/',$lname)){
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Name cannot contain numbers or symbols</strong>
            </div>";

            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";

        }//password must contain at least 8 characters
        else if(strlen($password)<8)
        {
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Password Must Conatin at least 8 characteres</strong>
            </div>";

            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
        else if($profile_error == 0)
        {
            if($profile_size >300000){
                echo "<div class='alert custom-alert alert-danger'>
                <strong>Sorry, your file is too large.</strong>
                </div>
                ";

                echo "<script>
                setTimeout(function(){
                    document.querySelector('.custom-alert').remove();
                },3000);
                </script>";
            }
            else{
                $profile_ex = pathinfo($profile_name,PATHINFO_EXTENSION);

                $profile_ex_lc = strtolower($profile_ex);

                $allowed_exs = array("jpg","jpeg","png");

                if(in_array($profile_ex_lc,$allowed_exs)){
                    $new_profile_name = uniqid("IMG-",true).'.'.$profile_ex_lc;
                    $profile_upload_path = 'users/'.$new_profile_name;
                    move_uploaded_file($profile_tmp,$profile_upload_path);
                //Insert into the database  
                $sql = "INSERT INTO user_cred (name, email, phone, image, username, password) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);

                if ($stmt) {
                // Bind the values to the placeholders
                mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $phone, $new_profile_name, $username, $password);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<div class='alert alert-success custom-alert'>
                    <strong>Account Created Successfully</strong>
                    </div>";

                    echo "<script>
                    setTimeout(function(){
                        document.querySelector('.custom-alert').remove();
                    },3000);
                    </script>";
                    } else {
                        echo "<div class='alert custom-alert alert-danger'>
                        <strong>Sorry, Database Error.</strong>
                        </div>";

                        echo "<script>
                        setTimeout(function(){
                            document.querySelector('.custom-alert').remove();
                        },3000);
                        </script>";
                    }
                mysqli_stmt_close($stmt);
                    } else {
                        // Handle the prepare error
                        echo "Error preparing the statement: " . mysqli_error($con);

                        echo "<script>
                        setTimeout(function(){
                            document.querySelector('.custom-alert').remove();
                        },3000);
                        </script>";
                    }
                }else {
                    echo "<script>alert('error','Image must be in jpg, jpeg or png format')</script>";

                    echo "<script>
                    setTimeout(function(){
                        document.querySelector('.custom-alert').remove();
                    },3000);
                    </script>";
                }
            }
        } else {
            echo "<div class='alert alert-danger custom-alert'>
            <strong>Sorry, there was an error uploading your file.</strong>
            </div>";

            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
    }     



    function filteration($data)
    {
        foreach($data as $key => $value)
        {
            $value=trim($value);
            $value=stripslashes($value);
            $value=strip_tags($value);
            $value=htmlspecialchars($value);
            $data[$key] = $value;
        }
        return $data;
    }

    function selectAll($table)
    {
        $con = $GLOBALS['con'];
        $res = mysqli_query($con,"SELECT * FROM $table");
        return $res;
    }


    function select($sql,$values,$datatypes)
    {
        $con = $GLOBALS['con'];
        if($stmt = mysqli_prepare($con,$sql))
        {
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res =  mysqli_stmt_get_result($stmt);
                mysqli_stmt_close(($stmt));
                return $res;
            }
            else
            {
                mysqli_stmt_close(($stmt));
                die("Query cannot be executed - Select");
            }
        }
        else
        {
            die("Query cannot be prepared - Select");
        }
    }

    function insert($sql,$values,$datatypes)
    {
        $con = $GLOBALS['con'];
        if($stmt = mysqli_prepare($con,$sql))
        {
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res =  mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close(($stmt));
                return $res;
            }
            else
            {
                mysqli_stmt_close(($stmt));
                die("Query cannot be executed - Insert");
            }
        }
        else
        {
            die("Query cannot be prepared - Insert");
        }
    }

    function update($sql,$values,$datatypes)
    {
        $con = $GLOBALS['con'];
        if($stmt = mysqli_prepare($con,$sql))
        {
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res =  mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close(($stmt));
                return $res;
            }
            else
            {
                mysqli_stmt_close(($stmt));
                die("Query cannot be executed - Update");
            }
        }
        else
        {
            die("Query cannot be prepared - Update");
        }
    }
    function delete($sql,$values,$datatypes)
    {
        $con = $GLOBALS['con'];
        if($stmt = mysqli_prepare($con,$sql))
        {
            mysqli_stmt_bind_param($stmt,$datatypes,...$values);
            if(mysqli_stmt_execute($stmt))
            {
                $res =  mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close(($stmt));
                return $res;
            }
            else
            {
                mysqli_stmt_close(($stmt));
                die("Query cannot be executed - Update");
            }
        }
        else
        {
            die("Query cannot be prepared - Update");
        }
    }

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <title>User Registration System</title>
</head>
<body>

    <div class="container">
        <div class="head">
            <h2>Register</h2>
        </div>
        <div class="form">
            <form action="index.php" method="post" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" placeholder="Enter First Name">
                </div>
                <div class="input-group">
                    <label for="lname">Last Name</label>
                    <input type="text" id="lname" name="lname" placeholder="Enter Last Name">
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="uname" name="username" placeholder="Enter Username">
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email">
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="pass">
                        <input type="password" id="password1" name="password" placeholder="Enter Password">
                        <i id="pass1eye" class="bi bi-eye"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Confirm Password</label>
                    <div class="pass">
                        <input type="password" id="password2" name="cpassword" placeholder="Confirm Password">
                        <i id="pass2eye" class="bi bi-eye"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="number">Phone</label>
                    <input type="tel" name="phone" placeholder="Enter Phone Number">
                </div>
                <div class="input-group">
                    <label for="pp">Profile Picture</label>
                    <input type="file" name="profile" accept="image/*" name="pp">
                </div>
                <div class="input-group">
                    <input type="submit" name="submit" value="Submit" id="submit">
                </div>
            </form>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
