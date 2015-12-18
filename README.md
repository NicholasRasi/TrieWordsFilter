# TrieWordsFilter
This library is written to provide a useful tools to filter unwanted word from user input. Clearly, it could be useful in web application. It does not use regular expression because is intended to be used with a large dictionary and a large text to scan.

## Usage ##
Firstly, we need to include all the classes and create the object.

    include(__DIR__ . '/../classes/Bootstrap.php');
    $trie = new StringsTrie\Trie();
    
Then we can add a single word into the dictionary with:

    $word = "badword";
    $trie->add($word);
Or we can load a dictionary with:

    $trie->addFromFile($words)

Few dictionary are included in data folder. There are de, en, es, fi, fr, it, no dictionary with bad words.

We can show the trie:

    $trie->show($trie->root)

To search if a single word is in the trie we can use:

    $trie->search($word)

To scan a text we can use these three function:

    $trie->searchText($text)
The fastest one. It looks for exact match.
E.g. we have added "badword" into the dictionary.
	
    $found_1 = $trie->searchText("This string contains a badword");
    $found_1 is true
    $found_2 = $trie->searchText("This string contains a badwordsss");
    $found_2 is false
 
 Then:

    $trie->searchTextSpecialChars($text)

   It looks for exact match but ignores special chars (like numbers or accented characters).
   E.g. we have added "badword" into the dictionary.
	
    $found = $trie->searchText("This string contains a bàdw0rd");
    $found is true
 Also:

    $trie->searchTextSpecialCharsAndDel($text)

The slowest one. It scans the text ignoring special chars, delimiters (like spaces or - or . ) and letter repetitions.
E.g. we have added "badword" into the dictionary.
	
    $found = $trie->searchText("This string contains a bàdw..0-rrd");
    $found is true
 
##How it works##
Without using regular expression it creates a trie filled with words from one or more dictionary. Every node is a letter and contains pointers to other nodes in an associative array.

##Performance##
Some test files are given to test performance and timing. For testing the worst case text files do not contains any bad words.
