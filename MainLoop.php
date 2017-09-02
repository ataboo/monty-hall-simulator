<?php
namespace Atasoft\MHS;

class MainLoop
{
    private $wins = 0;
    private $losses = 0;
    private $switches = 0;
    private $sticks = 0;
    /** @var Art */
    private $art;
    private $carSpot;
    private $firstChoice;
    private $openedDoor;
    private $options;
    const MANUAL = 0;
    const AUTO_STICK = 1;
    const AUTO_SWITCH = 2;
    private $autoPick = false;
    private $controlMode = self::MANUAL;
    private $forceWait = false;
    private $doors;

    public function run()
    {
        $this->parseOptions();

        $this->doors = [
            new Door(),
            new Door(),
            new Door()
        ];

        $this->art = new Art($this);

        while (true) {
            $this->startRound();

            $this->openFirstGoatDoor($this->firstChoice());

            $this->wrapUp($this->secondChoice());
        }
    }

    public function doors()
    {
        return $this->doors;
    }

    private function startRound()
    {
        system('clear');
        foreach ($this->doors as $door) {
            $door->set(Art::DOOR, Door::NONE);
        }
        $this->carSpot = rand(0, 2);

        $this->art->renderScore();
        $this->art->render();
    }

    private function firstChoice()
    {
        if ($this->autoPick) {
            return rand(0, 2);
        }

        $firstChoice = null;
        while(true) {
            $firstChoice = (int) readline('Pick a door {1, 2, 3}: ');

            $valid = is_int($firstChoice) && $firstChoice > 0 && $firstChoice < 4;
            if ($valid) {
                break;
            }
            echo "\nTry a little harder there...\n";
        }

        $firstChoice-=1;

        return $firstChoice;

    }

    private function openFirstGoatDoor($firstChoice)
    {
        $this->doors[$firstChoice]->set(Art::DOOR, Door::YELLOW);

        $remainder = array_filter([ 0, 1, 2 ], function($option) use ($firstChoice) {
            return $option != $firstChoice && $option != $this->carSpot;
        });

        $this->openedDoor = array_rand($remainder);

        $this->openDoor($this->openedDoor);
    }

    private function openDoor($doorIndex)
    {
        echo "\nOpening a goat door...";
        $this->doors[$doorIndex]->set(Art::GOAT);

        $this->art->render();
    }

    private function secondChoice()
    {
        if ($this->controlMode == self::AUTO_SWITCH) {
            return true;
        }

        if ($this->controlMode == self::AUTO_STICK) {
            return false;
        }

        $secondChoice = null;
        while(true) {
            $secondChoice = strtolower(readline('Care to switch your choice? {y or n}:'));

            $valid = $secondChoice === 'y' || $secondChoice === 'n';
            if ($valid) {
                break;
            }
            echo "\nTry a little harder there...\n";
        }

        return $secondChoice === 'y';
    }

    private function wrapUp($switching)
    {
        $secondChoice = $switching ?  : $this->firstChoice;

        $guessedRightFirst = $this->firstChoice == $this->carSpot;
        $win = $guessedRightFirst != $switching;

        $this->[$this->carSpot] = Art::CAR;

        for($i=0; $i<count($this->doorVals); $i++) {
            $this->doorVals[$i] = $i == $this->carSpot ? Art::CAR : Art::GOAT;
        }

        $this->art->render();

        if ($switching) {
            $this->switches++;
        } else {
            $this->sticks++;
        }

        if ($win) {
            echo("Great Success!!!\n");
            $this->wins++;
        } else {
            echo("No Luck...\n");
            $this->losses++;
        }

        if ($this->waiting()) {
            readline('[Hit a Key to Continue...]');
        }
    }

    private function waiting()
    {
        return $this->controlMode == self::MANUAL || $this->forceWait;
    }

    private function parseOptions()
    {
        $this->options = getopt('wa', [ 'switch', 'stick' ]);

        if ($this->optionSet('stick')) {
            $this->controlMode = self::AUTO_STICK;
        } elseif ($this->optionSet('switch')) {
            $this->controlMode = self::AUTO_SWITCH;
        }

        if ($this->optionSet('w')) {
            $this->forceWait = true;
        }

        if ($this->optionSet('a')) {
            $this->autoPick = true;
        }
    }

    private function optionSet($option)
    {
        return key_exists($option, $this->options);
    }

    public function wins()
    {
        return $this->wins;
    }

    public function losses()
    {
        return $this->losses;
    }

    public function rounds()
    {
        return $this->wins + $this->losses;
    }

    public function switches()
    {
        return $this->switches;
    }

    public function sticks()
    {
        return $this->sticks;
    }
}

?>
