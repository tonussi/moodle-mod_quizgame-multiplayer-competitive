<?php

namespace quizgame\model;

class quizgame
{

    public $arena;

    function __construct()
    {
        $questions = array();
        $players = array();

        $questions[] = 'Quantos anos tem o Brasil desda idependência de Portugal?';
        $questions[] = 'Qual é o maior pais no território brasileiro?';
        $questions[] = 'Quanta área verde nós temos no Brasil hoje?';

        $players[] = new Player('Carlos');
        $players[] = new Player('Maria');
        $players[] = new Player('José');

        $questionPackage = new QuestionPackage(120, new \DateTime(), $questions);

        $this->arena = new Arena($questionPackage, $players);
    }

    function loop()
    {
        $id = 0;
        $startTiming = 0;

        while (Game::UNFINISHED) {
            $next = showQuestionWaitForAnswer($id, $player);
        }
    }

    function showQuestion($question)
    {
        echo $question;
    }
}

?>