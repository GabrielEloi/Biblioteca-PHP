# 📚 FunctionsGPM - Biblioteca PHP para Sistema de Chamados

Uma biblioteca PHP profissional e segura para gerenciar um sistema de chamados/tickets com 2 níveis de acesso (Admin e Leitor).

---

## 🎯 Visão Geral

**FunctionsGPM** é uma biblioteca PHP desenvolvida para facilitar o desenvolvimento de um sistema web de chamados/tickets com os seguintes recursos:

- ✅ **Dois Níveis de Acesso**: Admin (envia chamados) e Leitor (recebe chamados)
- ✅ **Autenticação Segura**: Sistema de login com sessões
- ✅ **Gerenciamento de Usuários**: Criar, atualizar, deletar usuários
- ✅ **CRUD de Tickets**: Criar, listar, atualizar status, deletar
- ✅ **Tratamento de Erros**: Exceptions específicas para cada tipo de erro

---

## ⚙️ Instalação e Configuração

### 1. Incluir o Autoload

No início do seu projeto, sempre inclua o arquivo Autoload.php:

```php
<?php
require_once 'path/to/FunctionsGPM/Autoload.php';
session_start();
?>
```

### 2. Configurar Conexão com Banco de Dados

Edite `Database/functionsBD.php` com suas credenciais:

```php
<?php
$host = 'localhost';
$user = 'seu_usuario';
$password = 'sua_senha';
$database = 'seu_banco';

$dataBase = new mysqli($host, $user, $password, $database);

if($dataBase->connect_errno) {
    throw new DatabaseException("Erro ao conectar: " . $dataBase->connect_error);
}

$dataBase->query("SET NAMES 'utf8mb4'");
?>
```

### 3. Criar Schema do Banco de Dados

```sql
-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'leitor') DEFAULT 'leitor',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de tickets
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status ENUM('aberto', 'em_andamento', 'fechado', 'aguardando') DEFAULT 'aberto',
    prioridade ENUM('baixa', 'média', 'alta') DEFAULT 'média',
    usuario_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

---

## 💻 Como Usar

### Exemplo 1: Login e Autenticação

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

// Incluir função de verificação de login
require_once 'FunctionsGPM/Auth/functionsSession.php';

// Dados do formulário
$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!empty($usuario) && !empty($senha)) {
    try {
        // Verificar credenciais
        $resultado = verifyLogin($dataBase, $usuario, $senha);
        
        if ($resultado) {
            // Login bem-sucedido
            echo "Login realizado com sucesso!";
            // Redirecionar para dashboard
            header('Location: dashboard.php');
        } else {
            throw new AuthenticationException('Usuário ou senha inválidos');
        }
    } catch (AuthenticationException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
```

### Exemplo 2: Criar um Novo Ticket

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Models/ticket.php';
require_once 'FunctionsGPM/Auth/user.php';

// Verificar se é admin
if (!isAdmin($_SESSION['tipo'])) {
    throw new AuthorizationException('Apenas admins podem criar tickets');
}

try {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $status = $_POST['status'] ?? 'aberto';
    $prioridade = $_POST['prioridade'] ?? 'média';
    $usuario_id = (int)$_POST['usuario_id'] ?? 0;

    // Criar o ticket
    $resultado = createTicketBD($titulo, $descricao, $status, $prioridade, $usuario_id);
    
    if ($resultado) {
        echo "Ticket criado com sucesso!";
    }
} catch (DatabaseException $e) {
    echo "Erro ao criar ticket: " . $e->getMessage();
}
?>
```

### Exemplo 3: Listar Tickets

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Models/ticket.php';
require_once 'FunctionsGPM/Auth/user.php';

try {
    // Listar todos os tickets
    $tickets = listAllTickets();
    
    if ($tickets) {
        while ($ticket = $tickets->fetch_object()) {
            echo "ID: " . $ticket->id . " - " . $ticket->titulo . " (Status: " . $ticket->status . ")<br>";
        }
    } else {
        echo "Nenhum ticket encontrado";
    }
} catch (DatabaseException $e) {
    echo "Erro ao listar tickets: " . $e->getMessage();
}
?>
```

### Exemplo 4: Atualizar Status do Ticket

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Models/ticket.php';
require_once 'FunctionsGPM/Auth/user.php';

// Verificar se é admin
if (!isAdmin($_SESSION['tipo'])) {
    throw new AuthorizationException('Apenas admins podem atualizar tickets');
}

try {
    $ticketId = (int)$_POST['ticket_id'] ?? 0;
    $novoStatus = $_POST['novo_status'] ?? '';

    // Validar status
    $statusValidos = ['aberto', 'em_andamento', 'fechado', 'aguardando'];
    if (!in_array($novoStatus, $statusValidos)) {
        throw new ModelException('Status inválido');
    }

    // Atualizar status
    $resultado = updateStatus($ticketId, $novoStatus);
    
    if ($resultado) {
        echo "Status atualizado com sucesso!";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
```

### Exemplo 5: Buscar Ticket por ID

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Models/ticket.php';

try {
    $id = (int)$_GET['id'] ?? 0;
    
    $ticket = findById($id);
    
    if ($ticket) {
        echo "Título: " . $ticket->titulo . "<br>";
        echo "Descrição: " . $ticket->descricao . "<br>";
        echo "Status: " . $ticket->status . "<br>";
        echo "Prioridade: " . $ticket->prioridade . "<br>";
    } else {
        echo "Ticket não encontrado";
    }
} catch (DatabaseException $e) {
    echo "Erro ao buscar ticket: " . $e->getMessage();
}
?>
```

---

## 📚 Classes e Funções

### 📁 Auth/functionsSession.php

#### `checkSession()`
Verifica se existe uma sessão ativa.

```php
if (checkSession()) {
    echo "Usuário autenticado";
} else {
    echo "Faça login primeiro";
}
```

#### `hasRole($requireRole)`
Verifica se o usuário tem uma role específica.

```php
if (hasRole('admin')) {
    // Mostrar menu de admin
}
```

#### `verifyLogin($database, $user, $password)`
Verifica credenciais e cria a sessão.

```php
$user = verifyLogin($dataBase, 'joao', 'senha123');
if ($user) {
    echo "Login bem-sucedido";
}
```

---

### 👤 Auth/user.php

#### `isAdmin($role)`
Verifica se a role é 'admin'.

```php
if (isAdmin($_SESSION['tipo'])) {
    // Executar ação de admin
}
```

#### `isReader($role)`
Verifica se a role é 'leitor'.

```php
if (isReader($_SESSION['tipo'])) {
    // Mostrar tickets do leitor
}
```

#### `user()`
Retorna array com dados do usuário autenticado.

```php
$usuario = user();
// Retorna: [$_SESSION['user'], $_SESSION['tipo'], $_SESSION['nome']]
```

---

### 🎫 Models/ticket.php

#### `createTicketBD($titulo, $descricao, $status, $prioridade, $usuario_id)`
Cria um novo ticket no banco de dados.

```php
$resultado = createTicketBD(
    'Problema no sistema',
    'O sistema está lento',
    'aberto',
    'alta',
    1
);
```

**Parâmetros:**
- `$titulo` (string) - Título do ticket
- `$descricao` (string) - Descrição do problema
- `$status` (string) - Status inicial (aberto, em_andamento, fechado, aguardando)
- `$prioridade` (string) - Prioridade (baixa, média, alta)
- `$usuario_id` (int) - ID do usuário que criou

**Retorna:** Resultado da query

---

#### `listAllTickets()`
Lista todos os tickets ordenados por data de criação (mais recentes primeiro).

```php
$tickets = listAllTickets();
while ($ticket = $tickets->fetch_object()) {
    echo $ticket->titulo;
}
```

**Retorna:** MySQLi Result com todos os tickets

---

#### `findById($id)`
Busca um ticket específico por ID.

```php
$ticket = findById(5);
if ($ticket) {
    echo $ticket->titulo;
}
```

**Parâmetros:**
- `$id` (int) - ID do ticket

**Retorna:** Objeto com dados do ticket ou null

---

#### `updateStatus($ticketId, $newStatus)`
Atualiza o status de um ticket.

```php
$resultado = updateStatus(5, 'em_andamento');
```

**⚠️ IMPORTANTE:** Usar com cuidado! Validar status antes de chamar.

**Parâmetros:**
- `$ticketId` (int) - ID do ticket
- `$newStatus` (string) - Novo status

**Retorna:** Objeto Statement

---

#### `deleteStatus($ticketId)`
Deleta um ticket do banco de dados.

```php
$resultado = deleteStatus(5);
```

**Parâmetros:**
- `$ticketId` (int) - ID do ticket a deletar

**Retorna:** Objeto Statement

---

### 💬 Models/functionsMSG.php

#### `msgSuccess($msg)`
Exibe mensagem de sucesso com HTML.

```php
msgSuccess("Operação realizada com sucesso!");
```

---

#### `msgError($msg)`
Exibe mensagem de erro com HTML.

```php
msgError("Erro ao processar sua requisição");
```

---

#### `msgWarning($msg)`
Exibe mensagem de aviso com HTML.

```php
msgWarning("Atenção: esta ação é irreversível");
```

---

### 💾 Database/functionsBD.php

#### `connectDataBase($host, $user, $password, $dataBaseName)`
Cria conexão com o banco de dados.

```php
$db = connectDataBase('localhost', 'root', '', 'meu_banco');
```

**Retorna:** Objeto MySQLi ou morre se não conseguir conectar

---

#### `query(mysqli $dataBase, string $query)`
Executa uma query genérica.

```php
$resultado = query($db, "SELECT * FROM usuarios");
```

---

## 🚨 Tratamento de Erros

A biblioteca utiliza 3 tipos de exceções personalizadas:

### DatabaseException
Lançada quando há erro nas operações com banco de dados.

```php
try {
    $resultado = createTicketBD(...);
} catch (DatabaseException $e) {
    echo "Erro ao criar ticket: " . $e->getMessage();
}
```

---

### ModelException
Lançada quando há erro na lógica de negócio (validação, dados inválidos, etc).

**Filhas:**
- `NotFoundException` - Quando recurso não é encontrado
- `ValidationException` - Quando dados são inválidos

```php
try {
    if (!isValidStatus($status)) {
        throw new ModelException("Status inválido");
    }
} catch (ModelException $e) {
    echo "Erro: " . $e->getMessage();
}
```

---

### AuthException
Lançada quando há problemas de autenticação ou autorização.

**Filhas:**
- `AuthenticationException` - Quando falha no login
- `AuthorizationException` - Quando usuário não tem permissão

```php
try {
    if (!isAdmin($_SESSION['tipo'])) {
        throw new AuthorizationException("Apenas admins");
    }
} catch (AuthorizationException $e) {
    echo "Erro: " . $e->getMessage();
}
```

---

## 🔒 Boas Práticas de Segurança

### 1. ✅ Sempre Validar Entrada

```php
// ❌ ERRADO
$titulo = $_POST['titulo'];

// ✅ CORRETO
$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
if (empty($titulo)) {
    throw new ValidationException("Título é obrigatório");
}
```
---

### 2. ✅ Hash de Senhas

```php
// ❌ ERRADO - Senha em texto plano
$senha = $_POST['senha'];

// ✅ CORRETO - Hash com bcrypt
$senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
```

---

### 3. ✅ Verificar Permissões

```php
// Sempre verificar permissão antes de ação sensível
if (!isAdmin($_SESSION['tipo'])) {
    throw new AuthorizationException("Sem permissão");
}
```

---

### 4. ✅ Sanitizar Dados para Exibição

```php
// ✅ Previne XSS
$titulo = htmlspecialchars($ticket->titulo, ENT_QUOTES, 'UTF-8');
echo $titulo;
```

---

## 📖 Exemplos Práticos

### Login.php - Página de Login

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Auth/functionsSession.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    try {
        if (empty($usuario) || empty($senha)) {
            throw new AuthenticationException('Usuário e senha são obrigatórios');
        }

        $resultado = verifyLogin($GLOBALS['dataBase'], $usuario, $senha);

        if ($resultado) {
            header('Location: dashboard.php');
            exit;
        } else {
            throw new AuthenticationException('Usuário ou senha inválidos');
        }
    } catch (AuthenticationException $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - FunctionsGPM</title>
</head>
<body>
    <h1>Login</h1>
    
    <?php if ($erro): ?>
        <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
```

---

### Dashboard.php - Dashboard Admin

```php
<?php
require_once 'FunctionsGPM/Autoload.php';
session_start();

require_once 'FunctionsGPM/Auth/functionsSession.php';
require_once 'FunctionsGPM/Auth/user.php';
require_once 'FunctionsGPM/Models/ticket.php';

// Verificar se está autenticado
if (!checkSession()) {
    header('Location: login.php');
    exit;
}

$usuario = user();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($usuario[2]); ?></h1>
    <p>Role: <?php echo htmlspecialchars($usuario[1]); ?></p>

    <?php if (isAdmin($usuario[1])): ?>
        <h2>Criar Novo Ticket</h2>
        <form method="POST" action="criar_ticket.php">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="descricao" placeholder="Descrição"></textarea>
            <select name="status">
                <option value="aberto">Aberto</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="fechado">Fechado</option>
                <option value="aguardando">Aguardando</option>
            </select>
            <select name="prioridade">
                <option value="baixa">Baixa</option>
                <option value="média">Média</option>
                <option value="alta">Alta</option>
            </select>
            <input type="number" name="usuario_id" placeholder="ID do Usuário" required>
            <button type="submit">Criar</button>
        </form>
    <?php endif; ?>

    <h2>Tickets</h2>
    <?php
    try {
        $tickets = listAllTickets();
        while ($ticket = $tickets->fetch_object()) {
            echo "<div>";
            echo "<strong>" . htmlspecialchars($ticket->titulo) . "</strong><br>";
            echo "Status: " . htmlspecialchars($ticket->status) . "<br>";
            echo "Prioridade: " . htmlspecialchars($ticket->prioridade) . "<br>";
            echo "</div><hr>";
        }
    } catch (DatabaseException $e) {
        echo "Erro ao listar tickets: " . $e->getMessage();
    }
    ?>

    <a href="logout.php">Sair</a>
</body>
</html>
```


### P: Erro "Classe não encontrada"

**R:** Certifique-se de incluir o Autoload.php no início:

```php
require_once 'FunctionsGPM/Autoload.php';
```

---

### P: Erro "Conexão recusada" ao banco

**R:** Verifique:
1. MySQL está rodando?
2. Credenciais estão corretas?
3. Banco de dados existe?
4. Usuário tem permissão?

```php
// Testar conexão
$db = new mysqli('localhost', 'root', '', 'meu_banco');
if ($db->connect_error) {
    die("Erro: " . $db->connect_error);
}
echo "Conectado!";
```

---

## 📝 Notas Finais

- Esta biblioteca foi criada como referência para boas práticas
- Sempre teste em ambiente local antes de produção
- Mantenha a biblioteca atualizada
- Revise o código regularmente
- Documente mudanças e novas funcionalidades

---

**Criada com ❤️ para sua carreira como desenvolvedor PHP**
