## Monty Hall Simulator
Simulates the classic 'Monty Hall' thought problem using high powered shell graphics.

![Screenshot](/monty_hall_screenshot.PNG?raw=true "Screenshot")

The Monty Hall proplem can be very counter-intuitive from a quick glance.
A game show has 3 doors for a contestant to choose from.  Behind 1 is a brand new car and the other 2 have a goat.
    - The contestant chooses one of the 3 doors
    - The host opens one of the un-chosen doors revealing a goat.
    - The contestant then has to decide if they want to stay with their original guess or switch to the other remaining closed door.
    - The last two doors are then opened to reveal if the contestant has won the car.

The debate is over whether it's better to `switch` doors or `stick` with the same one.

### Dependancies:
Requires php to run.  Written in PHP 7, should run in 5.6.

### Usage:
Only tested in bash shell.

```
php montyhall.php [options]
```

Or run the `montyhall.sh` script with options.

### Options:
Runs in manual input mode with not options.

`--stick` Randomly choose the first door and switch at the last two.

`--switch` Randomly choose the first door and stick with the original choice.

`-w` Wait between rounds when automatically choosing.

`-h`, `--help` Show help.
