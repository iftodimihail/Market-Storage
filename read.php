<?php
    require 'database.php';
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    if ( null==$id ) {
        header("Location: index.php");
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql_1 = "SELECT PRODUCT_ID, PRODUCT_NAME, CATEGORY_NAME, SUPPLIER_NAME, PRODUCT_PRICE, DATE_FORMAT(STORAGE_DATE,'%d.%m.%Y') AS STORAGE_DATE
				FROM PRODUCTS P, CATEGORIES CAT, SUPPLIERS SUP
				WHERE P.CATEGORY_ID = CAT.CATEGORY_ID AND
				P.SUPPLIER_ID = SUP.SUPPLIER_ID AND
				P.PRODUCT_ID = ?";
		$sql_2 ="SELECT CONTACT_ID FROM SUPPLIERS
				WHERE SUPPLIER_NAME = ?";
				
        $q = $pdo->prepare($sql_1);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
		
		$query = $pdo->prepare($sql_2);
		$query->execute(array($data['SUPPLIER_NAME']));
		$contact_id = $query->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
    }
	
	
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
 
<body>
    <div style="padding-top: 40px;" class="container">
     
                <div class="span10 offset1">
                    <div style="padding-left: 15px"class="row">
                        <h3>Produs</h3>
                    </div>
                     
                    <div class="form-horizontal" >
                      <div class="control-group">
                        <label class="control-label"><strong>Nume:</strong></label>
                        <div style="display: inline-block" class="controls">
                            <label class="checkbox">
                                <?php echo $data['PRODUCT_NAME'];?>
                            </label>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label"><strong>Categorie:</strong></label>
                        <div style="display: inline-block" class="controls">
                            <label class="checkbox">
                                <?php echo $data['CATEGORY_NAME'];?>
                            </label>
                        </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label"><strong>Distribuior:</strong></label>
                        <div  style="display: inline-block" class="controls">
                            <label class="checkbox">
                                <a href="/MarketStorageApp/contact.php?contact_id=<?php echo $contact_id['CONTACT_ID']?>"> <?php echo $data['SUPPLIER_NAME'];?></a>
                            </label>
                        </div>
                      </div>
					  <div class="control-group">
                        <label class="control-label"><strong>Preț:</strong></label>
                        <div  style="display: inline-block" class="controls">
                            <label class="checkbox">
                                <?php echo $data['PRODUCT_PRICE'];?>
                            </label>
                        </div>
                      </div>
					  <div class="control-group">
                        <label class="control-label"><strong>Data de depozitare:</strong></label>
                        <div style="display: inline-block" class="controls">
                            <label class="checkbox">
                                <?php echo $data['STORAGE_DATE'];?>
                            </label>
                        </div>
                      </div>
                        <div class="form-actions">
                          <a class="btn btn-info" href="index.php">Înapoi</a>
                       </div>
                     
                      
                    </div>
                </div>
                 
    </div> <!-- /container -->
  </body>
</html>