<?php

namespace TrieWordsFilter;

class TrieNode{
    public $childrenValue = []; // pointer to other TrieNode
    public $value = null;  // single character
    public $isWord = false;

    public function __construct($value){
        $this->value = $value;
    }

    public function hasChild($character){
        if(isset($this->childrenValue[$character])) return $this->childrenValue[$character];
        return null;
    }

    /**
     * Check if the character could be converted into an alias
     *
     * @param $character
     * @return null
     */
    public function hasChildSpecialChars($character){
        if(isset(Alias::$alias[$character])) $character=Alias::$alias[$character];
        if(isset($this->childrenValue[$character])) return $this->childrenValue[$character];
        return null;
    }

    public function allChildren(){
        return $this->childrenValue;
    }

    public function addChild($child){
        $this->childrenValue[$child->value] = $child;
    }
}

