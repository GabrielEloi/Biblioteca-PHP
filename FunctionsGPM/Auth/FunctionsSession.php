<?php 

// Verifica se existe uma sessão de usuário ativa
function checkSession(){
        if(empty($_SESSION['user'])){
            return false;
        }else{
            return true;
        }

    }

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
    
// Valida credenciais do usuário e cria a sessão
    function verifyLogin($database, $user, $password) {
    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE user = ? LIMIT 1";

    $stmt = $database->prepare($sql);

    $stmt->bind_param("s", $user);
    $stmt->execute();


    $result = $stmt->get_result();
    $reg = $result->fetch_object();


    if ($reg && hashTest($password, $reg->senha)) {
        $_SESSION['user'] = $reg->user;
        $_SESSION['nome'] = $reg->nome;
        $_SESSION['tipo'] = $reg->tipo;
        return $reg;
    }

    return false;

    }


// Destrói a sessão do usuário
function logout(){
    unset($_SESSION['user']);
        unset($_SESSION['password']);
        unset($_SESSION['tipo']);

    }


// Criptografa a senha deslocando cada caractere
function cript($password){
    $passwordCript = '';
        for($pos = 0; $pos<strlen($password); $pos++){
            $letter = ord($password[$pos]) + 1;
            $passwordCript .= chr($letter);
        }
        return $passwordCript;
    }

    function hashGenerate($password){
// Gera hash bcrypt da senha criptografada
    $passwordCript = cript($password);
        $hash = password_hash($passwordCript, PASSWORD_DEFAULT);
        return $hash;
    }

// Verifica se a senha corresponde ao hash
function hashTest($password, $hash){
        $verify = password_verify($password, $hash);
        return $verify;
    }





?>