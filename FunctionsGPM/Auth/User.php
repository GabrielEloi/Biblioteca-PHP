<?php 

// Verifica se o usuário tem uma role específica
function hasRole($requireRole){
    if(!isset($_SESSION['tipo'])){
        return false;
    };

    $userRole = $_SESSION['tipo'];

    if($userRole == $requireRole){
        return true;
    }
}

// Verifica se a role é 'admin'
function isAdmin($role){
    return $role === 'admin';
}
// Verifica se a role é 'reader'

function isReader($role){
    return $role === 'reader';
}

// Retorna dados do usuário autenticado na sessão
function user(){
    return array(
        $_SESSION['user'],
        $_SESSION['tipo'],
        $_SESSION['nome']
    );
}

?>