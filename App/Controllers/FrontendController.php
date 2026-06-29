<?php

class FrontendController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../Config/database.php';
        $this->pdo = $pdo;
    }

    private function render(string $view): void
    {
        require __DIR__ . '/../Views/' . $view;
    }

    public function pessoas(): void
    {
        $tituloPagina = 'Pessoas atendidas';
        $this->render('pessoas/index.php');
    }

    public function tipos(): void
    {
        $tituloPagina = 'Tipos de atendimento';
        $this->render('tipos-atendimentos/index.php');
    }

    public function atendimentos(): void
    {
        $tituloPagina = 'Atendimentos';
        $this->render('atendimentos/index.php');
    }

}