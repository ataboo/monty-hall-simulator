<?php

namespace Atasoft\MHS;


class Door
{
    const YELLOW = '43';
    const GREEN = '42';
    const RED = '41';
    const NONE = null;
    public $id;
    public $isCar = false;
    public $isOpen = false;
    public $isPicked = false;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function doorRender()
    {
        $type = $this->isOpen ? ($this->isCar ? Art::CAR : Art::GOAT) : Art::DOOR;

        return Art::DOOR_RENDERS[$type];
    }

    public function renderLine($line)
    {
        if ($this->isPicked) {
            if ($this->isOpen) {
                $color = $this->isCar ? self::GREEN : self::RED;
            } else {
                $color = self::YELLOW;
            }
        } else {
            $color = self::NONE;
        }

        $render = '';
        $tail = '';

        if (!is_null($color)) {
            $render.="\033[".$color.'m';
            $tail = "\033[0m";
        }

        return $render.$this->doorRender()[$line].$tail;
    }

    public function reset()
    {
        $this->isCar = false;
        $this->isOpen = false;
        $this->isPicked = false;
    }
}