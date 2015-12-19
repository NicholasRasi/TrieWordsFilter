<?php

namespace TrieWordsFilter;


class TrieRoot {
    public $children = [];

    public function __construct($value){
        $this->value = $value;
    }
}