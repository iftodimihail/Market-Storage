<?php
    require 'database.php';
    $id = null;
    if ( !empty($_GET['contact_id'])) {
        $id = $_REQUEST['contact_id'];
    }
     
    if ( null==$id ) {
        header("Location: index.php");
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT CITY, STR_NAME, PHONE
				FROM CONTACT
				WHERE CONTACT_ID=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>
 
<body>
    <div class="container">
     
                <div class="span10 offset1">
                    <div class="row">
                        <h3>Distribuitor</h3>
                    </div>
                     
                    <div class="form-horizontal" >
                      <div class="control-group">
                        <label class="control-label">Oraș</label>
                        <div class="controls">
                            <label class="checkbox">
                                <?php echo $data['CITY'];?>
                            </label>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label">Adresa</label>
                        <div class="controls">
                            <label class="checkbox">
                                <?php echo $data['STR_NAME'];?>
                            </label>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label">Număr de telefon</label>
                        <div class="controls">
                            <label class="checkbox">
                                <?php echo $data['PHONE'];?>
                            </label>
                        </div>
                      </div>
                    </div>
                </div>
                 
    </div> <!-- /container -->
  </body>
</html>