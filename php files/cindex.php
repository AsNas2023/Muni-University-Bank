<?php
session_start();
if(!isset($_SESSION['cashId'])){ header('location:login.php');}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Banking</title>
</head>
  <?php require 'assets/autoloader.php'; ?>
  <?php require 'assets/db.php'; ?>
  <?php require 'assets/function.php'; ?>
  <?php $note ="";
    if (isset($_POST['withdrawOther']))
    { 
      $accountNo = $_POST['otherNo'];
      $checkNo = $_POST['checkno'];
      $amount = $_POST['amount'];
      if(setOtherBalance($amount,'debit',$accountNo))
      $note = "<div class='alert alert-success'>successfully transaction done</div>";
      else
      $note = "<div class='alert alert-danger'>Failed</div>";

    }
    if (isset($_POST['withdraw']))
    {
      setBalance($_POST['amount'],'debit',$_POST['accountNo']);
      makeTransactionCashier('withdraw',$_POST['amount'],$_POST['checkno'],$_POST['userId']);
      $note = "<div class='alert alert-success'>successfully transaction done</div>";

    }
    if (isset($_POST['deposit']))
    {
      setBalance($_POST['amount'],'credit',$_POST['accountNo']);
      makeTransactionCashier('deposit',$_POST['amount'],$_POST['checkno'],$_POST['userId']);
      $note = "<div class='alert alert-success'>successfully transaction done</div>";

    }
   ?>
   </head>
<body style="background-size: 60%; background: url(images/mtransfer.jpg); background-color: #d1eeff" class="bg-gradient-seconday">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
 <a class="navbar-brand" href="#">
    <img src="images/muni.png" style="object-fit:cover;object-position:center center" width="30" height="30" class="d-inline-block align-top" alt="">
   <!--  <i class="d-inline-block  fa fa-building fa-fw"></i> --><?php echo bankname; ?>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item ">
        <a class="nav-link active" href="cindex.php" style="font-family: copperplate, fantasy; color: orange;> <span class="sr-only"><b>Home</b></span></a>
      </li>
      <!-- <li class="nav-item"><a class="nav-link" href="caccounts.php">Account Setting</a></li> -->
     <!--  <li class="nav-item"><a class="nav-link" href="statements.php">Account Statements</a></li>
      <li class="nav-item"><a class="nav-link" href="transfer.php">Funds Transfer</a></li> -->
      <!-- <li class="nav-item ">  <a class="nav-link" href="profile.php">Profile</a></li> -->
	  <!-- <li class="nav-item ">  <a class="nav-link" href="transfer.php">Funds Transfer</a></li> -->
      <!-- <li class="nav-item ">  <a class="nav-link" href="profile.php">Profile</a></li> -->
    </ul>
    <?php include 'csideButton.php'; ?>
  </div>
</nav><br><br><br>
<?php
if (isset($_POST['saveAccount']))
{
  if (!$con->query("insert into useraccounts (name,cnic,accountNo,accountType,city,address,email,password,balance,source,number,branch) values ('$_POST[name]','$_POST[cnic]','$_POST[accountNo]','$_POST[accountType]','$_POST[city]','$_POST[address]','$_POST[email]','$_POST[password]','$_POST[balance]','$_POST[source]','$_POST[number]','$_POST[branch]')")) {
    echo "<div claass='alert alert-success'>Failed. Error is:".$con->error."</div>";
  }
  else
    echo "<div class='alert alert-info text-center'>Account added Successfully</div>";

}
if (isset($_GET['del']) && !empty($_GET['del']))
{
  $con->query("delete from login where id ='$_GET[del]'");
}
  
  
 ?>
<div class="row w-100" style="padding: 11px" >
  <div class="col">
    <div class="card text-center w-105 mx-auto">
  <div class="card-header" style="font-family: Tahoma, sans-serif;">
    <b>Account Information</b>
  </div>
  <div class="card-body" style="background-color: #5F9EA0">
    <p class="card-text"><?php echo $note; ?>
      <form method="POST" id="left-content">
          <div class="alert alert-success w-50 mx-auto">
            <h5>Enter Account Number</h5>
            <input type="text" name="otherNo" class="form-control " placeholder="Enter  Account number" required>
            <button type="submit" name="get" class="btn btn-primary btn-bloc btn-sm my-1">Get Account Info</button>
          </div>
      </form>
    </p>
    <?php if (isset($_POST['get'])) 
      {
        $array2 = $con->query("select * from otheraccounts where accountNo = '$_POST[otherNo]'");
        $array3 = $con->query("select * from userAccounts where accountNo = '$_POST[otherNo]'");
        {
          if ($array2->num_rows > 0) 
          { $row2 = $array2->fetch_assoc();
            echo "<div class='row'>
                  <div class='col'>
                  <form method='POST'>
                    Account No.
                    <input type='text' value='$row2[accountNo]' name='otherNo' class='form-control ' readonly required>
                    Account Holder Name.
                    <input type='text' class='form-control' value='$row2[holderName]' readonly required>
                    Account Holder Bank Name.
                    <input type='text' class='form-control' value='$row2[bankName]' readonly required>
                     
                  
                  </div>
                  <div class='col'>
                    Bank Balance
                    <input type='text' class='form-control my-1'  value='UGX.$row2[balance]' readonly required>
                    <input type='number' class='form-control my-1' name='checkno' placeholder='Write Check Number' required>
                    <input type='number' class='form-control my-1' name='amount' placeholder='Write Amount' max='$row2[balance]' required>
                   <button type='submit' name='withdrawOther' class='btn btn-success btn-bloc btn-sm my-1'> Withdraw</button></form>
                  </div>
                </div>";
          }elseif ($array3->num_rows > 0) {
           $row2 = $array3->fetch_assoc();
            echo "
            <div class='row'>
                  <div class='col'>
                  
                    Account No.
                    <input type='text' value='$row2[accountNo]' name='otherNo' class='form-control ' readonly required>
                    Account Holder Name.
                    <input type='text' class='form-control' value='$row2[name]' readonly required>
                    Account Holder Bank Name.
                    <input type='text' class='form-control' value='".bankname."' readonly required>Bank Balance
                    <input type='text' class='form-control my-1'  value='UGX.$row2[balance]' readonly required>
                     
                  
                  </div>
                  <div class='col'>
                    Transaction Process.
                    <form method='POST'>
                     
                    <input type='hidden' value='$row2[accountNo]' name='accountNo' class='form-control ' required>
                    <input type='hidden' value='$row2[id]' name='userId' class='form-control ' required>
                    <input type='number' class='form-control my-1' name='checkno' placeholder='Write Check Number' required>
                    <input type='number' class='form-control my-1' name='amount' placeholder='Write Amount for withdraw' max='$row2[balance]' required>
                   <button type='submit' name='withdraw' class='btn btn-primary btn-bloc btn-sm my-1'> Withdraw</button></form><form method='POST'> 
                    <input type='hidden' value='$row2[accountNo]' name='accountNo' class='form-control ' required>
                    <input type='hidden' value='$row2[id]' name='userId' class='form-control ' required>
                   <input type='number' class='form-control my-1' name='checkno' placeholder='Write Check Number' required>
                    <input type='number' class='form-control my-1' name='amount' placeholder='Write Amount for deposit'  required>

                   <button type='submit' name='deposit' class='btn btn-success btn-bloc btn-sm my-1'> Deposit</button></form>
                  </div>
                </div>
            ";
          }
          else
            echo "<div class='alert alert-success w-50 mx-auto'>Account No. $_POST[otherNo] Does not exist</div>";
        }
  } 
      ?>
  </div>
    <div class="card-footer text-muted">
    <?php echo bankname; ?>
  </div>
</div>
  </div>
</div>
<div class="container">
<div class="card w-70 text-center shadowBlue" >
  <div class="card-header">
  <b>Create New Account </b>
  </div>
  <div class="card-body bg-dark text-white">
    <table class="table">
      <tbody>
        <tr>
          <form method="POST" id="right-content">
          <th>Name</th>
          <td><input type="text" name="name" class="form-control input-sm" required></td>
          <th>CNIC</th>
          <td><input type="number" name="cnic" class="form-control input-sm" required></td>
        </tr>
        <tr>
          <th>Account Number</th>
          <td><input type="" name="accountNo" readonly value="<?php echo time() ?>" class="form-control input-sm" required></td>
          <th>Account Type</th>
          <td>
            <select class="form-control input-sm" name="accountType" required>
              <option value="current" selected>Current</option>
              <option value="saving" selected>Saving</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>City</th>
          <td><input type="text" name="city" class="form-control input-sm" required></td>
          <th>Address</th>
          <td><input type="text" name="address" class="form-control input-sm" required></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><input type="email" name="email" class="form-control input-sm" required></td>
          <th>Password</th>
          <td><input type="password" name="password" class="form-control input-sm" required></td>
        </tr>
        <tr>
          <th>Deposit</th>
          <td><input type="number" name="balance" min="500" class="form-control input-sm" required></td>
          <th>Source of income</th>
          <td><input type="text" name="source" class="form-control input-sm" required></td>
        </tr>
        <tr>
          <th>Contact Number</th>
          <td><input type="number" name="number"  class="form-control input-sm" required></td>
          <th>Branch</th>
          <td>
            <select name="branch" required class="form-control input-sm">
              <option selected value="">Please Select..</option>
              <?php 
                $arr = $con->query("select * from branch");
                if ($arr->num_rows > 0)
                {
                  while ($row = $arr->fetch_assoc())
                  {
                    echo "<option value='$row[branchId]'>$row[branchName]</option>";
                  }
                }
                else
                  echo "<option value='1'>Main Branch</option>";
               ?>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <button type="submit" name="saveAccount" class="btn btn-primary btn-sm">Save Account</button>
            <button type="Reset" class="btn btn-secondary btn-sm">Reset</button></form>
          </td>
        </tr>
      </tbody>
    </table>
    

  </div>
  <div class="card-footer text-muted">
    <?php echo bankname; ?>
  </div>
</div>

</body>
</html>