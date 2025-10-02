<?php
  ob_start(); // ensures resfreshing works properly

  //connect to database
  include "task_connect.php";
  $db = connect();

  //selected date database
  $serviceDate = $db->query("SELECT * FROM currDate");
  $currDate = $serviceDate->fetch(PDO::FETCH_ASSOC);

  //defaults to Greece timezone
  date_default_timezone_set('Europe/Athens');
  $day = date("d");
  $month = date("m");

  //task editing variables
  $change = FALSE;
  $idEdit = 0;

  //totals variables
  $totalBookings = 0;
  $totalPrice = 0;
?>


<!-- html code -->
<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bookings Manager</title>
    
    <!-- main css code -->
    <style>
        .header {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;      
          margin: 16px auto 0;
          padding: 0 16px;
        }

        main {
          margin: 24px auto;
          padding: 0 16px;
          text-align: center;            
        }

        #datePicker {
          border: 2px solid black;
          left: 10px;
          top: 140px;
          background-color: lightsalmon;
          padding: 10px;
          width: 300px;
        }

        #qBox {
          border: 5px solid black;
          width: 500px;
          background-color: floralwhite;
        }

        #totals {
          margin-top: 40px;             
          width: 220px;
          border: 1px solid black;
          background-color: floralwhite;
          float: right;
        }

        .buttons {
          color: black;
          background-color: floralwhite;
          border: 2px solid black;
          border-radius: 0px;
        }

        .dates {
          padding: 3px;
          border: 1px solid black;
        }

        input[type="text"] {
          padding: 10px;
          border: 1px solid black;
        }

        input[type="submit"] {
          color: black;
          border-radius: 10px;
          padding: 5px;
          border: 2px solid black;
        }

        th {
          padding-left: 25px;
          padding-right: 25px;
          border: 2px black solid;
          background-color: floralwhite;
        }

        td {
          border: 2px solid black;
          padding-left: 10px;
          padding-right: 10px;
        }

        p {
          color: salmon;
        }
    </style>
  </head>


  <main>
    <div class="header">
      <!-- code to select date of displayed tasks -->
      <div id="datePicker">
        <h3>Select Day</h3>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <!-- defaults to todays date in greek time -->
        <input class="dates" type="date" name="today" value="<?php echo date('Y-m-d');?>">
        <input type="submit" name="date_submit" style="background-color: floralwhite;">
        </form>
        <!-- date stored and obtained from database -->
        <?php if(isset($_POST['date_submit'])) {
          if (!empty($_POST['today'])) {
              $date = $_POST['today'];
              $db->query("UPDATE currDate SET currDate = '$date' WHERE id = 1");

              $day = date('d', strtotime($_POST['today']));
              $month = date('m', strtotime($_POST['today']));
          } else {
              ?>
              <center><p style = "color: black">Please fill out all fields before submitting</p></center>
              <?php
          }
        }

        $dateQuery = $db->query("SELECT * FROM currDate WHERE id = 1");
        $date = $dateQuery->fetch(PDO::FETCH_ASSOC);
        $date = date("m/d/y", strtotime($date['currDate']));
        ?>
        <h4>Current Selected Date: <?=$date?> </h4>
      </div>
    </div>

    <br></br>
    <center><h1>Bookings Manager</h1></center>

    <!-- div containing submission form -->
    <br></br>
    <center><div id="qBox">
    <h2>Input Booking</h2>

    <?php
    //handles submit button
    if (isset($_POST['submit'])) {
      try {
        //checks that price is a numerical value
        doubleval($_POST['price']);
        //if editing vs submitting
        if ($_POST['change'] == 'true'){
          $id = $_POST['id'];

          if(!empty($_POST['description'])) {
            $description = $_POST['description'];
            $test = $db->prepare("UPDATE tasks SET description = :description WHERE id = :id");
            $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
            $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
            $test->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

          }

          if($_POST['price'] !== '') {
            $description = $_POST['price'];
            $test = $db->prepare("UPDATE tasks SET price = :description WHERE id = :id");
            $test->bindValue(':description', $description); //ensures correct parameter
            $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
            $test->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
          }

          if(!empty($_POST['company'])) {
            $description = $_POST['company'];
            $test = $db->prepare("UPDATE tasks SET company = :description WHERE id = :id");
            $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
            $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
            $test->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
          }

          if(!empty($_POST['time'])) {
            $description = $_POST['time'];
            $test = $db->prepare("UPDATE tasks SET currTime = '$description' WHERE id = :id");
            $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
            $test->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
          }

        
      } else {

          //check if all values are complete first
          if(!empty($_POST['description']) && ($_POST['price'] !== '') && !empty($_POST['company']) && !empty($_POST['time'])) {
            $description = $_POST['description'];
            $price = $_POST['price'];
            $company = $_POST['company'];

            $time = $_POST['time'];
            $date = $currDate['currDate'];

            $test = $db->prepare("INSERT INTO tasks (description, price, company, completed, currTime, currDate) VALUES (:description,:price, :company, FALSE, '$time', '$date')");
            $test->bindValue(':description', $description, PDO::PARAM_STR); //ensures correct parameter
            $test->bindValue(':price', $price); //ensures correct parameter
            $test->bindValue(':company', $company, PDO::PARAM_STR); //ensures correct parameter
            $test->execute();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

          } else {
            ?>
            <center><p>Please fill out all fields before submitting <br></br></p>
            <?php
          }

        }
      } catch (Exception $e) {
        ?>
        <center><p>Make sure price is a numerical value<br></br></p>
        <?php
      }

    } elseif (isset($_POST['delete'])){
      //handles delete button submission
      $id = $_POST['id'];
      $test = $db->prepare("DELETE FROM tasks WHERE id = :id");
      $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
      $test->execute();
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;

    } elseif (isset($_POST['complete'])){
      //handles complete/incomplete button submission
      $id = $_POST['id'];
      $val = $_POST['val'];
      $test = $db->prepare("UPDATE tasks SET completed = !$val WHERE id = :id");
      $test->bindValue(':id', $id, PDO::PARAM_INT); //ensures correct parameter
      $test->execute();
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    } elseif (isset($_POST['edit'])){
      //handles edit button submission
      $idEdit = $_POST['id'];
      ?>

      <center><p>Fill out fields you want to change<br></br></p>

    <?php
      $change = TRUE; //boolean for editing vs. submission mode of form

    }
    ?>

      <form method="post"; action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" placeholder="Booking Description" name="description">
        <br></br>

        <input type="text" placeholder="Price" name="price">
        <br></br>

        <input type="text" placeholder="Company" name="company">
        <br></br>
        
        <input class="dates" type="time" name="time" class="<?php echo date("h:i");?>">
        <br></br>
      
        <input type="submit" name="submit" value="Submit" style="background-color: lightsalmon;">

        <?php $val = $change ? 'true' : 'false';?>

        <input type="hidden" name="change" value="<?=$val?>">
        <input type="hidden" name="id" value="<?=$idEdit?>">
        <br></br>
      </form>
    </div>

    <br></br>

    <!-- table displaying tasks -->
    <table style="border-collapse: collapse;">
    <!-- display column names -->
    <tr>
        <th>Booking Description</th>
        <th>Price</th>
        <th>Start Time</th>
        <th>Company</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    //query in ascending order of date
      $taskQuery = $db->query("SELECT * FROM tasks ORDER BY currTime ASC");
      $results = $taskQuery->fetchAll(PDO::FETCH_ASSOC);
    //query tracking date
      $dateQuery = $db->query("SELECT * FROM currDate WHERE id = 1");
      $date = $dateQuery->fetch(PDO::FETCH_ASSOC);

      foreach ($results as $result) {
        //checks if task is completed and assigns val
        $val = '';

        if ($result['completed']) {
          $val = 'True';
        } else {
          $val = 'False';
        }

        if ($date['currDate'] == $result['currDate']) {
        ?>
      
        <tr>
        <!-- prints out the correct information under each column for each row -->
          <td>
          <?php
            echo $result['description'] . "</td>";
          ?>

          <td>
          <?php
            echo $result['price'] . "</td>";
            $totalBookings++;
            $totalPrice += $result['price'];
          ?>

          <td>
          <?php
            echo date('H:i', strtotime($result['currTime'])) . "</td>";
          ?>

          <td>
          <?php
            echo $result['company'] . "</td>";
          ?>

          <?php
          //check if each task is completed and display appropriately
            $completed = '';
            if ($result['completed']) {
              $completed = 'Mark as Incomplete';
              ?>
              <td style="padding-top: 10px; background-color: lightseagreen;">
                <center><b><div>Complete</div>
              <?php
            } else {
              $completed = 'Mark as Complete';
              ?>
              <center><td style="padding-top: 10px; background-color: lightcoral;">
              <center><b><div>Incomplete</div>
              <?php
            }
          ?>

          <!-- completed button next to each task -->
          <form method="POST" style="padding: 10px">
            <!-- result set to value of completed -->
            <button type="submit" class="buttons" name="complete"><?=$completed?></button>
            <input type="hidden" name="id" value="<?=$result['id']?>">
            <input type="hidden" name="val" value="<?=$result['completed']?>">
            <input type="hidden" name="complete" value="true">
          </form>

          <td>
            <!-- delete button next to each task -->
            <center>
            <form method="POST">
              <button type="submit" class="buttons" name="edit">Edit</button>
              <input type="hidden" name="id" value="<?=$result['id']?>">
              <input type="hidden" name="edit" value="true">
            </form>

            <!-- delete button next to each task -->
            <form method="POST" style="padding-top: 5px;">
              <button type="submit" class="buttons" name="delete">Delete</button>
              <input type="hidden" name="id" value="<?=$result['id']?>">
              <input type="hidden" name="delete" value="true">
            </form>
          </td>

        <br></br>
        </tr>

    <?php
        }
      }
    ?>

    </table>

    <div id="totals">
        <p>Total Bookings: <?php echo $totalBookings; ?></p>
        <p>Total Price: <?php echo $totalPrice; ?></p>
    </div>

    <br></br>
    <br></br>
    <br></br>

  <?php ob_end_flush(); ?>
  </main>


