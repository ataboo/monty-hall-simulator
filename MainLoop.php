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
    private $firstPick;
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

        for($i=0; $i<3; $i++) {
            $this->doors[] = new Door($i);
        }

        $this->art = new Art($this);

        while (true) {
            $this->startRound();
            $this->firstPick();
            $this->openFirstGoatDoor();
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
            $door->reset();
        }
        $this->doors[rand(0, 2)]->isCar = true;

        $this->art->renderScore();
        $this->art->render();
    }

    private function firstPick()
    {
        $pickId = $this->selectFirstPick();

        $this->firstPick = $this->doors[$pickId];
        $this->firstPick->isPicked = true;

        $this->art->render();
    }

    private function selectFirstPick() {
        if ($this->autoPick) {
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

        $goatDoorCandidates[array_rand($goatDoorCandidates)]->isOpen = true;

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
        $nonFirstPickClosedDoor = array_values(array_filter($this->doors, function(Door $door) {
            return !$door->isPicked && !$door->isOpen;
        }))[0];

        if ($switching) {
            $this->firstPick->isPicked = false;
            $nonFirstPickClosedDoor->isPicked = true;
            $this->switches++;
        } else {
            $this->sticks++;
        }

        $finalPick = $switching ? $nonFirstPickClosedDoor : $this->firstPick;
        $win = $finalPick->isCar;

        foreach ($this->doors as $door) {
            $door->isOpen = true;
        }

        $this->art->render();

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
        $this->options = getopt('wah', [ 'switch', 'stick', 'help' ]);

        if ($this->optionSet('stick')) {
            $this->controlMode = self::AUTO_STICK;
            $this->autoPick = true;
        } elseif ($this->optionSet('switch')) {
            $this->controlMode = self::AUTO_SWITCH;
            $this->autoPick = true;
        }

        if ($this->optionSet('w')) {
            $this->forceWait = true;
        }

        if ($this->optionSet('h') || $this->optionSet('help')) {
            $this->dumpHelp();
        }
    }

    private function optionSet($option)
    {
        return key_exists($option, $this->options);
    }

    public function stats()
    {
        return [
            'wins' => $this->wins,
            'losses' => $this->losses,
            'rounds' => $this->rounds(),
            'switches' => $this->switches,
            'sticks' => $this->sticks,
        ];
    }

    public function rounds()
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
