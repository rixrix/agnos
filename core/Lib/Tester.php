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
 * This class facilitates running individual/all tests. 
 */
class Core_Lib_Tester 
{
    /**
     * Holds informations of all magic that happens to all the test files
     * after it was run.
     * 
     * A container/look-up table as would others call it. 
     *
     * @var    array
     * @access public
     */
    public $FailedTestTable = array (
        'BORKED'  => array(),
        'FAILED'  => array(),
        'WARNED'  => array(),
        'LEAKED'  => array(),
        'SKIPPED' => array()	    
    );

    /**
     * Array of test file sections
     *
     * @var     array
     * @access  public
     */
    public $parsedTestFile = array();
    
    /**
     * Host system environment
     *
     * @var     array
     * @access  protected
     */
    protected $hostEnvironment = array();
    
    /**
     * Envinronment settings for the test file. See --ENV--
     * 
     * @var     array
     * @access  public
     */
    public $testEnv;
    
    /**
     * Temporary test environment for the test file
     *
     * @var     array
     * @access  public
     */
    public $testEnvTmp;
    
    /**
     * Configuration settings for the test file.
     * 
     * Settings may vary from language to language. See --INI--
     * e.g.
     *      php => php.ini
     *
     * @var     array
     * @access  public
     */
    public $testSettings;
    
    /**
     * Actual output after running the test file.
     *
     * @var     string
     * @access  public
     */
    public $testOutput;
	
    /**
     * Temporary directory
     *
     * @var        string
     * @access     public
     */
    public $tempDir = null;
	
    /**
     * Test directory
     *
     * @var        string  
     * @access     public
     */
    public $testDir = null;
	
    /**
     * Holds the complete path where this test file resides exlcuding the CWD
     * of the agnos.php
     * e.g.
     *      Sample path   :somedir/testdir
     *                        |       |---/basic/001.phpt
     *                        |-----------/agnos.php
     * 
     *      $testFilename : 'basic/001.phpt'
     * 
     * @var     string 
     * @access  public
     */	
	public $testFileName = null;
	
    /**
     * Holds the full path of the current file to be tested
     * 
     * @var         string
     * @access      public
     */	
	public $testFilePath = null;
	
    /**
     * Holds an array temporary filenames of the current test file.
     * e.g.
     *      tesfilename   : 001.phpt
     *  
     *      extensions    : '.diff', '.log', '.exp', '.out', '.mem', '.php', '.skip.php',
     *                      '.skip.php', '.clean.php', '.clean.php'
     * 
     *      $tempTestFiles : array(
     *                              'diff_filename' => full/path/of/testfile/001.phpt.diff,
     *                               ... 
     *                      )
     *
     * @var     array
     * @access  public
     */
    public $tempTestFiles = array();
    	
    function __construct (){} 
	
    /**
     * Singleton 
     *
     * @return unknown
     */
    public function &singleton()
    {
        static $instance = array();
	
        if (!$instance) {
	        $instance[0] = & new agnosTester();
	}

        return $instance[0];
    }

    /**
     * Runs all the test files
     *
     * @param   array   Test files
     * @return  boolean True/False
     * 
     * @access  public
     */	
    public function testRunAll ($Tests = null) 
    {
        if (!is_null($Tests)) {
    		if (is_array($Tests)) {
    		    
    		    /*
    		     * Set the test counter to 1 as the first test file being tested
    		     */
    		    $testId      = 1;
                $timeStarted = agnosConsole::timeStart();    		    
    		    $testCount   = sizeof($Tests);

    		    /* show something in console */
    		    agnosConsole::message("\nTotal Test(s) Found : $testCount", false, true);
    		    
    		    foreach($Tests as $testFile) {
    		        
    		        /* Run the test file */
    		        $this->testRun($testFile);
    		        
    		        agnosConsole::testing($testId, $testCount, $testFile);
    		        $testId++;
    		        
    		        /* @todo : Temporarily clean-up */
    		        @unlink($testFile . ".php");
    		        @unlink($testFile . ".skip.php");
    		    }
    		} else {
    		    $this->testRun($Tests);
    		}
    		
    		/* Time end */
    		$timeEnded = agnosConsole::timeEnd();
    		
    		/* Show test summary */
    		agnosReporter::testSummary($timeStarted, $timeEnded, $testCount, $this->FailedTestTable);
        }
		return false;
	}
	
    /**
     * Perform initial check if test file should be skip
     * 
     * @param  object  A reference instance of the class tester
     * @return boolean 
     * 
     * @access private
     */
     private function testRunSkip(&$tester)
     {
	    if ($tester->runSkip()) {
	        return true;
	    }
        return false;
     }
	   
    /**
     * Runs the specified test file
     *
     * @param  string  Test file
     * @access public
     */
    public function testRun($testFile) 
    {
        $ParseTestFile =& new agnosParser($testFile);
        $isParsed      = $ParseTestFile->parse();
        if ($isParsed == true) {
            $coreInstance           = & new agnosTesterFactory($testFile, $ParseTestFile);
            $tester                 = & $coreInstance->createCoreInstance();
            $this->testFilePath     = $testFile;
            $this->parsedTestFile   = & $ParseTestFile;
            $testDesc               = trim($this->parsedTestFile->fileSection['TEST']);
        	
        	/* Check if we need to skip the test */
        	if (!$this->testRunSkip($tester)) {
        	    switch($tester->run()) {
                     case false:
                         /* Problem running our test file ? */
                         $isPass = 'FAIL';
                         break;
                     case true :
                     default :
                         $tester->testOutput = str_replace("\r\n", "\n", trim($tester->testOutput));
                         $isMatch = $this->isMatch($tester->testOutput);
                         switch($isMatch) {
                             case true :
                                 $isPass = 'PASS';
                                 break; 
                             case false :
                                 /* Expected output doesn't match with the actual output */
                                 $isPass = 'FAIL';
                                 break;
                         }
                        break;
                }
                /*
                 * Record this failed test
                 * @todo : better implementation
                 */
                if ($isPass == 'FAIL') {
                    $this->setFailedTest('FAILED', $this->testFilePath, $tester->tempTestFiles, $this->parsedTestFile->fileSection);                        
                }
        	} else {
        	    $isPass = 'SKIP';
        	    $this->setFailedTest('SKIPPED', $this->testFilePath, $tester->tempTestFiles, $this->parsedTestFile->fileSection);
        	}
        	
        	/* Show info in console */
        	agnosConsole::realTime($isPass, $testDesc, $testFile);
        } else {
        	/*
        	 * Save the borked info properly
        	 */
        	$this->setBorkedInformation($this->parsedTestFile->borkedInformations);
        }
        return false;
    }
	
    /**
     * Compares the expected output and the actual output of the current test file
     * after it was run.
     *
     * @param  string   $_output
     * @return boolean  true/false
     */	
    private function isMatch($output)
    {
        $expectSection = $this->getTestFileSection();  
        switch ($expectSection) {
            case 'EXPECTF':
                return $this->processExpectfOutput($output);
                break;
            case 'EXPECTREGEX':
                return $this->processExpectRegexOutput($output);
                break;
            case 'EXPECTHEADERS' :
                return $this->processExpectHeadersOutput($output);
                break;                  
            case 'EXPECT' :
                return $this->processExpectOutput($output);
                break;              
            default :
                return true;
        }
        return false;	    
    }
	
    /**
     * Returns EXPECT* section of the test file.
     * e.g.
     *      EXPECT
     *      EXPECTF
     *      EXPECTREGEX
     *      EXPECTHEADERS
     *      SKIPIF
     * 
     * @return  string  The test file/EXPECT* section
     */
    private function getTestFileSection()
    {
        $getArrayKeys = array_keys($this->parsedTestFile->fileSection);
        foreach ($getArrayKeys as $expectType) {
            switch($expectType) {
                case 'EXPECT':
                    return $expectType;
                    break;  
                case 'EXPECTIF':
                    return $expectType;
                    break;
                case 'EXPECTREGEX':
                    return $expectType;
                    break;
                case 'EXPECTHEADERS':
                    return $expectType;
                    break;
            }
        }
        return false;
    }
    	
    /**
     * Executes the given test file, this time no other checking/setup is
     * involved, just run it and get the result.
     *
     * @param  string  $commands to execute the test file
     * @param  mixed   environment variable needed by the test file before firing
     *                 the $commands
     * @param  string  standard input
     * @return mixed   Returns data output after the $commands is invoked
     */
    public function executeTest($commands, $env = null, $stdin = null)
    {
        $output = agnosRunner::console($commands, $env, $stdin);
		return $output;
    }
    
    /**
     * Saves the information found in --FILE-- section into a temporary file
     * e.g. mytestcase.php
     * 
     * @param  string  filename in full path
     * @param  string  text/source codes found in --FILE-- section
     * @param  string  filename copy (optional)
     * @return boolean true; 
     */
    public function saveTestFile($filename, $text, $filenameCopy = null)
    {
        if (!empty($filename) && $filenameCopy != $filename) {
            if (@file_put_contents($filenameCopy, $text) === false) {
                agnosConsole::error("Cannot open file '$filenameCopy'");
                return false;
            }
        } else {
            if (@file_put_contents($filename, $text) === false) {
                agnosConsole::error("Cannot open file '$filename'");
                return false;
            }
        }
        # if verbose == true, show detailed info
        return true; 
    }
    
    /**
     * Something is borked while parsing the test file. Save to our 
     * FailedTestTable for later use.
     *
     * @param   array   Of borked information
     * @return  boolean true/false
     */
    public function setBorkedInformation($borkedData)
    {
    	 if (is_array($borkedData)) {
    	 	 $this->FailedTestTable['BORKED'][] = $borkedData;
    	 	 return true;
    	 }
    	return false;
    }
    
    /**
     * Updates $FailedTestTable of all failed tests. Failed could be
     * a memory leak, warning, or just the failed test.
     *
     * @param   string  $failType = FAIL, WARN, LEAK
     * @param   string  $testFile, string that contains full path of the test file
     * @param   array   $tempTestFiles, contains all the temporary filenames associated
     *                  with $testFile. 
     * @param   array   $parsedTestFileSection, an array of section of $testFile after 
     *                  it was parsed. @see parser::fileSection
     * @param   string  Some warnings, and other console messages(if possible)
     * the
     * @return  boolean true
     * @access  private         
     */
    private function setFailedTest($failType, $testFile, $tempTestFiles, $parsedTestFileSection, $runInfo = null)
    {
        $this->FailedTestTable[$failType][] = array (
            'name'        => $testFile,
            'test_script' => $tempTestFiles['test_file'],
            'skip_script' => $tempTestFiles['test_skipif'],                 
            'test_name'   => $parsedTestFileSection['TEST'],
            'output'      => $tempTestFiles['output_filename'],
            'diff'        => $tempTestFiles['diff_filename'],
            'run_info'    => $runInfo
        );
        return true;
    }
    
    /**
     * Setup temporary test files for the current test file
     *
     * @param   none
     * @return  boolean     true
     * @access  private
     */
    protected function setupTestFiles()
    {
        $baseFileName = basename($this->testFilePath);
        
        $this->tempTestFiles['diff_filename']     = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.diff';
        $this->tempTestFiles['log_filename']      = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.log';
        $this->tempTestFiles['exp_filename']      = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.exp';
        $this->tempTestFiles['output_filename']   = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.out';
        $this->tempTestFiles['memcheck_filename'] = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.mem';
        $this->tempTestFiles['temp_file']         = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.php';
        $this->tempTestFiles['test_file']         = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.php';
        $this->tempTestFiles['temp_skipif']       = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.skip.php';
        $this->tempTestFiles['test_skipif']       = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.skip.php';
        $this->tempTestFiles['temp_clean']        = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.clean.php';
        $this->tempTestFiles['test_clean']        = $this->tempDir . DIRECTORY_SEPARATOR . $baseFileName.'.clean.php';
        $this->tempTestFiles['tmp_post']          = $this->tempDir . DIRECTORY_SEPARATOR . uniqid('/phpt.');
        $this->tempTestFiles['tmp_relative_file'] = str_replace(dirname(__FILE__) . DIRECTORY_SEPARATOR, '', $this->tempTestFiles['test_file']) . 't'; 

        return true;        
    }
    
    /**
     * Performs comparison of the actual output and the expected output of 
     * the current test file specifically the --EXPECT-- section
     *
     * @param   string  $_output
     * @return  boolean true/false
     */
    protected function processExpectOutput($output)
    {
        $wanted = trim($this->parsedTestFile->fileSection['EXPECT']);
        $wanted = preg_replace('/\r\n/', "\n", $wanted);   

        if (strcmp($output, $wanted) == 0)
            return true;
        return false;
    }
        
    /**
     * Performs comparison of the actual output and the expected output of 
     * the current test file specifically the --EXPECTREGEX-- section
     *
     * @param   string  $_output
     * @return  boolean true/false
     */        
    protected function processExpectRegexOutput($output)
    {
        $wanted_regex = trim($this->parsedTestFile->fileSection['EXPECTREGEX']);
        $wanted_regex = preg_replace('/\r\n/', "\n", $wanted_regex);
        
        if (preg_match("/^$wanted_regex\$/s", $output)) {
            return true;
        }
        return false;
    }    
     
    /**
     * Performs comparison of the actual output and the expected output of 
     * the current test file specifically the --EXPECTF-- section
     *
     * @param   string  $_output
     * @return  boolean true/false
     */        
    private function processExpectfOutput($output)
    {
        $wanted_regex = trim($this->parsedTestFile->fileSection['EXPECTF']);
        $wanted_regex = preg_replace('/\r\n/', "\n", $wanted_regex);
        
        $wanted_regex = str_replace("%e", '\\' . DIRECTORY_SEPARATOR, $wanted_regex);
        $wanted_regex = str_replace("%s", ".+?", $wanted_regex); 
        $wanted_regex = str_replace("%w", "\s*", $wanted_regex);
        $wanted_regex = str_replace("%i", "[+\-]?[0-9]+", $wanted_regex);
        $wanted_regex = str_replace("%d", "[0-9]+", $wanted_regex);
        $wanted_regex = str_replace("%x", "[0-9a-fA-F]+", $wanted_regex);
        $wanted_regex = str_replace("%f", "[+\-]?\.?[0-9]+\.?[0-9]*(E-?[0-9]+)?", $wanted_regex);
        $wanted_regex = str_replace("%c", ".", $wanted_regex);
                
        if (preg_match("/^$wanted_regex\$/s", $output)) {
            return true;
        }
        return false;
    }

    /**
     * Performs comparison of the actual output and the expected output of 
     * the current test file specifically the --EXPECTHEADERS-- section
     *
     * @param   string  $_output
     * @return  boolean true/false
     * 
     * @todo    Need further info about this function.
     */    
    private function processExpectHeadersOutput($output)
    {
        $expected         = array();
        $expected_headers = array();
        $lines            = preg_split("/[\n\r]+/", $this->parsedTestFile->fileSection['EXPECTHEADERS']);
        
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                $line                     = explode(':', $line, 2);
                $expected[trim($line[0])] = trim($line[1]);
                $expected_headers[]       = trim($line[0] . ': ' . trim($line[1]));
            }
        }
    }    
}

?>
