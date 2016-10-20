<?php
namespace quizgame\model;

class arena
{

    public $questionPackage;

    public $players;

    public function __construct(QuestionPackage $questionPackage, $players = array())
    {
        if ($questionPackage == null) {
            throw new \Exception('Package of Questions must not be null.');
        }

        if (count($players) < 2) {
            throw new \Exception('Must be at least two players playing the game.');
        }

        $this->questionPackage = $questionPackage;
        $this->players = $players;
    }
}

?>