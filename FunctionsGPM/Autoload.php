<?php

/**
 * Arquivo de autoload - carrega automaticamente as classes
 * Inclua este arquivo no início do seu projeto
 */

// Carregar Exceptions
require_once __DIR__ . '/Exceptions/DatabaseException.php';
require_once __DIR__ . '/Exceptions/ModelException.php';
require_once __DIR__ . '/Exceptions/AuthException.php';

// Carregar Auth
require_once __DIR__ . '/Auth/FunctionsSession.php';
require_once __DIR__ . '/Auth/User.php';

// Carregar Models
require_once __DIR__ . '/Models/Ticket.php';
require_once __DIR__ . '/Models/FunctionsMSG.php';

//Carregar database
require_once __DIR__ . '/Database/Connection.php';

?>
