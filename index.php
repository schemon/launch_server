<html>
<head>
  <title>PHP Test</title>
 </head>
 <body>
 <?php
  try {
    $user = 'root';
    $pass = 'FrukE2pE';
    $dbh = new PDO('mysql:host=launcherdb.cpu4kl1cdbe3.eu-west-1.rds.amazonaws.com;port=3306;dbname=launcher', $user, $pass);
    $result = $dbh->query('select * from app_list;')->fetchAll();
    //var_dump($result);
    
    foreach($result as $key => $app) {
      print json_encode( 
      array(
        'package_name' => $app['package_name'],
        'title' => $app['title'],
        'icon_url' => $app['icon_url'],
      ));


      print '<br>';
      $data = $app['package_name'] . ' ' . $app['title'] . ' <img src="' . $app['icon_url'] . '=w140">';
      print $data . '<br>';
    }
    echo '<br>Thats all there was in db';
    $dbh = null;
  } catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  echo '<p>Hello World</p>'; 

 ?> 
 </body>
</html>
