<?php 
// Conecta ao banco de dados MySQL com configurações UTF-8
function connectDataBase($host, $user, $password, $dataBaseName){
    $dataBase = new mysqli($host, $user, $password, $dataBaseName);

    if($dataBase->connect_errno){
        echo "Existe um erro $dataBase->errno $dataBase->connect_error";
        throw new Exception("Erro ao conectar ao banco de dados");
    }

    $dataBase->query("SET NAMES 'utf8' ");
    $dataBase->query("SET character_set_conection=utf8");
    $dataBase->query("SET character_set_client=utf8");
    $dataBase->query("SET charecter_set_result=utf8");

    return $dataBase;
}

// Executa uma query no banco de dados
function query(mysqli $dataBase, string $query){
    $search = $dataBase->query($query);
    return $search;
}







?>