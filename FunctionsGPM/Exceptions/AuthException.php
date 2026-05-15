<?php

/**
 * Exception para erros de autenticação
 */
class AuthenticationException extends Exception
{
    public function __construct(string $message = "Falha na autenticação", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

/**
 * Exception para erros de autorização
 */
class AuthorizationException extends Exception
{
    public function __construct(string $message = "Acesso não autorizado", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

?>
