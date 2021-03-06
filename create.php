<?php
     
    require 'database.php';
 
    if ( !empty($_POST)) {
        // keep track validation errors
		$idError = null;
        $nameError = null;
        $categoryError = null;
        $supplierError = null;
		$priceError = null;
		$dayError = null;
		$monthError = null;
		$yearError = null;
         
        // keep track post values
		$id = $_POST['id'];
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
        if (empty($id)) {
            $idError = 'Introdu codul de bare';
            $valid = false;
        }else{
			if(!preg_match("/^[0-9]{6}$/",$id)){
				$idError = 'Codul de bare trebuie sa contina exact 6 caractere numerice';
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
		 
        // insert data
        if ($valid) {
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO PRODUCTS 
			SELECT ".$id.",'".$name."',C.CATEGORY_ID,S.SUPPLIER_ID,CONCAT(FORMAT(".$price.",2),' LEI'),'".$sDate."'
			FROM CATEGORIES C, SUPPLIERS S
			WHERE C.CATEGORY_NAME = '".$category."' AND
			S.SUPPLIER_NAME LIKE '%".$supplier."%'";
            $q = $pdo->query($sql);
            Database::disconnect();
			header("Location: index.php");
        }
    }else{
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
                        <h3>Adaugă un produs</h3>
                    </div>
             
                    <form class="form-horizontal" action="create.php" method="post">
                      <div class="control-group <?php echo !empty($idError)?'error':'';?>">
                        <div class="label_div">
							<label class="control-label">Cod de bare</label>
						</div>
                        <div class="controls">
                            <input class="form-control" name="id" type="number"  placeholder="Cod de bare" value="<?php echo !empty($id)?$id:'';?>">
                            <?php if (!empty($idError)): ?>
                                <span class="help-inline"><?php echo $idError;?></span>
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
                          <button type="submit" class="btn btn-success">Adaugă</button>
                          <a class="btn btn-info" href="index.php">Înapoi</a>
                        </div>
                    </form>
                </div>
                 
    </div> <!-- /container -->
  </body>
</html>