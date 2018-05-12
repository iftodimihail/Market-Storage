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
            <div class="row">
                <h3>Market Database</h3>
            </div>
            <div class="row">
                <p>
                    <a href="create.php" class="btn btn-success">Create</a>
                </p>
                <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Cod de bare</th>
                          <th>Nume produs</th>
                          <th>Categorie</th>
                          <th>Distribuitor</th>
						  <th>Preț</th>
						  <th>Data de depozitare</th>
						  <th>Acțiuni</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                       include 'database.php';
                       $pdo = Database::connect();
                       $sql = "SELECT PRODUCT_ID, PRODUCT_NAME, CATEGORY_NAME, SUPPLIER_NAME, PRODUCT_PRICE, DATE_FORMAT(STORAGE_DATE,'%d.%m.%Y') AS STORAGE_DATE
								FROM PRODUCTS P, CATEGORIES CAT, SUPPLIERS SUP
								WHERE P.CATEGORY_ID = CAT.CATEGORY_ID AND
								P.SUPPLIER_ID = SUP.SUPPLIER_ID 
								ORDER BY P.PRODUCT_ID";
								/*
								P.PRODUCT_NAME LIKE '%".$pName."%' AND
								CAT.CATEGORY_NAME LIKE '%".$pCateg."%' AND
								SUP.SUPPLIER_NAME LIKE '%".$pSupp."%' */
                       foreach ($pdo->query($sql) as $row) {
                                echo '<tr>';
                                echo '<td>'. $row['PRODUCT_ID'] . '</td>';
                                echo '<td>'. $row['PRODUCT_NAME'] . '</td>';
                                echo '<td>'. $row['CATEGORY_NAME'] . '</td>';
								echo '<td>'. $row['SUPPLIER_NAME'] . '</td>';
								echo '<td>'. $row['PRODUCT_PRICE'] . '</td>';
								echo '<td>'. $row['STORAGE_DATE'] . '</td>';
                                echo '<td width=250>';
                                echo '<a class="btn btn-primary" href="read.php?id='.$row['PRODUCT_ID'].'">Read</a>';
                                echo ' ';
                                echo '<a class="btn btn-success" href="update.php?id='.$row['PRODUCT_ID'].'">Update</a>';
                                echo ' ';
                                echo '<a class="btn btn-danger" href="delete.php?id='.$row['PRODUCT_ID'].'">Delete</a>';
                                echo '</td>';
                                echo '</tr>';
                                echo '</tr>';
                       }
                       Database::disconnect();
                      ?>
                      </tbody>
                </table>
        </div>
    </div> <!-- /container -->
  </body>
</html>
