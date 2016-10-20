<?php

namespace quizgame\service;

// use Doctrine\ORM\Mapping as ORM;

/**
 * MdlQuizgameGameConfiguration
 *
 * @ORM\Table(name="mdl_quizgame_game_configuration", indexes={@ORM\Index(name="mdl_quizgameconf_qui_ix", columns={"quizgame"})})
 * @ORM\Entity
 */
class quizgame_game_configuration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mdl_quizgame_game_configuration_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quizgame", type="bigint", nullable=false)
     */
    private $quizgame;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer", nullable=true)
     */
    private $time = '60';

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
     * Set quizgame
     *
     * @param integer $quizgame
     *
     * @return MdlQuizgameGameConfiguration
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
     * Set name
     *
     * @param string $name
     *
     * @return MdlQuizgameGameConfiguration
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MdlQuizgameGameConfiguration
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return MdlQuizgameGameConfiguration
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set timecreated
     *
     * @param integer $timecreated
     *
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
