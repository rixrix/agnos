<?php
/**
 * The MIT License
 *
 * Copyright (c) 2008, 2009 Richard Sentino <richard.sentino@gmail.com>
 *  
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *  
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *  
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @filesource
 * @category        Testing
 * @package         agnos
 * @license         http://www.opensource.org/licenses/mit-license.php
 * @link            http://github.com/rixrix/agnos/tree/master
 * @since           v 0.0.1
 */

require_once 'Libs.php';

/**
 * Base class for console behavior
 * 
 * e.g.
 *  show summary report
 *  show test output in realtime
 *
 */
class Core_Lib_Reporter 
{
    /**
     * Class constructor
     *
     */
	function __construct(){}

	/**
	 * Displays overall test summary
	 *
	 * @param  timestamp   Time tests started
	 * @param  timestatmp  Time tests ended
	 * @param  integer     Total number of tests found
	 * @param  array       Array of failed tests
	 */
	public function testSummary($_timeStart, $timeEnd, $totalTestCases, $failedTest)
	{
	    $countFailed  = sizeof($failedTest['FAILED']);
	    $countSkipped = sizeof($failedTest['SKIPPED']);
	    $countPassed  = ($totalTestCases - ($countFailed + $countSkipped)); 
	    $avgTime      = ($timeEnd - $_timeStart);
        $timeStart    = @date('Y-m-d H:i:s', $_timeStart);	     
        $timeEnd      = @date('Y-m-d H:i:s', $timeEnd);
        $date         = @date('D, Y-m-d H:i:s a', $_timeStart);
        $uniqid       = @date('Y-m-d-H:i:s', $_timeStart);
        $filename     = "agnos-report-{$uniqid}.txt";
        
		$testSummary  = "
                             TEST RESULT SUMMARY
                       Copyright (C) 2008 agnos v0.0.1
                       
Filename    : $filename
Date        : $date
--------------------------------------------------------------------------------
Total Tests : $totalTestCases
Passed      : $countPassed
Failed      : $countFailed
Skipped     : $countSkipped
--------------------------------------------------------------------------------

TIMESTAMP
--------------------------------------------------------------------------------
Started     : $timeStart
Ended       : $timeEnd
Total time  : $avgTime seconds
--------------------------------------------------------------------------------\n";
        if ($countFailed >= 1) {
            
            $contents = agnosUtil::arrayToString($failedTest['FAILED'], 'name');
            $failedTestReport = "
FAILED TESTS REPORT
--------------------------------------------------------------------------------
$contents
--------------------------------------------------------------------------------\n";
            $testSummary.= $failedTestReport;
        }
        
        agnosConsole::message($testSummary);
        if (agnosFileDir::createDir('agnos-reports')) {
            agnosUtil::saveText('agnos-reports' . DIRECTORY_SEPARATOR . $filename, $testSummary);
        }
        return true;
	}
}
?>
