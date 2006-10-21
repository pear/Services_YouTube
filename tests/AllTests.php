<?php

if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'AllTests::main');
    chdir(dirname(__FILE__));
}

if (!defined('PHPUnit2_INSIDE_OWN_TESTSUITE')) {
    define('PHPUnit2_INSIDE_OWN_TESTSUITE', TRUE);
}
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'YouTubeTest.php';

class AllTests
{
    public static function main()
    {

        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Services');
        /** Add testsuites, if there is. */
        $suite->addTestSuite('YouTubeTest');
        $suite->addTestSuite('Bug9917');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
?>
