<?php

namespace App\Entity;

class SearchData
{

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Country[]
     */
    public $country = [];

    /**
     * @var City[]
     */
    public $city = [];
}
