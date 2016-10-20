<?php
namespace quizgame\model;

class player
{

    private $id;

    private $name;

    private $score;

    public function __construct($nome)
    {
        $this->nome = $nome;
        $this->score = 0;
    }
}

?>