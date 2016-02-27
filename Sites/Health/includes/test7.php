<?php
require_once('CRUD_config.php');

$procName = "Paginate";
$pageNumber = 1;
$rowsperpage = 20;

try {
    $dsn = $config['driver'].':server='.$config['server'].';Database='.$config['dbname'].';';
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "exec $procName :pagenumber, :rowsperpage";
    $query = $pdo->prepare($sql);
    $query->bindValue(':pagenumber', $pageNumber, PDO::PARAM_INT);
    $query->bindValue(':rowsperpage', $rowsperpage, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    var_dump($results);
}
catch(PDOException $e) {
    echo $e->getMessage();
}
