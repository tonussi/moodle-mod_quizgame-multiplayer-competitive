<?php

namespace quizgame\service;

// use Doctrine\ORM\Mapping as ORM;

/**
 * MdlQuizgameScores
 *
 * @ORM\Table(name="mdl_quizgame_scores", indexes={@ORM\Index(name="mdl_quizscor_que_ix", columns={"question_pack"}), @ORM\Index(name="mdl_quizscor_use_ix", columns={"user"})})
 * @ORM\Entity
 */
class quizgame_scores
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mdl_quizgame_scores_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_pack", type="bigint", nullable=false)
     */
    private $questionPack;

    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="bigint", nullable=true)
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="bigint", nullable=false)
     */
    private $score = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="timecreated", type="bigint", nullable=false)
     */
    private $timecreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="timemodified", type="bigint", nullable=false)
     */
    private $timemodified = '0';


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set questionPack
     *
     * @param integer $questionPack
     *
     * @return MdlQuizgameScores
     */
    public function setQuestionPack($questionPack)
    {
        $this->questionPack = $questionPack;

        return $this;
    }

    /**
     * Get questionPack
     *
     * @return integer
     */
    public function getQuestionPack()
    {
        return $this->questionPack;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return MdlQuizgameScores
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return MdlQuizgameScores
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set timecreated
     *
     * @param integer $timecreated
     */
    public function setTimecreated()
    {
        $this->timecreated = time();

        return $this;
    }

    /**
     * Get timecreated
     *
     * @return integer
     */
    public function getTimecreated()
    {
        return $this->timecreated;
    }

    /**
     * Set timemodified
     *
     * @param integer $timemodified
     */
    public function setTimemodified()
    {
        $this->timemodified = time();

        return $this;
    }

    /**
     * Get timemodified
     *
     * @return integer
     */
    public function getTimemodified()
    {
        return $this->timemodified;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="record_match", type="bigint", nullable=false)
     */
    private $recordMatch;


    /**
     * Set recordMatch
     *
     * @param integer $recordMatch
     *
     * @return MdlQuizgameScores
     */
    public function setRecordMatch($recordMatch)
    {
        $this->recordMatch = $recordMatch;

        return $this;
    }

    /**
     * Get recordMatch
     *
     * @return integer
     */
    public function getRecordMatch()
    {
        return $this->recordMatch;
    }
}
