# moby-dick-project
A short project involving parsing the story of Moby Dick by Herman Melville

## Basic Requirements
Choosing a language of your choice, create a list of the top 100 most frequently occurring words (excluding stop words) paired with the count of occurances of the word in the provided text of Herman Melville's book Moby Dick. This should include an UI element.

## Project Details
Language: PHP  
Approach: Test Driven Development  
Approved Dependencies:  
- PHPTenjin v. 0.0.2  
- Vue.js v. 2.5.21 included via CDN
- jQuery v. 3.3.1 included via CDN

Deadline: Wednesday, December 19th, EOD (48 hours)  

## Project Policy
Commits are to be made by using a Red - Green - Commit process. We should not be committing failing tests going forward. If there are many changes to be made in one go, please create a separate branch to do so.

If you cannot clearly articulate the product risk associated with a test failing then do not include it in the test suite.

Vendor dependencies, due to the small size of this project, should be bundled explicitly so we can avoid issues related to differences in versions and security concerns.

## Usage
Clone the repository to your desktop and use a local web server with the document root pointed at /public to view the webpage.

To run tests, navigate to the tests directory in your command line tool and run php testsuite.php