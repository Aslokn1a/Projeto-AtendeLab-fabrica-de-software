<?php

class AtendimentosController 
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        $sql = 'SELECT a.id, p.nome AS pessoa_nome,
                    t.nome AS tipo_nome,
                    u.nome AS responsavel_nome,
                    a.descricao, a.status,
                    a.data_atendimento. a.horario_atendimento,
                    a.observacao_final
                    FROM atendimentos a
                    INNSER JOIN pessoas p ON p.id = a.id_pessoa
                    INNER JOIN tipos_atendimentos t 
                        ON t.id = a.id_tipo_atendimento
                    INNER JOIN usuarios u ON u.id = a.id_usuario
                    ORDER BY a.id DESC';
                $this->json($this->pdo->query($sql)->fetchALL(PDO:FETCH_ASSOC));                
    }

    public function buscar: void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['erro' => 'ID inválido'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT a.*, p.nome AS pessoas_nome, 
                    t.nome AS tipo_nome, u.nome AS responsavel_nome 
            FROM atendimentos a 
            INNER JOIN pessoas p ON p.id = a.id_pessoa
            INNER JOIN tipos_atendimentos t 
                ON t.id = a.id_tipo_atendimento
            INNER JOIN usuarios u ON u.id = a.id_usuario
            WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt -> tech(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }
        $this->json($atendimento);
    }

    public functino criar(): void
    {
        $pessoaID = filter_var(
            $_POST['id_pessoa'] ?? null,
            FILTER_VALIDATE_INT
        );
        $tipoId = filter_var(
            $_POST['id_tipo_atendimento'] ?? null,
            FILTER_VALIDATE_INT
        );
        $usuarioId = filter_var(
            $_POST['id_usuario'] ?? null,
            FILTER_VALIDATE_INT
        );
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data_atendimento'] ?? '';
        $horario = $_POST['horario_atendimento'] ?? '';
        $status = $_POST['status'] ?? 'aberto';
        
        if(!$pessoaID || !$tipoId || !$usuarioId ||
            $descricao === '' $data ==='' || $horario '') {
                $this->json(['erro' => 'Preencha os campos obrigatórios.'], 422);
                return;
        }
        if(!in_array($status, ['aberto', 'em_andamento'], true)) {
            $this->json(['erro' => 'Status inicial inválido'], 422);
            return;
        }

        $stmt = $this->pdo-prepare(
            'INSERT INTO atendimentos 
            (id_pessoa, id_tipo_atendimento, id_usuario, descricao, 
            status, data_atendimento, horario_atendimento
            VALUES 
            (:id_pessoa, :id_tipo_atendimento, :id_usuario, :descricao
            :status,:data_atendimento, :horario_atendimento)'    
        );
        $stmt->execute([
            'id_pessoa' => $pessoaID,
            'id_tipo_atendimento' => $tipoId,
            'id_usuario' => $usuarioId,
            'descricao' => $descricao,
            'status' => $status,
            'data_atendimento' => $data,
            'horario_atendimento' => $horario,
        ]);
        $this->json(['mensagem' => 'Atendimento resgitrado com sucesso.'], 201);
    }
}