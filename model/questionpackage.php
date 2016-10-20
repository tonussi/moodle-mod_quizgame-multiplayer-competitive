<?php

namespace quizgame\model;

class questionpackage
{

    public $pack;

    public $timeCreated;

    public $timeModified;

    public $id;

    public $questionTime;

    public function __construct($questionTime = 60, $timeModifed, $questions = array())
    {
        $this->pack = array();
        $this->timeCreated = new \DateTime();
        $this->timeModified = $timeModifed;
        $this->questionTime = $questionTime;
        if ($questions != null) {
            foreach ($questions as $question) {
                $this->page[] = $question;
            }
        }
    }

    function addQuestion($question, $points)
    {
        $this->pack[] = $question;
        $this->pack[] = $points;
    }

    public function getPack()
    {
        return $this->pack;
    }

    public function setPack($pack)
    {
        $this->pack = $pack;
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getQuestionTime()
    {
        return $this->questionTime;
    }

    public function setQuestionTime($questionTime)
    {
        $this->questionTime = $questionTime;
        return $this;
    }
}

?>