<?php

namespace quizgame\service;

// use Doctrine\ORM\Mapping as ORM;

/**
 * MdlQuizgameAnswersGame
 *
 * @ORM\Table(name="mdl_quizgame_answers_game", indexes={@ORM\Index(name="mdl_quizanswgame_pac_ix", columns={"packid"})})
 * @ORM\Entity
 */
class quizgame_answers_game
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mdl_quizgame_answers_game_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="bigint", nullable=true)
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="packid", type="bigint", nullable=false)
     */
    private $packid;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_answer", type="bigint", nullable=false)
     */
    private $currentAnswer;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="text", nullable=true)
     */
    private $key;

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
     * Set user
     *
     * @param integer $user
     *
     * @return MdlQuizgameAnswersGame
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
     * Set packid
     *
     * @param integer $packid
     *
     * @return MdlQuizgameAnswersGame
     */
    public function setPackid($packid)
    {
        $this->packid = $packid;

        return $this;
    }

    /**
     * Get packid
     *
     * @return integer
     */
    public function getPackid()
    {
        return $this->packid;
    }

    /**
     * Set currentAnswer
     *
     * @param integer $currentAnswer
     *
     * @return MdlQuizgameAnswersGame
     */
    public function setCurrentAnswer($currentAnswer)
    {
        $this->currentAnswer = $currentAnswer;

        return $this;
    }

    /**
     * Get currentAnswer
     *
     * @return integer
     */
    public function getCurrentAnswer()
    {
        return $this->currentAnswer;
    }

    /**
     * Set key
     *
     * @param string $key
     *
     * @return MdlQuizgameAnswersGame
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set timecreated
     *
     * @param integer $timecreated
     *
     * @return MdlQuizgameAnswersGame
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
     *
     * @return MdlQuizgameAnswersGame
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
     * @ORM\Column(name="current_question", type="bigint", nullable=false)
     */
    private $currentQuestion;


    /**
     * Set currentQuestion
     *
     * @param integer $currentQuestion
     *
     * @return MdlQuizgameAnswersGame
     */
    public function setCurrentQuestion($currentQuestion)
    {
        $this->currentQuestion = $currentQuestion;

        return $this;
    }

    /**
     * Get currentQuestion
     *
     * @return integer
     */
    public function getCurrentQuestion()
    {
        return $this->currentQuestion;
    }
}
