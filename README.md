## Monty Hall Simulator
Simulates the classic [Monty Hall Thought Problem](https://en.wikipedia.org/wiki/Monty_Hall_problem) using high powered shell graphics.

![Screenshot](/screen_1.png)

The Monty Hall problem can be very counter-intuitive from a quick glance.

A game show has 3 doors for a contestant to choose from.  Behind 1 is a brand new car and the other 2 have a goat.

1. The contestant chooses one of the 3 doors
2. The host opens one of the un-chosen doors revealing a goat.
3. The contestant then has to decide if they want to stay with their original guess or switch to the other remaining closed door.
4. The last two doors are then opened to reveal if the contestant has won the car.

The debate is about whether it is better to `switch` doors or `stick` with original choice.

<details> 
<summary><strong>Spoilers:</strong></summary>
   You should switch as sticking trends to 1 in 3 wins while switching trends to 2 in 3.
   
   **No, it's not 50/50.**
</details>

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

`--switch` Randomly choose the first door and switch at the last two.

`--stick` Randomly choose the first door and stick with the original choice.

`-w` Wait between rounds when automatically choosing.

`-h`, `--help` Show help.
