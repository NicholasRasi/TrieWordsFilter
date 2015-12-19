<?php

namespace TrieWordsFilter;

class Trie{
    public $root = null;
    public $numberOfWord = 0;
    public $delimiter = []; // contains all delimiters like ,.: etc

    /**
     * Generates a new trie and populate delimiter Array
     */
    public function __construct(){
        $this->root = new TrieNode('#');

        $delimiterString = '. ,:;_?"\'^|/\=+-*[]';
        for($i=0;$i<strlen($delimiterString);$i++){
            $this->delimiter[$delimiterString[$i]] = true;
        }
    }


    /**
     * Adds a new word to the trie. Trie starts with an empty root.
     * E.g.
     *              #->h->o->m->e
     * We want to insert house
     *              #->h->o->m->e
     *                        \
     *                         ->u->s->e
     * Where # is the root
     * @param $word
     */
    public function add($word){
        $child = null;
        $this->numberOfWord++;
        if($word > ''){
            $currentNode = $this->root;
            $wordLength = strlen($word);
            for($i=0; $i<$wordLength; $i++){
                $child = $currentNode->hasChild($word[$i]);
                if(!isset($child)){
                    $child = new TrieNode($word[$i]);
                    $currentNode->addChild($child);
                }
                $currentNode = $child;
            }
            $child->isWord = true;
        }
    }


    /**
     * Loads into the trie all the word from an Array
     * and return the number of read words
     *
     * @param $words
     * @return int
     */
    public function addFromFile($words){
        $wordCount = 0;
        foreach($words as $word) {
            if ($word > '') {
                $this->add($word);
                $wordCount++;
            }
        }
        return $wordCount;
    }


    /**
     * Shows the trie in a text format
     *
     * @param TrieNode $node
     * @param int $level
     * @return null
     */
    public function show(TrieNode $node, $level = 0){
        if(isset($node)){
            echo $level . ' ';
            for($i=1;$i<$level;$i++) echo '-';
            echo $node->value;
            echo $node->isWord ? 'W<br>' : '<br>';
            $children = $node->allChildren();
            foreach($children as $child){
                $this->show($child, $level+1);
            }
        }
        return null;
    }


    /**
     * Searches a single word into the trie
     *
     * @param $word
     * @return bool
     */
    public function search($word){
        if($word > ''){
            $currentNode = $this->root;
            $wordLength = strlen($word);
            for($i=0; $i<$wordLength; $i++){
                $child = $currentNode->hasChild($word[$i]);
                if(!isset($child)) return false;
                $currentNode = $child;
            }
            if($child->isWord) return true;
        }
        return false;
    }


    /**
     * Looks for a trie's entry into the input string;
     * Does not check special characters and it is case sensitive.
     * E.g. hous3 is not equal house
     *
     * @param $text
     * @return int
     */
    public function searchText($text){
        $text.=array_keys($this->delimiter)[0];
        $currentNode = $this->root;
        $currentChar = 0;
        $length = strlen($text);

        while($currentChar<$length){
            $sendChar = $text[$currentChar];

            $child = $currentNode->hasChild($sendChar);

            if(isset($child)){
                $currentChar++;
                $currentNode = $child;
            }
            else{
                if(isset($this->delimiter[$sendChar])){
                    if($currentNode->isWord) { return $currentChar; };
                }
                while(!isset($this->delimiter[$text[$currentChar++]]));
                $currentNode = $this->root;
            }
        }
        return false;
    }


    /**
     * Looks for a trie's entry into the input string;
     * Checks special characters and it is case insensitive.
     * E.g. Hous3 is equal to house
     *
     * @param $text
     * @return int
     */
    public function searchTextSpecialChars($text){
        $text.=array_keys($this->delimiter)[0];
        $currentNode = $this->root;
        $currentChar = 0;
        $length = strlen($text);

        while($currentChar<$length){
            $sendChar = $text[$currentChar];

            // manage multibyte char https://en.wikipedia.org/w/index.php?title=UTF-8&oldid=388157043
            $ord = ord($sendChar);
            if(128 & $ord){ // is multibyte? ‭1000 0000 & 0xxxxxxx‬ o 110xxxxx
                $sendChar .= $text[++$currentChar]; // at least 2 byte
                if(32 & $ord){ // 3 byte? ‭0010 0000 & ‬110xxxxx o 1110xxxx
                    $sendChar .= $text[++$currentChar]; // at least 3 byte
                }
            }

            $child = $currentNode->hasChildSpecialChars($sendChar);

            if(isset($child)){
                $currentChar++;
                $currentNode = $child;
            }
            else{
                if(isset($this->delimiter[$text[$currentChar]])){
                    if($currentNode->isWord) return $currentChar;
                }
                while(!isset($this->delimiter[$text[$currentChar++]]));
                $currentNode = $this->root;
            }
        }
        return false;
    }


    /**
     * Looks for a trie's entry into the input string;
     * Checks special characters, it is case insensitive and ignore delimiters.
     * E.g. H.o.u.s.3 is equal to house
     *
     * @param $text
     * @return int
     */
    public function searchTextSpecialCharsAndDel($text)
    {
        $text .= array_keys($this->delimiter)[0];
        $currentChar = 0;
        $length = strlen($text);

        while($currentChar<$length){
            $result = $this->searchWord($text,$currentChar,$this->root);
            if($result)
                return $result;
            else
                $currentChar++;
        }

        return false;
    }

    public function searchWord($text, $currentChar, $currentNode){
        while(isset($text[$currentChar+1]) && isset($this->delimiter[$text[$currentChar]])) //skips delimiters
            $currentChar++;

        $sendChar = $text[$currentChar];

        $ord = ord($sendChar);
        $byte=1;
        if(128 & $ord){
            $sendChar .= $text[++$currentChar];
            $byte++;
            if(32 & $ord){
                $sendChar .= $text[++$currentChar];
                $byte++;
            }
        }

        if($currentNode->isWord) return $currentChar;

        $child = $currentNode->hasChildSpecialChars($sendChar);
        if(isset($child))
            return $this->searchWord($text,$currentChar+1,$child);
        else{
            if(($byte==1 && isset($text[$currentChar-1]) && ($text[$currentChar]==$text[$currentChar-1]) && isset($text[$currentChar+1])) || ($byte>1 && isset($text[$currentChar-($byte*2-1)]) && (strcmp(substr($text,$currentChar-($byte*2-1),$byte),substr($text,$currentChar-$byte+1,$byte))==0) && isset($text[$currentChar+1])))
                return $this->searchWord($text,$currentChar+1,$currentNode);
            else
                return false;
        }
    }
}