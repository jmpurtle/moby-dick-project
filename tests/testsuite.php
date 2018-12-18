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

function canAccessFile($filePath, string $failMessage = "File cannot be found at the referenced directory") {
	if (file_exists($filePath)) {
		return ['state' => '.', 'message' => ''];
	}

	return ['state' => 'F', 'message' => $failMessage];
}

/* >Invoke */
$testResults[] = isNotEqual(1, 0, 'We have some serious problems if 1 == 0');
$testResults[] = isEqual(1, 1, 'Also serious problems if 1 != 1');
$testResults[] = canAccessFile('../public/index.php');

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