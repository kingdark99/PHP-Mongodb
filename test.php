<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<?php
// define variables and set to empty values
$nameErr = $emailErr = $genderErr = $websiteErr = "";
$name = $email = $gender = $comment = $website = $id="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {



if (isset($_POST['delete'])) {

  $m = new MongoClient();
  $db = $m->mydb;
  $collection = $db->mycol;
  $collection->remove(array("_id"=>new MongoId($_POST["delete"])));

  //header("Refresh:0");
}
elseif (isset($_POST['edit'])) {
  $m = new MongoClient();
  $db = $m->mydb;
  $collection = $db->mycol;
  $cursor=$collection->findone(array("_id"=>new MongoId($_POST["edit"])));

  $name =$cursor['name'];
   $email = $cursor["e-mail"];
   $gender = $cursor["Sex"] ;
   $comment = $cursor["comment"] ;
   $website = $cursor["website"];
   $id=$cursor["_id"];


}
else {

  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed";
    }



  }

  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }

  if (empty($_POST["website"])) {
    $website = "";
  } else {
    $website = test_input($_POST["website"]);
    // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
      $websiteErr = "Invalid URL";
    }
  }

  if (empty($_POST["comment"])) {
    $comment = "";
  } else {
    $comment = test_input($_POST["comment"]);
  }

  if (empty($_POST["gender"])) {
    $genderErr = "Gender is required";
  } else {
    $gender = test_input($_POST["gender"]);
  }



  if ($name!=""&$gender!=""&$email!="") {
    $m = new MongoClient();
    $db = $m->mydb;
    $collection = $db->mycol;
    if ($id="") {
      $document = array(
          "name" => $name,
          "e-mail" => $email,
          "website" => $website,
          "comment" => $comment,
          "Sex"=>$gender
       );
       $collection->insert($document);
    }
     else {
       $collection->update(
         array('_id' =>  new MongoId($_POST["hdnCmd"])),
         array('$set' =>array(
           "name" => $name,
           "e-mail" => $email,
           "website" => $website,
           "comment" => $comment,
           "Sex"=>$gender))
         );

     }
      header("Refresh:0");
  }
}

}


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}



?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field.</span></p>
<form name="asd" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<input type="hidden" name="hdnCmd" value="<?php echo $id;?>">
  Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Website: <input type="text" name="website" value="<?php echo $website;?>">
  <span class="error"><?php echo $websiteErr;?></span>
  <br><br>
  Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
  <br><br>
  Gender:
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="female") echo "checked";?> value="female">Female
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="male") echo "checked";?> value="male">Male
  <span class="error">* <?php echo $genderErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="save">


<?php
$m = new MongoClient();
 $db = $m->mydb;
 $collection = $db->mycol;

echo "<h2>LIST:</h2>";
 $cursor = $collection->find();
 echo "
 <table border='1'>
<tr>
<th style='display:none;'></th>
<th>name</th>
<th>e-mail</th>
<th>website</th>
<th>comment</th>
<th>Sex</th>
<th>Remove</th>
<th>Edit</th>
</tr>";
 foreach ($cursor as $document) {
      echo "<tr>";
      echo "<td style='display:none;'><input type='text' name='deleteID'  value=".$document["_id"]."></td>";
      echo "<td>" . $document["name"]. "</td>";
      echo "<td>" . $document["e-mail"] . "</td>";
      echo "<td>" . $document["website"] . "</td>";
      echo "<td>" . $document["comment"] . "</td>";
      echo "<td>" . $document["Sex"] . "</td>";
      echo "<td><input type='submit' name='delete'  value=".$document["_id"]." ></td>";
      echo "<td><input type='submit' name='edit'  value=".$document["_id"]." '></td>";
      echo "</tr>";

   }

?>
</form>
</body>
</html>
