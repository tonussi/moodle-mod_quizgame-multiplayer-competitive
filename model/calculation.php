<?php
namespace quizgame\model;

class Calculations
{

    public $total;

    function calculateTimeWithPoints($totalTime = 60, $totalPoints = 0)
    {
        return $this->total - $totalTime - $totalPoints;
    }
}
?>