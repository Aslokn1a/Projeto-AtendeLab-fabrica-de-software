<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../Config/database.php';
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
        $sql = 'SELECT id, protocolo, pessoa_id, pessoa_nome, pessoa_documento,
                    pessoa_email, tipo_atendimento_id, tipo_nome,
                    usuario_id, responsavel_nome, descricao, status,
                    data_atendimento, horario_atendimento, observacao_final,
                    criado_em, atualizado_em
                    FROM vw_atendimentos_detalhados
                    ORDER BY id DESC';
        $this->json($this->pdo->query($sql)->fetchALL(PDO::FETCH_ASSOC));
    }

    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->json(['erro' => 'ID inválido'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, protocolo, pessoa_id, pessoa_nome, pessoa_documento,
                    pessoa_email, tipo_atendimento_id, tipo_nome,
                    usuario_id, responsavel_nome, descricao, status,
                    data_atendimento, horario_atendimento, observacao_final,
                    criado_em, atualizado_em
            FROM vw_atendimentos_detalhados
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }
        $this->json($atendimento);
    }

    public function criar(): void
    {
        $usuario = usuarioAtual();
        $usuarioId = filter_var($usuario['id'] ?? null, FILTER_VALIDATE_INT);
        $pessoaID = filter_var($_POST['pessoa_id'] ?? null, FILTER_VALIDATE_INT);
        $tipoId = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
        $descricao = trim($_POST['descricao'] ?? '');
        $data = $_POST['data_atendimento'] ?? '';
        $horario = $_POST['horario_atendimento'] ?? '';
        $observacaoFinal = trim($_POST['observacao_final'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        if (
            !$pessoaID || !$tipoId || !$usuarioId ||
            $descricao === '' || $data === '' || $horario === ''
        ) {
            $this->json(['erro' => 'Preencha os campos obrigatórios.'], 422);
            return;
        }
        if (!in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'Status inicial inválido'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO atendimentos 
            (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento, observacao_final)
            VALUES 
            (:pessoa_id, :tipo_atendimento_id, :usuario_id, :descricao, :status, :data_atendimento, :horario_atendimento, :observacao_final)'
        );
        $stmt->execute([
            'pessoa_id' => $pessoaID,
            'tipo_atendimento_id' => $tipoId,
            'usuario_id' => $usuarioId,
            'descricao' => $descricao,
            'status' => $status,
            'data_atendimento' => $data,
            'horario_atendimento' => $horario,
            'observacao_final' => $observacaoFinal,
        ]);
        $this->json(['mensagem' => 'Atendimento resgitrado com sucesso.'], 201);
    }

    public function alterarStatus(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $observacao = trim($_POST['observacao_final'] ?? '');

        if (
            !$id || !in_array(
                $status,
                ['aberto', 'em_andamento', 'concluido'],
                true
            )
        ) {
            $this->json(['erro' => 'ID ou status inválido'], 422);
            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Informe a observação final para concluir'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos 
            SET status = :status, observacao_final = :observacao
            WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'observacao' => $observacao !== '' ? $observacao : null,
        ]);
        $this->json(['mensagem' => 'Status atualizado com sucesso.']);
    }
}