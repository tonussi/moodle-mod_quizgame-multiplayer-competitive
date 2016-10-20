<?php

namespace quizgame\service;

// use Doctrine\ORM\Mapping as ORM;

/**
 * MdlQuizgameTeacherState
 *
 * @ORM\Table(name="mdl_quizgame_teacher_state", indexes={@ORM\Index(name="mdl_quizteacstat_use_ix", columns={"user"})})
 * @ORM\Entity
 */
class quizgame_teacher_state
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mdl_quizgame_teacher_state_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="current_question", type="bigint", nullable=false)
     */
    private $currentQuestion;

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
     * @return MdlQuizgameTeacherState
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
     * Set currentQuestion
     *
     * @param integer $currentQuestion
     *
     * @return MdlQuizgameTeacherState
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

    /**
     * Set key
     *
     * @param string $key
     *
     * @return MdlQuizgameTeacherState
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
}

