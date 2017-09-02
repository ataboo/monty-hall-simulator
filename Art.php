<?php
namespace Atasoft\MHS;

class Art
{
    const DOOR_RENDERS = [
        [
            '  ________',
            '  |      |',
            '  |      |',
            '  |     0|',
            '  |      |',
            '  |______|',
        ], [
            '  ________',
            '|| \___/  ',
            '||<|O O|> ',
            '|| |(@)|  ',
            '|| | _ |  ',
            '|| || ||  ',
        ], [
            '  ________',
            '||        ',
            '||  ____  ',
            '|| /____\ ',
            '|||O_HH_O|',
            '|||_|  |_|',
        ]
    ];

    const DOOR = 0;
    const GOAT = 1;
    const CAR = 2;

    private $lines;

    public function renderScore($stats)
    {
        $rounds = $stats['rounds'];
        unset($stats['rounds']);

        $expandedStats = [];
        foreach($stats as $key => $value) {
            $expandedStats[ucwords($key)] = $value;
        }

        $scoreStrings = [];
        foreach ($expandedStats as $scoreName => $scoreVal) {
            $factor = $rounds == 0 ? 0 : 100 * (float)$scoreVal / (float)$rounds;
            $scoreStrings[] = sprintf('%s: %s (%.1d%%)', $scoreName, $scoreVal, $factor);
        }

        echo "Quit by breaking with `ctrl-c`.\n";
        echo join(' | ', $scoreStrings)."\n";
    }

    public function render($doors)
    {
        echo "\n";
        $this->lines = [];
        $space = '      ';
        for ($line = 0; $line < count(self::DOOR_RENDERS[0]); $line++) {
            echo $space . $doors[0]->renderLine($line) . $space . $doors[1]->renderLine($line) . $space . $doors[2]->renderLine($line) . $space . "\n";
        }
        echo "\n";
    }
}

?>
