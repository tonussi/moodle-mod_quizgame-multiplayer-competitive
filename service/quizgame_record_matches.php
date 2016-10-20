<?php

namespace quizgame\service;

// use Doctrine\ORM\Mapping as ORM;

/**
 * MdlQuizgameRecordMatches
 *
 * @ORM\Table(name="mdl_quizgame_record_matches", indexes={@ORM\Index(name="mdl_quizrecomatc_qui_ix", columns={"quizgame"})})
 * @ORM\Entity
 */
class quizgame_record_matches
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mdl_quizgame_record_matches_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="text", nullable=true)
     */
    private $key;

    /**
     * @var integer
     *
     * @ORM\Column(name="quizgame", type="bigint", nullable=false)
     */
    private $quizgame;

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
     * Set key
     *
     * @param string $key
     *
     * @return MdlQuizgameRecordMatches
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
     * Set quizgame
     *
     * @param integer $quizgame
     *
     * @return MdlQuizgameRecordMatches
     */
    public function setQuizgame($quizgame)
    {
        $this->quizgame = $quizgame;

        return $this;
    }

    /**
     * Get quizgame
     *
     * @return integer
     */
    public function getQuizgame()
    {
        return $this->quizgame;
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
     * @ORM\Column(name="date_match", type="bigint", nullable=false)
     */
    private $dateMatch;


    /**
     * Set dateMatch
     *
     * @param integer $dateMatch
     *
     * @return MdlQuizgameRecordMatches
     */
    public function setDateMatch($dateMatch)
    {
        $this->dateMatch = date();

        return $this;
    }

    /**
     * Get dateMatch
     *
     * @return integer
     */
    public function getDateMatch()
    {
        return $this->dateMatch;
    }
}
