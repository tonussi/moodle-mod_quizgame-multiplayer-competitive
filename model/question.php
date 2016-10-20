<?php
namespace quizgame\model;

class question
{

    public $questionText;

    public $points;

    public $timeCreated;

    public $timeModified;

    public function __construct($questionText, $points, $timeCreated, $timeModified)
    {
        $this->questionText = $questionText;
        $this->points = $points;
        $this->timeCreated = new \DateTime();
        $this->timeModified = $timeModified;
    }

    public function getQuestionText()
    {
        return $this->questionText;
    }

    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;
        return $this;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }

    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    public function setTimeCreated($timeCreated)
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    public function getTimeModified()
    {
        return $this->timeModified;
    }

    public function setTimeModified($timeModified)
    {
        $this->timeModified = $timeModified;
        return $this;
    }
}

?>