<?php
    // Check if the form is submitted
    if(isset($_POST['fname']) && isset($_FILES['profile']))
    {
        // Include the database configuration file
        include('db_config.php');

        // Retrieve form data
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $name = $fname." ".$lname;
        $email = $_POST['email'];
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $profile = $_FILES['profile'];

        // Check if username and email already exist in the database
        $u_exists = select("SELECT * FROM user_cred WHERE username=? LIMIT 1",[$username],'s');
        $e_exists = select("SELECT * FROM user_cred WHERE email=? LIMIT 1",[$email],'s');
        
        // Extract file details
        $profile_name = $profile['name'];
        $profile_size = $profile['size'];
        $profile_tmp = $profile['tmp_name'];
        $profile_error = $profile['error'];

        // Match password and confirm password
        if($password!=$cpassword){
            // Display an error message
            echo "<div class='alert alert-danger'>
            <strong>Passwords do not match</strong>
            </div>";

            // Remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        } 
        // Check if username exists
        else if(mysqli_num_rows($u_exists)>0){
            // Display an error message
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Username already exists</strong>
            </div>";

            // Remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
        // Check if email exists
        else if(mysqli_num_rows($e_exists)>0){
            // Display an error message
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Email already exists</strong>
            </div>";
        }
        // Check if name contains numbers or symbols
        else if(preg_match('/[0-9]/',$fname) || preg_match('/[0-9]/',$lname) || preg_match('/[!@#$%^&*(),.?":{}|<>]/',$fname) || preg_match('/[!@#$%^&*(),.?":{}|<>]/',$lname)){
            // Display an error message
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Name cannot contain numbers or symbols</strong>
            </div>";

            // Remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
        // Check if password length is at least 8 characters
        else if(strlen($password)<8)
        {
            // Display an error message
            echo "<div class='alert custom-alert alert-danger'>
            <strong>Password must contain at least 8 characters</strong>
            </div>";

            // Remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
        // Check if the profile picture is uploaded successfully
        else if($profile_error == 0)
        {
            // Check if the file size is within limits
            if($profile_size >300000){
                // Display an error message
                echo "<div class='alert custom-alert alert-danger'>
                <strong>Sorry, your file is too large.</strong>
                </div>
                ";

                // Remove error message after 3 seconds
                echo "<script>
                setTimeout(function(){
                    document.querySelector('.custom-alert').remove();
                },3000);
                </script>";
            }
            else{
                // Get file extension and convert it to lowercase
                $profile_ex = pathinfo($profile_name,PATHINFO_EXTENSION);
                $profile_ex_lc = strtolower($profile_ex);

                // Allowed file extensions
                $allowed_exs = array("jpg","jpeg","png");

                // Check if the file extension is allowed
                if(in_array($profile_ex_lc,$allowed_exs)){
                    // Generate a unique profile picture name
                    $new_profile_name = uniqid("IMG-",true).'.'.$profile_ex_lc;
                    $profile_upload_path = 'users/'.$new_profile_name;

                    // Move the uploaded file to the specified path
                    move_uploaded_file($profile_tmp,$profile_upload_path);

                    // Insert user data into the database
                    $sql = "INSERT INTO user_cred (name, email, phone, image, username, password) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($con, $sql);

                    if ($stmt) {
                        // Bind the values to the placeholders
                        mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $phone, $new_profile_name, $username, $password);

                        // Execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Display a success message
                            echo "<div class='alert alert-success custom-alert'>
                            <strong>Account Created Successfully</strong>
                            </div>";

                            // Remove success message after 3 seconds
                            echo "<script>
                            setTimeout(function(){
                                document.querySelector('.custom-alert').remove();
                            },3000);
                            </script>";
                        } else {
                            // Display a database error message
                            echo "<div class='alert custom-alert alert-danger'>
                            <strong>Sorry, Database Error.</strong>
                            </div>";

                            // Remove error message after 3 seconds
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

                        // Remove error message after 3 seconds
                        echo "<script>
                        setTimeout(function(){
                            document.querySelector('.custom-alert').remove();
                        },3000);
                        </script>";
                    }
                } else {
                    // Display an error message if the file format is not allowed
                    echo "<script>alert('error','Image must be in jpg, jpeg, or png format')</script>";

                    // Remove error message after 3 seconds
                    echo "<script>
                    setTimeout(function(){
                        document.querySelector('.custom-alert').remove();
                    },3000);
                    </script>";
                }
            }
        } else {
            // Display an error message if there was an error uploading the file
            echo "<div class='alert alert-danger custom-alert'>
            <strong>Sorry, there was an error uploading your file.</strong>
            </div>";

            // Remove error message after 3 seconds
            echo "<script>
            setTimeout(function(){
                document.querySelector('.custom-alert').remove();
            },3000);
            </script>";
        }
    }

    // Function to filter input data
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

    // Function to select all records from a table
    function selectAll($table)
    {
        $con = $GLOBALS['con'];
        $res = mysqli_query($con,"SELECT * FROM $table");
        return $res;
    }

    // Function to execute a SELECT query with parameters
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

    // Function to execute an INSERT query with parameters
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

    // Function to execute an UPDATE query with parameters
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

    // Function to execute a DELETE query with parameters
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
                <!-- Form fields -->
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
