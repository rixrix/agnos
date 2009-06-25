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
 * This class drives all console outputs
 *
 */
class Core_Lib_Console
{
    /**
     * Displays a timestamped start of test(s)
     *
     * @param  boolean Show in console
     * @param  date    $_timeStart (optional)
     * @return date    Time started
     */
    public function timeStart($verbose = false, $_timeStart = null)
    {
        if (is_null($_timeStart) || empty($_timeStart)) {
            $_timeStart = time();
        }

        $_timeStarted = $_timeStart;

        if ($verbose) {
            echo "\nTIME START $_timeStarted ";
            echo "
--------------------------------------------------------------------------------\n";
        }
        return $_timeStarted;            
    }
    
    /**
     * Displays a timestamped end of test(s)
     *
     * @param  boolean  Show in console
     * @param  date     Time end (optional)
     * @return date     Time stoped
     */
    public function timeEnd ($verbose = false, $_timeEnd = null) {
        if (is_null($_timeEnd) || empty($_timeEnd)) {
            $_timeEnd = time();
        }

        $_timeEnded = $_timeEnd;

        if ($verbose) {
            echo "
\n--------------------------------------------------------------------------------\n";
            echo "TIME END $_timeEnded \n\n";
        }
        return $_timeEnded;
    }
        
    /**
     * Shameless plugin from the authors. Displays logo/version/authors
     * and other info about this project.
     *
     * @return boolean true
     */
    public function agnosInfo()
    {
        echo "\nCopyright (C) 2008 agnos\nVersion 0.0.1\n\n";
    }
        
    /**
     * Show test result activity in realtime in console
     * e.g. PASS, FAIL, WARN, LEAK and Others
     *
     * @param  string  $test_result e.g. PASS, FAIL, WARN, LEAK
     * @param  string  $test_file_info found in --TEST-- section
     * @param  string  $filename_test_file of the current file being tested
     * @param  string  $extra text you want to display
     * @param  string  $filename_temp_file
     * @return boolean true
     */
    public function realTime($test_result, $test_file_info, $filename_test_file, $extra = '', $filename_temp_file = null)
    {
        echo "$test_result : $test_file_info [ $filename_test_file ] $extra \n";
        return true;
    }

    /**
     * Display error message, explicitly exit the script.
     * 
     * @param  string  Error message(s)
     * @access public
     */
    public function error($str)
    {
        echo "$str \n";
        exit();
    }

    /**
     * Shows what file is currently tested
     *
     * @param  int     $test_idx/test id of the current test file
     * @param  string  $shortname/ filename of current test file
     * @return boolean true
     * 
     * @todo   add validation
     */
    public function testing($test_idx, $testCount, $shortname)
    {
        echo "TESTING $test_idx/$testCount [ $shortname ]\r";
        flush();
        
        return true;
    }

    /**
     * Display texts in console
     *
     * @param  string   $msg
     * @return boolean  true
     */
    public function message($msg, $flash = false, $line = false)
    {
        if (!$flash) {
            echo "\n$msg";
            if ($line == true) {
                echo "
--------------------------------------------------------------------------------\n";
            } else {
                echo "\n";
            }
            flush();
        } else {
            echo "$msg\r";
        }
        return true;
    }
}
?>
