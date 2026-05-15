<?php

/**
 * Exception para erros em models
 */
class ModelException extends Exception
{
    public function __construct(string $message = "Erro ao processar modelo", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

/**
 * Exception quando recurso não é encontrado
 */
class NotFoundException extends ModelException
{
    public function __construct(string $message = "Recurso não encontrado", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

/**
 * Exception para validação de dados
 */
class ValidationException extends ModelException
{
    public function __construct(string $message = "Dados inválidos", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

?>
