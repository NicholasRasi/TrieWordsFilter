<?php
// For debugging
ini_set('memory_limit', '-1');
set_time_limit(0);
ob_implicit_flush(true);

// Include the autoloader
include(__DIR__ . '/../classes/Bootstrap.php');
$trie = new TrieWordsFilter\Trie();

// Populate the trie
$startTime = microtime(true);
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/de/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/en/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/es/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/fi/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/fr/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/it/bad_words.json'))) . " words<br>";
echo "Read #: " . $trie->addFromFile(json_decode(file_get_contents(__DIR__ . '/../data/no/bad_words.json'))) . " words<br>";

$trie->add("badword");

$endTime = microtime(true);
echo 'Number of words: '.$trie->numberOfWord.'<br>';
// Statistics
$callTime = $endTime - $startTime;
echo '<br><b>Load Structure Statistics</b><br>';
echo 'Load Time: ', sprintf('%.4f',$callTime), ' s<br>';
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k<br>';
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k<br>';


// Serialize the trie
$startTime = microtime(true);
$s = serialize($trie);
file_put_contents('trie', $s);
$endTime = microtime(true);
// Statistics
$callTime = $endTime - $startTime;
echo '<br><b>Serialize Statistics</b><br>';
echo 'Load Time: ', sprintf('%.4f',$callTime), ' s<br>';
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k<br>';
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k<br>';


// Unserialize the trie
$startTime = microtime(true);
$s = file_get_contents('trie');
$trie = unserialize($s);
$endTime = microtime(true);
// Statistics
$callTimeU = $endTime - $startTime;
echo '<br><b>Unserialize Statistics</b><br>';
echo 'Load Time: ', sprintf('%.4f',$callTimeU), ' s<br>';
echo 'Current Memory: ', sprintf('%.2f',(memory_get_usage(false) / 1024 )), ' k<br>';
echo 'Peak Memory: ', sprintf('%.2f',(memory_get_peak_usage(false) / 1024 )), ' k<br>';

// Input
$text = file_get_contents('test_file_small_600w.txt');
//$text = file_get_contents('test_file_medium_6.000w.txt');
//$text = file_get_contents('test_file_large_60.000w.txt');
//$text = file_get_contents('test_file_extralarge_600.000w.txt');
//$text = '4 @ Á á À Â à Â â Ä ä Ã ã Å å α Δ Λ λ 8 3 ß Β β Ç ç ¢ € < ( { © Þ þ Ð ð 3 € È è É é Ê ê ∑ ƒ 6 9 ! 1 ∫ Ì Í Î Ï ì í î ï κ £ η Ν Π 0 Ο ο Φ ¤ ° ø ρ Ρ ¶ þ ® 5 $ § τ υ µ ν ω ψ Ψ χ ¥ γ ÿ ý Ÿ Ý ';
//$text = 'Sentence with bad word: cazzao fai?'; // P.000-p 4Ss ass';


// Number of words
$ntok=0;
$tok = strtok($text, " ");
while ($tok !== false) {
    $tok = strtok(" ");
    $ntok++;
}

// Search without special chars
//header('Content-Type: text/html; charset=UTF-8');
$total=0;
for ($i=0; $i < 1; $i++) {
    $startTime = microtime(true);
    $found = false;
    $found = $trie->searchText($text);
    $endTime = microtime(true);
    // Statistics
    $callTimeS = $endTime - $startTime;
    echo "<br><b>Search Statistics (without special chars) $trie->numberOfWord : $ntok words</b><br>";
    echo 'Result: '; if($found) echo "Found at $found<br>"; else echo "Not Found<br>";
    echo 'Load Time: ', sprintf('%.4f', $callTimeS), ' s<br>';
    echo 'Current Memory: ', sprintf('%.2f', (memory_get_usage(false) / 1024)), ' k<br>';
    echo 'Peak Memory: ', sprintf('%.2f', (memory_get_peak_usage(false) / 1024)), ' k<br>';

    // total time
    echo 'TOTAL Time: ', sprintf('%.4f', $callTimeU + $callTimeS), ' s<br>';
    $total+=$callTimeS;
}
echo 'Average Time: ', sprintf('%.4f', $total/$i), ' s<br>';

// Search with special chars
//header('Content-Type: text/html; charset=UTF-8');
$total=0;
for ($i=0; $i < 1; $i++) {
    $startTime = microtime(true);
    $found = false;
    $found = $trie->searchTextSpecialChars($text);
    $endTime = microtime(true);
    // Statistics
    $callTimeS = $endTime - $startTime;
    echo "<br><b>Search Statistics (with special chars) $trie->numberOfWord : $ntok words</b><br>";
    echo 'Result: '; if($found) echo "Found at $found<br>"; else echo "Not Found<br>";
    echo 'Load Time: ', sprintf('%.4f', $callTimeS), ' s<br>';
    echo 'Current Memory: ', sprintf('%.2f', (memory_get_usage(false) / 1024)), ' k<br>';
    echo 'Peak Memory: ', sprintf('%.2f', (memory_get_peak_usage(false) / 1024)), ' k<br>';

    // total time
    echo 'TOTAL Time: ', sprintf('%.4f', $callTimeU + $callTimeS), ' s<br>';
    $total+=$callTimeS;
}
echo 'Average Time: ', sprintf('%.4f', $total/$i), ' s<br>';


// Search with special chars and delimiters
//header('Content-Type: text/html; charset=UTF-8');
$total=0;
for ($i=0; $i < 1; $i++) {
    $startTime = microtime(true);
    $found = false;
    $found = $trie->searchTextSpecialCharsAndDel($text);
    $endTime = microtime(true);
    // Statistics
    $callTimeS = $endTime - $startTime;
    echo "<br><b>Search Statistics (with special chars and delimiters) $trie->numberOfWord : $ntok words</b><br>";
    echo 'Result: '; if($found) echo "Found at $found<br>"; else echo "Not Found<br>";
    echo 'Load Time: ', sprintf('%.4f', $callTimeS), ' s<br>';
    echo 'Current Memory: ', sprintf('%.2f', (memory_get_usage(false) / 1024)), ' k<br>';
    echo 'Peak Memory: ', sprintf('%.2f', (memory_get_peak_usage(false) / 1024)), ' k<br>';

    // total time
    echo 'TOTAL Time: ', sprintf('%.4f', $callTimeU + $callTimeS), ' s<br>';
    $total+=$callTimeS;
}
echo 'Average Time: ', sprintf('%.4f', $total/$i), ' s<br>';

// Show
//$trie->show($trie->root);
echo "<br>";
echo "ok";