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