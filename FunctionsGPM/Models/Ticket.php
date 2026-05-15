<?php 
// Cria um novo ticket no banco de dados
function createTicketBD(string $titulo, string $descricao, string $status,  string $prioridade, int $usuario_id){
    global $dataBase;

    $titulo = $dataBase->real_escape_string($titulo);
    $descricao = $dataBase->real_escape_string($descricao);
    $status = $dataBase->real_escape_string($status);
    $prioridade = $dataBase->real_escape_string($prioridade);
    $usuario_id = (int) $usuario_id;

    $sql = "INSERT INTO tickets
    (titulo, descricao, status, prioridade, usuario_id)
    VALUES
    ('$titulo', '$descricao', '$status', '$prioridade', $usuario_id)";

    $search = $dataBase->query($sql);

    $reg = $search->fetch_object();

    return $reg;

}

// Lista todos os tickets ordenados por data decrescente
function listAllTickets(){
    global $dataBase;

    $sql = "SELECT * FROM tickets ORDER BY id DESC";

    $search = $dataBase->query($sql);

    $reg = $search->fetch_object();

    return $reg;
}

// Busca um ticket específico pelo ID
function findById($id){
    global $dataBase;
    
    $sql = "SELECT * FROM tickets WHERE id = ?";

    $stmt = $dataBase->prepare($sql);

    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_object();

}

function updateStatus($ticketId, $newStatus){
// Atualiza o status de um ticket
    global $dataBase;
    $sql = "UPDATE tickets SET status = ? WHERE id = ?";

    $stmt = $dataBase->prepare($sql);
    $stmt->bind_param("si", $newStatus, $ticketId);
    $stmt->execute();

    return $stmt;
}

// Deleta um ticket do banco de dados
function deleteStatus($ticketId){
    global $dataBase;
    $sql = "DELETE FROM tickets WHERE id = ?";
    $stmt = $dataBase->prepare($sql);
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();

    return $stmt;

}

?>
