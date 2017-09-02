<?php
namespace Atasoft\MHS;

class Door
{
    public $id;
    public $isCar = false;
    public $isOpen = false;
    public $isPicked = false;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function renderLine($line)
    {
        if ($this->isPicked) {
            if ($this->isOpen) {
                $color = $this->isCar ? Art::GREEN : Art::RED;
            } else {
                $color = Art::YELLOW;
            }
        } else {
            $color = Art::WHITE;
        }

        $colorTag = "\033[".$color.'m';
        $colorTagClose = "\033[0m";

        return $colorTag.$this->doorRender()[$line].$colorTagClose;
    }

    private function doorRender()
    {
        $type = $this->isOpen ? ($this->isCar ? Art::CAR : Art::GOAT) : Art::DOOR;

        return Art::DOOR_RENDERS[$type];
    }

    public function reset()
    {
        $this->isCar = false;
        $this->isOpen = false;
        $this->isPicked = false;
    }
}