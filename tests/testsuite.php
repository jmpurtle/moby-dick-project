<?php
/* As this project is an one-off and does not need to be extended, PHPUnit was
 * omitted to keep final program size down. The principles found in here should
 * be mappable to PHPUnit if the project were ever to be repurposed and 
 * extended.
 *
 * To use, simply open up your command line tool and navigate to where this 
 * script is located and run "php testsuite.php". It is recommended that you 
 * use entr to automatically run these tests when files change.
 *
 * To add new tests, Add a test function in the first section then your invokes
 * in the second section. They are marked as ">Test" and ">Invoke" for finding 
 * purposes.
 */

 $testResults = [];

/* >Test */

function isEqual($a, $b, string $failMessage = "Provided inputs are not equal, expected to be equal.") {
	if ($a == $b) {
		return ['state' => '.', 'message' => '']; 
	}

	return ['state' => 'F', 'message' => $failMessage];
}

function isNotEqual($a, $b, string $failMessage = "Provided inputs are equal, expected to be equal.") {
	if ($a != $b) {
		return ['state' => '.', 'message' => '']; 
	}

	return ['state' => 'F', 'message' => $failMessage];
}

function canAccessFile($filePath, string $failMessage = "File cannot be found at the referenced directory.") {
	if (file_exists($filePath)) {
		return ['state' => '.', 'message' => ''];
	}

	return ['state' => 'F', 'message' => $failMessage];
}

function isInFile($needle, $haystack, $failMessage = "String cannot be found in file.") {
	$haystackPile = fopen($haystack, 'r');

	while (($line = fgets($haystackPile)) !== false) {
		if (strpos($line, $needle) !== false) {
			return ['state' => '.', 'message' => ''];
		}
	}

	return ['state' => 'F', 'message' => $failMessage];
}

function assembleHTML($filePath, $expectedMarkup, $failMessage = "Unable to assemble HTML as expected.") {
	$bookContents = include($filePath);

	$flatBookContents = '';
	$lastLine = null;
	$lineBuffer = array();
	foreach ($bookContents as $line) {
		if ($line == $lastLine) {
			continue;
		}

		if (strpos($line, 'CHAPTER') !== false) {
			$flatBookContents .= '<h2>' . $line . '</h2>';
			$lastLine = $line;
			continue;
		}

		if (!empty(trim($line))) {
			$lineBuffer[] = $line;
		} else if (!empty($lineBuffer)) {
			$flatBookContents .= '<p>' . implode(' ', $lineBuffer) . '</p>';
			$lineBuffer = array();
		}

		$lastLine = $line;
	}
	if (!empty($lineBuffer)) {
		$flatBookContents .= '<p>' . implode(' ', $lineBuffer) . '</p>';
	}

	if ($flatBookContents == $expectedMarkup) {
		return ['state' => '.', 'message' => ''];
	}

	return ['state' => 'F', 'message' => $failMessage];
}

function filterStopWords($stopWordsFile, $haystack, $expected, $failMessage = "Stop words filtering did not catch all as expected.") {
	$stopWordsFH = fopen($stopWordsFile, 'r');
	$stopWordsSet = array();
	while (($line = fgets($stopWordsFH)) !== false) {
		if ((strpos($line, "#") !== false) || (empty(trim($line)))) {
			continue;
		}
		$stopWordsSet[] = preg_replace( "/\r|\n/", "", $line);
	}
	fclose($stopWordsFH);

	$haystackPile = include($haystack);
	$denseHaystackPile = array_filter($haystackPile);
	$bodyText = implode(' ', $denseHaystackPile);

	$result = preg_replace('/\b('. implode('|', $stopWordsSet) . ')\b/', "", strtolower($bodyText));

	//Removing common punctuations and spaces
	$result = preg_replace('/[^ a-zA-Z-]/', '', $result);

	$bodyTextSet = array_filter(explode(' ', $result));

	// Reindexes all the values for match
	$bodyTextSet = array_values($bodyTextSet);

	if ($bodyTextSet == $expected) {
		return ['state' => '.', 'message' => ''];
	}

	return ['state' => 'F', 'message' => $failMessage];

}

/* >Config */
$serverRoot   = dirname(__DIR__);

/* >Invoke */
$testResults[] = isNotEqual(1, 0, 'We have some serious problems if 1 == 0');
$testResults[] = isEqual(1, 1, 'Also serious problems if 1 != 1');
$testResults[] = canAccessFile('../public/index.php', "This is the application entrypoint, we're unable to find it. Perhaps one hasn't been created yet?");
$testResults[] = canAccessFile($serverRoot . '/src/books/eng-moby-dick.txt', "We need to have the story of Moby Dick to load into our page.");
$testResults[] = canAccessFile($serverRoot . '/src/lang/eng-stop-words.txt', "We need a list of English stop words to process our book.");
$testResults[] = canAccessFile($serverRoot . '/app/autoload.php', "The autoloader is vital to the application, we need to have it available.");
$testResults[] = canAccessFile($serverRoot . '/app/appEnv.php', "We'll also need the application environment values to use.");
$testResults[] = isInFile('CHAPTER 1. Loomings.', $serverRoot . '/tests/mocks/books/eng-moby-dick.txt');
$testResults[] = isInFile('End of Project Gutenberg', $serverRoot . '/tests/mocks/books/eng-moby-dick.txt');
$testResults[] = assembleHTML($serverRoot . '/tests/mocks/books/text-assembly.php', "<h2>CHAPTER 1. Loomings.</h2><p>In an instant the yards swung round; and as the ship half-wheeled upon her heel, her three firm-seated graceful masts erectly poised upon</p><p>Standing between the knight-heads, Starbuck watched the Pequod’s tumultuous way, and Ahab’s also, as he went lurching along the deck.</p><p>Standing between the knight-heads, Starbuck watched the Pequod’s tumultuous way, and Ahab’s also, as he went lurching along the deck.</p><h2>CHAPTER 2. Test.</h2><p>her long, ribbed hull, seemed as the three Horatii pirouetting on one sufficient steed.</p>");
$testResults[] = filterStopWords($serverRoot . '/tests/mocks/lang/eng-stop-words.txt', $serverRoot . '/tests/mocks/books/text-assembly.php', array('chapter','loomings','instant','yards','swung','round','ship','half-wheeled','heel','firm-seated','graceful','masts','erectly','poised','standing','knight-heads','starbuck','watched','pequod','tumultuous','ahab','lurching','deck','standing','knight-heads','starbuck','watched','pequod','tumultuous','ahab','lurching','deck','chapter','test','ribbed','hull','horatii','pirouetting','sufficient','steed'));

/* >Results */
$testStates   = "";
$testMessages = "";
$testCount    = 0;
$testPass     = 0;
$testFail     = 0;

foreach ($testResults as $testResult) {
	$testCount++;
	$testStates .= $testResult['state'];
	if ($testResult['state'] == "F") {
		$testMessages .= "Test ID: " . $testCount . ", " . $testResult['message'] . "\n";
		$testFail++;
		continue;
	}
	$testPass++;
}

echo $testStates . "\n\n";
echo "Tests ran: " . $testCount . " Tests passed: " . $testPass . " Tests Failed: " . $testFail . "\n\n";
echo $testMessages . "\n\n";