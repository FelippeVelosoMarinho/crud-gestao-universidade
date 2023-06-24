<?php
// instructor_update_process.php

// Database connection
require_once('../mysqli_connect.php');
require_once('../log.php');
include ("../header.html");

// Check if the form was submitted
if (isset($_POST['submit'])) {
  console_log($_POST);
  // Get the submitted form data
  $InstructorID = $_POST['InstructorID'];
  $DepartmentID = $_POST['DepartmentID'];
  $Email = $_POST['Email'];
  $FirstName = $_POST['FirstName'];
  $LastName = $_POST['LastName'];



  //First, check if the email was updated. If it was, update the email table for the Instructor EmailId
  $checkEmailSql = "SELECT * FROM Instructor JOIN Emails on Emails.EmailID = Instructor.EmailID WHERE InstructorID = ?";

  $stmt = $dbc->prepare($checkEmailSql);
  console_log("Instructor ID is " . $InstructorID);
  $stmt->bind_param("i", $InstructorID);
  $stmt->execute();

  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  $stmt->close();
  $emailID = $row['EmailID'];
  $oldEmail = $row['EmailAddress'];

  if ($oldEmail !== $Email) {
    console_log("Email was updated. Email was ". $oldEmail . " and is now " . $Email);
    $updateEmailSql = "UPDATE Emails SET EmailAddress = ? WHERE EmailID = ?";
    $stmt = $dbc->prepare($updateEmailSql);
    $stmt->bind_param("si", $Email, $emailID);
    $stmt->execute();
  }




  // Update the record in the database
  $sql = "UPDATE Instructor SET DepartmentID = ?, FirstName = ?, LastName = ? WHERE InstructorID = ?";
  
  $stmt = $dbc->prepare($sql);
  $stmt->bind_param("issi", $DepartmentID, $FirstName, $LastName, $InstructorID);
  $result = $stmt->execute();

  echo "
  <body>
  <div class='container'>";
  if ($result) {
    // Success message and button to return to homepage
    echo "<div class='alert alert-success mt-5' role='alert'>
            <h4 class='alert-heading'>Success!</h4>
            <p>Record updated successfully.</p>
            
          ";
  } else {
    echo "<div class='alert alert-danger mt-5' role='alert'>
    <h4 class='alert-heading'>Error!</h4>
            Error updating record: " . $dbc->error . "
          ";
  }
  echo "
  <hr>
  <a href='../index.php' class='btn btn-primary'>Return to Home</a>
  </div>
  ";
  // End HTML output
  echo "</div></body></html>";

  $stmt->close();
}

$dbc->close();
?>
