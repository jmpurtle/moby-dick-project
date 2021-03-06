<?php
namespace Http\Controllers {

	class RootController {

		private $context;

		public function __construct($context = null) {
			$this->context = $context;
		}

		public function __invoke($path = array()) {

			$book = fopen($this->context['serverRoot'] . '/src/books/eng-moby-dick.txt', 'r');
			$bookOpen        = false;
			$skipChapterList = false;
			$bookClose       = false;

			$bookContents = array();
			while (($line = fgets($book)) !== false) {

				if (!$bookOpen) {
					if (strpos($line, 'CHAPTER 1. Loomings.') !== false) {
						if ($skipChapterList) {
							$bookOpen = true;
						} else {
							$skipChapterList = true;
							continue;
						}
					} else {
						continue;
					}
				}

				if (!$bookClose) {
					if (strpos($line, 'End of Project Gutenberg') !== false) {
						$bookClose = true;
						continue;
					}
				} else {
					continue;
				}

				$bookContents[] = $line;

			}

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

			$stopWordsFH = fopen($this->context['serverRoot'] . '/src/lang/eng-stop-words.txt', 'r');
			$stopWordsSet = array();
			while (($line = fgets($stopWordsFH)) !== false) {
				if ((strpos($line, "#") !== false) || (empty(trim($line)))) {
					continue;
				}
				$stopWordsSet[] = preg_replace( "/\r|\n/", "", $line);
			}
			fclose($stopWordsFH);
			$stopWordsSet[] = 'll';

			$denseBookContents = array_filter($bookContents);
			$bodyText = implode(' ', $denseBookContents);

			$result = preg_replace('/\b('. implode('|', $stopWordsSet) . ')\b/', "", strtolower($bodyText));

			//Removing common punctuations and spaces
			$result = preg_replace('/[^ a-zA-Z-]/', '', $result);

			$bodyTextSet = array_filter(explode(' ', $result));

			$wordFreq = array_count_values($bodyTextSet);
			arsort($wordFreq);

			$finalWordList = array_slice($wordFreq, 0, 100);
			$jsonWordList = array();
			$keyid = 0;
			foreach ($finalWordList as $word => $occurances) {
				$jsonWordList[] = array("word" => $word, "occurances" => $occurances, "key" => $keyid);
				$keyid++;
			}

			return [
				'HTTPStatusCode'   => '200',
				'view'             => 'home/index',
				'title'            => 'Great White Whale - Holy Grail',
				'bookContents'     => json_encode($jsonWordList),
				'flatBookContents' => $flatBookContents
			];

		}

	}

}
