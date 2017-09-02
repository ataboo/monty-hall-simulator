<?php

namespace Atasoft\MHS;


class Door
{
    const YELLOW = '';
    const GREEN = '';
    const RED = '';
    const NONE = null;
    public $type = Art::DOOR;
    public $color = self::NONE;

    public function doorRender()
    {
        return Art::DOOR_RENDERS[$this->type];
    }

    public function renderLine($line)
    {
        $render = '';
        $tail = '';

        if (!is_null($this->color)) {
            $render.=$this->color;
            $tail = "\033[0m";
        }

        return $render.$this->doorRender()[$line].$tail;
    }

    public function update($type, $color = self::NONE)
    {
        $this->type = $type;
        $this->color = $color;
    }
}