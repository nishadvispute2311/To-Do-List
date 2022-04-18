<?php

session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !==true)
{
    header("location: login.php");
}


?>

<?php
//Connecting to the Database
$insert=false;
$updated=false;
$deleted=false;
$servername = "localhost";
$username = "root";
$password ="";
$database="notes";

$conn = mysqli_connect($servername,$username,$password,$database);

if(!$conn){
    die("Failed ". mysqli_connect_error());
}

if(isset($_GET['delete'])){
  //Deleting the Record
  $sno=$_GET['delete'];
  $sql = "DELETE FROM `mynote` WHERE `mynote`.`sno` = '$sno'";
  $result=mysqli_query($conn,$sql);
  if($result){
    $deleted=true;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(isset($_POST['snoEdit'])){
    //Update the Record
    $sno=$_POST["snoEdit"];
    $title = $_POST["titleEdit"];
    $description = $_POST["descEdit"];
    $sql = "UPDATE `mynote` SET `title` = '$title' , `description` = '$description' WHERE `mynote`.`sno` = '$sno'";
    $result=mysqli_query($conn,$sql);
    if($result){
      $updated=true;
    }
    else{
      echo mysqli_error($conn);
    }
  }
  else{
    $title = $_POST['title'];
    $description = $_POST['desc'];

    $sql = "INSERT INTO `mynote`(`title`, `description`) VALUES ('$title','$description')";
    $result = mysqli_query($conn,$sql);
    if($result){
      $insert = true;
    }
    else{
      echo "Failed to insert : ". mysqli_error($conn);
    }
  }
}
?>




<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="//cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">


  <title>iTask - Easy To-Do List</title>
</head>

<body>

  <!-- Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/crud/index.php" method="post">
          <div class="modal-body">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="mb-3">
              <label for="titleEdit" class="form-label">Task Title</label>
              <input type="text" class="form-control" id="titleEdit" name="titleEdit">
            </div>
            <div class="mb-3">
              <label for="descEdit" class="form-label">Task Description</label>
              <textarea class="form-control" id="descEdit" name="descEdit" rows="3"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">iTask</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only"></span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>

      
     
    </ul>

  <div class="navbar-collapse collapse">
  <ul class="navbar-nav ml-auto">
  <li class="nav-item active">
        <a class="nav-link" href="#"> <img src="https://img.icons8.com/metro/26/000000/guest-male.png"> <b><?php echo "Welcome ". $_SESSION['username']?></b></a>
      </li>
  </ul>
  </div>


  </div>
</nav>

  <?php
    if($insert){
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!! </strong>Your task is added to the table successfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }

    if($updated){
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!! </strong>Your task is updated successfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }

    if($deleted){
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!! </strong>Your task is deleted successfully
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
  ?>



  <div class="container my-5 mx-40">
    <h2>Add a Task</h2><br>
    <form action="/crud/index.php" method="post">
      <div class="mb-3">
        <label for="title" class="form-label">Task Title</label>
        <input type="text" class="form-control" id="title" name="title">
      </div>
      <div class="mb-3">
        <label for="desc" class="form-label">Task Description</label>
        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Add Task</button>
      </div>
    </form>
  </div>




  <div class="container my-5">
    <table class="table" id="myTable">
      <thead>
        <tr>
          <th scope="col">S.No</th>
          <th scope="col">Title</th>
          <th scope="col">Description</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>

        <?php
          $srno=0;
          $sql="SELECT * FROM mynote";
          $result=mysqli_query($conn,$sql);
          while($row = mysqli_fetch_assoc($result)){
                $srno=$srno+1;
                echo "<tr>
                <th scope='row'>". $srno ."</th>
                <td>". $row['title'] ."</td>
                <td>". $row['description'] ."</td>
                <td><button class='edit btn btn-sm btn-primary' id=". $row['sno'] .">Edit</button> <button class='delete btn btn-sm btn-primary' id=d". $row['sno'] .">Delete</button></td>
              </tr>";

          }
          
        ?>


      </tbody>
    </table>
  </div>


  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>
  <script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#myTable').DataTable();
    });
  </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
    crossorigin="anonymous"></script>

  <script>
    edits = document.getElementsByClassName('edit');
    Array.from(edits).forEach((element) => {
      element.addEventListener("click", (e) => {
        console.log("edit ",);
        tr = e.target.parentNode.parentNode;
        title = tr.getElementsByTagName("td")[0].innerText;
        description = tr.getElementsByTagName("td")[1].innerText;
        console.log(title, description)
        descEdit.value = description;
        titleEdit.value = title;
        snoEdit.value = e.target.id;
        console.log(e.target.id, snoEdit.value);
        $('#editModal').modal('toggle');
      })
    })


    deletes = document.getElementsByClassName('delete');
    Array.from(deletes).forEach((element) => {
      element.addEventListener("click", (e) => {
        console.log("delete ",);
        tr = e.target.parentNode.parentNode;
        title = tr.getElementsByTagName("td")[0].innerText;
        description = tr.getElementsByTagName("td")[1].innerText;
        sno = e.target.id.substr(1,);
        if (confirm("Are you sure you want to delete")) {
          console.log("yes bro");
          window.location = `/crud/index.php?delete=${sno}`;
        }
        else {
          console.log("no bhai");
        }
      })
    })

  </script>

</body>

</html>
