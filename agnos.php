#!/usr/bin/env php
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

/*******************************************************************************
 *                    agnos Main                                           *
 ******************************************************************************/
#require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Engine' . DIRECTORY_SEPARATOR . 'Libs.php';
#require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Engine' . DIRECTORY_SEPARATOR . 'Help.php';
#require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Engine' . DIRECTORY_SEPARATOR . 'Configuration.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Bootstrap.php';
require_once LIB_PATH . DS . 'Help.php';

$getOpt      = new Console_Getopt;
$consoleArgs = $getOpt->readPHPArgv();
$consoleCmd  = $getOpt->getopt($consoleArgs, $CLI_SHORT_OPTIONS, $CLI_LONG_OPTIONS);

if ($consoleCmd != -1) {

    if (isset($consoleCmd[0][0])) {

      # initialize variables
      $isFileTest = false;
      $isDirTest  = false;
      $testByFile = array();
      $testByDir  = null;

        foreach ($consoleCmd[0] as $cmd) {
            switch($cmd[0]) {
                # Short options
                case 'h' :
                    echo "\n$help\n";
                    exit;
                    break;
                case 'f' :
                    $testByFile[] = trim($cmd[1]);
                    $isFileTest   = true;
                    break;
                case 'r' :
                    $testByDir = trim($cmd[1]);
                    $isDirTest = true;
                    break;

                # Long options
                case '--version' :
                    echo "\nversion 0.1\n";
                    break;
                default :
                    break;
            }
        }

        /*
         * See help for additional informations
         */
      $fileToTests = null;
      $dirToTests  = null;

        # -f
        if ($isFileTest) {
            if (($fileToTests = agnosUtil::setUpTestFile($testByFile)) === false) {
                exit;
            }
        }

        # -r
        if ($isDirTest) {
            if (($dirToTests = agnosUtil::findFiles($testByDir)) === false) {
                exit;
            }
        }

        /*
         *  run all tests
         */
        if (($tests = agnosUtil::mergeTests($fileToTests, $dirToTests)) !== false) {
             $do =& new agnosTester;
             $do->testRunAll($tests);
        }
    } else {
        echo "\n$help\n";
    }
} else {
    echo "\n$help\n";
    exit;
}
?>
