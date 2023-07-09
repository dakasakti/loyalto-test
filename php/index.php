<?php

/**
 * @author Bona Brian Siagian <bonabriansiagian@gmail.com>
 */

/**
 * Class Dice
 */
class Dice
{
    /**
     * @var int $Dadu
     */
    private $Dadu;

    /**
     * @return int
     */
    public function getDadu()
    {
        return $this->Dadu;
    }

    /**
     * @return int
     */
    public function roll()
    {
        $this->Dadu =  rand(1, 6);
        return $this;
    }

    /**
     * @param int $Dadu
     * @return Dice
     */
    public function setDadu($Dadu)
    {
        $this->Dadu = $Dadu;
        return $this;
    }
}

/**
 * Class Player
 */
class Player
{

    /** 
     * @var array $koleksi
     */
    private $koleksi = [];

    /** 
     * @var string $name
     */
    private $name;

    /**
     * @var int $position
     */
    private $position;

    /**
     * @var int $point
     */
    private $point;

    /**
     * @return array
     */
    public function getkoleksi()
    {
        return $this->koleksi;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Player constructor.
     * @param int $player
     */
    public function __construct($player, $position, $name = '')
    {
        /* Set point to 0 */
        $this->point = 0;

        /* position 0 is the left most */
        $this->position = $position;

        /* Optional name, example Player A */
        $this->name = $name;

        /* Initialize array of dice */
        for ($i = 0; $i < $player; $i++) {
            array_push($this->koleksi, new Dice());
        }
    }

    /**
     * Add point
     * 
     * @var int $point
     */
    public function addPoint($point)
    {
        $this->point += $point;
    }

    /**
     * Get point
     * 
     * @return int
     */
    public function getPoint()
    {
        return $this->point;
    }

    public function play()
    {
        foreach ($this->koleksi as $dice) {
            $dice->roll();
        }
    }

    /**
     * @param int $key
     */
    public function removeDice($key)
    {
        unset($this->koleksi[$key]);
    }

    /**
     * @param Dice $dice
     */
    public function insertDice($dice)
    {
        array_push($this->koleksi, $dice);
    }
}

/**
 * Class Game
 */
class Game
{
    /**
     * @var array $players = []
     */
    private $players = [];

    /**
     * @var int $ronde
     */
    private $ronde;

    /**
     * @var int $totalPemain
     */
    private $totalPemain;

    /**
     * @var int $totalDaduPemain
     */
    private $totalDaduPemain;

    const REMOVED_DADU = 6;
    const MOVED_DADU = 1;

    /**
     * Game constructor.
     */
    public function __construct($totalPemain, $totalDaduPemain)
    {
        $this->ronde = 0;
        $this->totalPemain = $totalPemain;
        $this->totalDaduPemain = $totalDaduPemain;

        /* The game contains players and each player have dices */
        for ($i = 0; $i < $this->totalPemain; $i++) {
            $this->players[$i] = new Player($this->totalDaduPemain, $i, chr(65 + $i));
        }
    }

    /**
     * Display ronde.
     * 
     * @return $this
     */
    private function displayRonde()
    {
        echo "<strong>Giliran {$this->ronde}</strong><br/>\r\n";
        return $this;
    }

    /**
     * Show top side dice
     *
     * @param string $title
     * @return $this
     */
    private function displayTopSideDice($title = 'Lempar Dadu')
    {
        echo "<span>{$title}:</span><br/>";
        foreach ($this->players as $player) {
            echo "Pemain #{$player->getName()}: ";
            $diceTopSide = '';

            foreach ($player->getkoleksi() as $dice) {
                $diceTopSide .= $dice->getDadu() . ", ";
            }

            // Remove last comma and echo
            echo rtrim($diceTopSide, ',') . "<br/>\r\n";
        }

        echo "<br/>\r\n";
        return $this;
    }

    /**
     * @param Player $player
     * @return $this
     */
    public function displayWinner($player)
    {
        echo "<h1>Pemenang</h1>\r\n";
        echo "Pemain {$player->getName()}<br>\r\n";
        return $this;
    }

    /**
     * Start the game
     */
    public function start()
    {
        echo "Pemain = {$this->totalPemain}, Dadu = {$this->totalDaduPemain}<br/><br/>\r\n";
        // Loop until found the winner
        while (true) {
            $this->ronde++;
            $penampung = [];

            foreach ($this->players as $player) {
                $player->play();
            }

            /* Display before moved/removed */
            $this->displayRonde()->displayTopSideDice();

            /* Check player the top side */
            foreach ($this->players as $index => $player) {
                $tempDiceArray = [];

                foreach ($player->getkoleksi() as $diceIndex => $dice) {
                    /* Check for any occurrence of 6 */
                    if ($dice->getDadu() == self::REMOVED_DADU) {
                        $player->addPoint(1);
                        $player->removeDice($diceIndex);
                    }

                    /* Check for occurrence of 1 */
                    if ($dice->getDadu() == self::MOVED_DADU) {
                        /**
                         * Determine player position
                         * Max player is right most side.
                         * So move the dice to left most side.
                         */
                        if ($player->getPosition() == ($this->totalPemain - 1)) {
                            $this->players[0]->insertDice($dice);
                            $player->removeDice($diceIndex);
                        } else {
                            array_push($tempDiceArray, $dice);
                            $player->removeDice($diceIndex);
                        }
                    }
                }

                $penampung[$index + 1] = $tempDiceArray;

                if (array_key_exists($index, $penampung) && count($penampung[$index]) > 0) {
                    // Insert the dice
                    foreach ($penampung[$index] as $dice) {
                        $player->insertDice($dice);
                    }

                    // Reset
                    $penampung = [];
                }
            }

            /* Display after moved/removed */
            $this->displayTopSideDice("Setelah Evaluasi");

            /* Set number player who have dice. */
            $playerHasDice = $this->totalPemain;

            foreach ($this->players as $player) {
                if (count($player->getkoleksi()) <= 0) {
                    $playerHasDice--;
                }
            }

            /* Check if player has dice only one */
            if ($playerHasDice == 1) {
                $this->displayWinner($this->getWinner());
                /* Exit the loop */
                break;
            }
        }
    }

    /**
     * Get winner
     *
     * @return Player
     */
    private function getWinner()
    {
        $winner = null;
        $highscore = 0;
        foreach ($this->players as $player) {
            if ($player->getPoint() > $highscore) {
                $highscore = $player->getPoint();
                $winner = $player;
            }
        }

        return $winner;
    }
}

/**
 * New instance of game
 * Set number of player and number of dice per player
 */
$game = new Game(2, 2);

/* Start the game */
$game->start();
