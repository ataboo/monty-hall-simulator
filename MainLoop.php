<?php
namespace Atasoft\MHS;


class MainLoop
{
    const MANUAL = 0;
    const AUTO_STICK = 1;
    const AUTO_SWITCH = 2;

    private $wins = 0;
    private $losses = 0;
    private $switches = 0;

    private $sticks = 0;
    /** @var Art */
    private $art;
    private $options;
    /** @var Door */
    private $firstPick;
    private $controlMode = self::MANUAL;
    private $forceWait = false;
    private $doors;
    private $nonFirstPickClosedDoor;

    public function run()
    {
        $this->parseOptions();

        for($i=0; $i<3; $i++) {
            $this->doors[] = new Door($i);
        }

        $this->art = new Art();

        while (true) {
            $this->startRound();
            $this->firstPick();
            $this->openFirstGoatDoor();
            $this->wrapUp($this->isSwitchingDoors());
        }
    }

    private function startRound()
    {
        system('clear');
        foreach ($this->doors as $door) {
            $door->reset();
        }
        $this->doors[rand(0, 2)]->isCar = true;

        $this->art->renderScore($this->stats());
        $this->art->render($this->doors);
    }

    private function firstPick()
    {
        $pickId = $this->selectFirstPickId();
        echo "Picked door #".($pickId + 1)."\n";

        $this->firstPick = $this->doors[$pickId];
        $this->firstPick->isPicked = true;

        $this->art->render($this->doors);
    }

    private function selectFirstPickId() {
        if ($this->autoPick()) {
            return rand(0, 2);
        }

        $pickId = null;
        while(true) {
            $pickId = (int) readline('Pick a door {1, 2, 3}: ');

            $valid = is_int($pickId) && $pickId > 0 && $pickId < 4;
            if ($valid) {
                break;
            }
            echo "\nTry a little harder there...\n";
        }

        return $pickId - 1;
    }

    private function openFirstGoatDoor()
    {
        $goatDoorCandidates = array_filter($this->doors, function(Door $door) {
            return (!$door->isPicked && !$door->isCar);
        });

        $openedDoorIndex = array_rand($goatDoorCandidates);
        $goatDoorCandidates[$openedDoorIndex]->isOpen = true;

        echo"Revealing the goat in door #".($openedDoorIndex+1)."...\n";

        foreach ($this->doors as $door) {
            if (!$door->isPicked && !$door->isOpen) {
                $this->nonFirstPickClosedDoor = $door;
                break;
            }
        }

        $this->art->render($this->doors);
    }

    private function isSwitchingDoors()
    {
        if($this->autoPick()) {
            return $this->controlMode == self::AUTO_SWITCH;
        }

        while(true) {
            $doorNumber = $this->nonFirstPickClosedDoor->id + 1;
            $isSwitching = strtolower(readline('Care to switch to door #'.$doorNumber.'? {y or n}:'));

            if ($isSwitching === 'y') {
                return true;
            }

            if ($isSwitching === 'n') {
                return false;
            }
            echo "\nTry a little harder there...\n";
        }

        throw new\Exception('This should not happen...how embarrassing.');
    }

    private function wrapUp($switching)
    {
        if($switching) {
            $this->switches++;
            $this->firstPick->isPicked = false;
            $finalPick = $this->nonFirstPickClosedDoor;
            echo "Switching pick to door #".($this->nonFirstPickClosedDoor->id + 1).".\n";
        } else {
            $this->sticks++;
            $finalPick = $this->firstPick;
            echo "Sticking with first pick: door #".($this->firstPick->id + 1).".\n";
        }

        $finalPick->isPicked = true;

        $win = $finalPick->isCar;

        foreach ($this->doors as $door) {
            $door->isOpen = true;
        }

        $this->art->render($this->doors);

        if ($win) {
            echo("Great Success!!!\n");
            $this->wins++;
        } else {
            echo("No Luck...\n");
            $this->losses++;
        }

        if ($this->waiting()) {
            readline('[Hit Enter to Continue...]');
        }
    }

    private function waiting()
    {
        return $this->controlMode == self::MANUAL || $this->forceWait;
    }

    private function autoPick()
    {
        return $this->controlMode == self::AUTO_STICK || $this->controlMode == self::AUTO_SWITCH;
    }

    private function parseOptions()
    {
        $this->options = getopt('wah', [ 'switch', 'stick', 'help' ]);

        if ($this->optionIsSet('stick')) {
            $this->controlMode = self::AUTO_STICK;
        } elseif ($this->optionIsSet('switch')) {
            $this->controlMode = self::AUTO_SWITCH;
        }

        if ($this->optionIsSet('w')) {
            $this->forceWait = true;
        }

        if ($this->optionIsSet('h') || $this->optionIsSet('help')) {
            $this->dumpHelp();
        }
    }

    private function optionIsSet($option)
    {
        return key_exists($option, $this->options);
    }

    private function stats()
    {
        return [
            'wins' => $this->wins,
            'losses' => $this->losses,
            'rounds' => $this->rounds(),
            'switches' => $this->switches,
            'sticks' => $this->sticks,
        ];
    }

    private function rounds()
    {
        return $this->wins + $this->losses;
    }

    private function dumpHelp()
    {
        echo "Simulates the classic 'Monty Hall' thought problem which can be counter-intuitive from a quick glance.\n\n";
        echo "A game show has 3 doors for a contestant to choose from.  Behind 1 is a brand new car and the other 2 have a goat.\n";
        echo"    - The contestant chooses one of the 3 doors\n";
        echo"    - The host opens one of the un-chosen doors revealing a goat.\n";
        echo"    - The contestant then has to decide if they want to stay with their original guess or switch to the other remaining closed door.\n";
        echo"    - The last two doors are then opened to reveal if the contestant has won the car.\n\n";
        echo"The debate is over whether it's better to `switch` doors or `stick` with the same one.\n\n";
        echo"Runs in manual interactive mode when no options provided.\n";
        echo"Usage:\n";
        echo"    php montyhall.php [options]\n\n";
        echo"Options:\n";
        echo"    --stick            Randomly choose the first door and switch at the last two.\n";
        echo"    --switch           Randomly choose the first door and stick with the original choice.\n";
        echo"    -w                 Wait between rounds when automatically choosing.\n";
        echo"    -h, --help         Show this description.\n";
        die();
    }
}
?>
