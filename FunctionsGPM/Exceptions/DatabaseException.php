<?php

/**
 * Exception para erros de banco de dados
 */
class DatabaseException extends Exception
{
    public function __construct(string $message = "Erro ao executar operação no banco de dados", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

?>
