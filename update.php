<?php
    require 'database.php';
 
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    if ( null==$id ) {
        header("Location: index.php");
    }
     
    if ( !empty($_POST)) {
         // keep track validation errors
		$newidError = null;
        $nameError = null;
        $categoryError = null;
        $supplierError = null;
		$priceError = null;
		$dayError = null;
		$monthError = null;
		$yearError = null;
         
        // keep track post values
		$newid = $_POST['newid'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $supplier = $_POST['supplier'];
		$price = $_POST['price'];
		$day = $_POST['day'];
		$month = $_POST['month'];
		$year = $_POST['year'];
		if(strlen($day) == 1){
			$day = '0'.$day;
		}
		if(strlen($month) == 1){
			$month = '0'.$month;
		}
		$sDate = $year.$month.$day;
		
		//extract current date
		$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT CAST(CURRENT_DATE AS CHAR) as DATE_NOW";
        $q = $pdo->query($sql);
        $data = $q->fetch(PDO::FETCH_ASSOC);
		$currentDate = explode("-",$data['DATE_NOW']);
        $currentYear =  $currentDate[0];
		$currentMonth = $currentDate[1];
		$currentDay = $currentDate[2];
		Database::disconnect();
		
		//get all categories
		$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare("SELECT category_name from categories");
		$q->execute();
		$categories = $q->fetchAll();
		//print_r($categories);
		
		//get all suppliers
		$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare("SELECT supplier_name from suppliers");
		$q->execute();
		$suppliers = $q->fetchAll();
         
        // validate input
        $valid = true;
		
        if (empty($newid)) {
            $newidError = 'Introdu codul de bare';
            $valid = false;
        }else{
			if(!preg_match("/^[0-9]{6}$/",$newid)){
				$newidError = 'Codul de bare trebuie sa contina exact 6 caractere numerice';
				$valid = false;
			}
		}
         
        if (empty($name)) {
            $nameError = 'Intodu numele produsului';
            $valid = false;
        } 
         
        if (empty($category)) {
            $categoryError = 'Introdu categoria produsului';
            $valid = false;
        }
		
		if (empty($supplier)) {
            $supplierError = 'Introdu distribuitorul produsului';
            $valid = false;
        }
		
		if (empty($price)) {
            $priceError = 'Introdu prețul produsului';
            $valid = false;
        }else{
			if(!preg_match("/^(((\d{1,3})|(\d{1,3}\.{1}\d{1,2}))|(\.\d{1,2}))$/",$price)){
				$priceError = "Pretul introdus trebuie trebuie sa fie unul din formele: 123 sau .12 sau 123.12";
				$valid = false;
			}
		}
		
		if(empty($day)){
			$dayError = 'Introdu ziua de depozitare';
			$valid = false;
		}else{
			if(!preg_match("/^[0-9]{1,2}$/",$day)){
				$dayError = "Ziua introdusa trebuie sa aiba 1 sau 2 caractere numerice";
				$valid = false;
			}
		}
		
		if(empty($month)){
			$monthError = 'Introdu luna de depozitare';
			$valid = false;
		}
		else{
			if(!preg_match("/^[0-9]{1,2}$/",$month)){
				$monthError = "Luna introdusa trebuie sa aiba 1 sau 2 caractere numerice";
				$valid = false;
			}
		}
         
		if(empty($year)){
			$yearError = 'Introdu anul de depozitare';
			$valid = false;
		}else{
			if(!preg_match("/^[0-9]{4}$/",$year)){
				$yearError = "Anul introdusa trebuie sa aiba exact 4 caractere numerice";
				$valid = false;
			}
		}
		
		if(!empty($year)){
			if($year <= $currentYear){
				if(!empty($month)){
						if($year == $currentYear){
						if($month <= $currentMonth){
							if(!empty($day)){
								if($day > $currentDay && $month == $currentMonth){
									$dayError = "Ziua introdusa nu poate fi mai mare decat cea curenta: ".$currentDay;
									$valid = false;
								}
							}else{
								$dayError = "Introdu ziua";
								$valid = false;
							}
						}else{
							$monthError = "Luna introdusa nu poate fi mai mare decat cea curenta: ".$currentMonth;
							$valid = false;
						}
					}
				}else{
					$monthError = "Introdu luna";
					$valid = false;
				}	
				if($year < $currentYear - 5){
					$yearError = "Anul introdus nu poate fi mai mic cu 5 ani decat cel curent: ".$currentYear;
					$valid= false;
				}
			}else{
				$yearError = "Anul introdus nu poate fi mai mare decat cel curent: ".$currentYear;
				$valid = false;
			}
		}else{
			$yearError = "Introdu anul";
			$valid= false;
		}

        // update data
        if ($valid) {
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE products 
					SET product_id = ?,
					product_name = ?,
					category_id = (select category_id from categories where category_name = ?),
					supplier_id = (select supplier_id from suppliers where supplier_name = ?),
					product_price = CONCAT(FORMAT(?,2),' LEI'),
					storage_date = ?
					where product_id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($newid,$name,$category,$supplier,$price,$sDate,$id));
            Database::disconnect();
            header("Location: index.php");
        }
    } else {
		//get all categories
		$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare("SELECT category_name from categories");
		$q->execute();
		$categories = $q->fetchAll();
		//print_r($categories);
		
		//get all suppliers
		$pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare("SELECT supplier_name from suppliers");
		$q->execute();
		$suppliers = $q->fetchAll();
		
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT PRODUCT_ID, PRODUCT_NAME, CATEGORY_NAME, SUPPLIER_NAME, SUBSTRING(PRODUCT_PRICE,1,LENGTH(PRODUCT_PRICE)-4) AS PRICE, DATE_FORMAT(STORAGE_DATE,'%d.%m.%Y') AS STORAGE_DATE
				FROM PRODUCTS P, CATEGORIES CAT, SUPPLIERS SUP
				WHERE P.CATEGORY_ID = CAT.CATEGORY_ID AND
				P.SUPPLIER_ID = SUP.SUPPLIER_ID 
				AND PRODUCT_ID = ?";
				
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
		$newid = $data['PRODUCT_ID'];
        $name = $data['PRODUCT_NAME'];
        $category = $data['CATEGORY_NAME'];
        $supplier = $data['SUPPLIER_NAME'];
		$price = $data['PRICE'];
		$date = explode(".",$data['STORAGE_DATE']);
		$day = $date[0];
		$month = $date[1];
		$year = $date[2];
        Database::disconnect();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="./css/create.css";
</head>
<body>
    <div class="container">
     
                <div class="span10 offset1">
                    <div class="row">
                        <h3>Modifică</h3>
                    </div>
             
                   <form class="form-horizontal" action="update.php?id=<?php echo $id?>" method="post">
                      <div class="control-group <?php echo !empty($newidError)?'error':'';?>">
                        <div class="label_div">
							<label class="control-label">Cod de bare</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="newid" type="number"  placeholder="Cod de bare" value="<?php echo !empty($newid)?$newid:'';?>">
                            <?php if (!empty($newidError)): ?>
                                <span class="help-inline"><?php echo $newidError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="control-group <?php echo !empty($nameError)?'error':'';?>">
                       <div class="label_div">
							<label class="control-label">Nume</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="name" type="text"  placeholder="Name" value="<?php echo !empty($name)?$name:'';?>">
                            <?php if (!empty($nameError)): ?>
                                <span class="help-inline"><?php echo $nameError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="control-group <?php echo !empty($categoryError)?'error':'';?>">
                        <div class="label_div"> 
							<label class="control-label">Categorie</label>
						</div>
                        <div class="controls">
                            <select name="category" class="selectpicker" data-live-search="true">
							  <?php 
								foreach($categories as $cat)
									echo "<option>".$cat['category_name']."</option>"; ?>
							</select>

                            <?php if (!empty($categoryError)): ?>
                                <span class="help-inline"><?php echo $categoryError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
					  <div class="control-group <?php echo !empty($supplierError)?'error':'';?>">
                       <div class="label_div">
							<label class="control-label">Distribuitor</label>
						</div>
                        <div class="controls">
                            <select name="supplier" class="selectpicker" data-live-search="true">
							  <?php 
								foreach($suppliers as $sup)
									echo "<option id='supplier'>".$sup['supplier_name']."</option>"; ?>
							</select>
                            <?php if (!empty($supplierError)): ?>
                                <span class="help-inline"><?php echo $supplierError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
					  <div class="control-group <?php echo !empty($priceError)?'error':'';?>">
                        <div class="label_div">
							<label class="control-label">Preț</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="price" type="text"  placeholder="Preț" value="<?php echo !empty($price)?$price:'';?>">
                            <?php if (!empty($priceError)): ?>
                                <span class="help-inline"><?php echo $priceError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
					  <div class="control-group <?php echo !empty($dayError)?'error':'';?>">
                        <div class="label_div">
							<label class="control-label">Ziua</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="day" type="number"  placeholder="Ziua" value="<?php echo !empty($day)?$day:'';?>">
                            <?php if (!empty($dayError)): ?>
                                <span class="help-inline"><?php echo $dayError;?></span>
                            <?php endif; ?>
                        </div class="label_div">
                      </div>
					  <div class="control-group <?php echo !empty($monthError)?'error':'';?>">
						<div class="label_div">
							<label class="control-label">Luna</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="month" type="number"  placeholder="Luna" value="<?php echo !empty($month)?$month:'';?>">
                            <?php if (!empty($monthError)): ?>
                                <span class="help-inline"><?php echo $monthError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
					  <div class="control-group <?php echo !empty($yearError)?'error':'';?>">
                        <div class="label_div">
							<label class="control-label">Anul</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="year" type="number"  placeholder="Anul" value="<?php echo !empty($year)?$year:'';?>">
                            <?php if (!empty($yearError)): ?>
                                <span class="help-inline"><?php echo $yearError;?></span>
                            <?php endif; ?>
                        </div>
                      </div>
                      <div class="form-actions">
                          <button type="submit" class="btn btn-success">Modifică</button>
                          <a class="btn btn-info" href="index.php">Înapoi</a>
                        </div>
                    </form>
                </div>
                 
    </div> <!-- /container -->
  </body>
</html>