<?php

namespace AdminBundle\Classes;

class Sex
{
    const MALE = 1;
    const FEMALE = 2;

    public function getSexes()
    {
        return [
            self::MALE,
            self::FEMALE
        ];
    }

}