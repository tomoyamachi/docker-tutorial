<?php
echo 'Tables :<br />';
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
$server = getenv('DB_HOST');
$database = getenv('MYSQL_DATABASE');
$pdo = new PDO("mysql:host=$server;dbname=$database", $user, $password);

$sql = "SHOW DATABASES";

$statement = $pdo->prepare($sql);
$statement->execute();
$databases = $statement->fetchAll(PDO::FETCH_NUM);

foreach($databases as $db){
    echo $db[0], '<br>';
}
?>