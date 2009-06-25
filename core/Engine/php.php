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

require_once realpath('.') . DIRECTORY_SEPARATOR . 'Engine' . DIRECTORY_SEPARATOR . 'Tester.php';
require_once realpath('.') . DIRECTORY_SEPARATOR . 'Engine' . DIRECTORY_SEPARATOR . 'TesterInterface.php';

/**
 * A Subclass of Class Tester and the Baseclass for testing PHP scripts.
 *
 * This class drives the whole testing of all PHP scripts. All PHP related
 * test process must be performed here otherwise create a separate lib specific
 * for this class.
 *
 * @todo    implement REDIRECTTEST and EXPECTHEADERS
 */
class AgnosCorePhp extends AgnosTester implements AgnosTesterInterface
{
    function __construct($testFile, &$parsedTestFile)
    {
        $this->testFilePath   = $testFile;
        $this->parsedTestFile = $parsedTestFile;
        $this->tempDir        = dirname($testFile);
        $this->setTestFiles();
    }

    /**
     * Invokes test file's --FILE-- section
     *
     * @return  boolean true/false
     * @access  public
     */
    public function run()
    {
        $shortFilename      = str_replace(getcwd() . '/', '', $this->testFilePath);;
        $this->testFileName = $shortFilename;

        /*
         *  Save the temporary source code (from --FILE-- section)
         *  to a local file.
         */
        $isSaved = $this->saveTestFile($this->tempTestFiles['test_file'],
                                       $this->parsedTestFile->fileSection['FILE'],
                                       $this->tempTestFiles['temp_file']);

        if ($isSaved) {
            $commands     = $this->getCommand();
            $actualOutput = $this->executeTest($commands);

            $this->testOutput = $actualOutput;
            return true;
        }
        return false;
    }

    /**
     * Performs simple validation if the test should skipped
     *
     * @param   none
     * @return  boolean true/false
     * @access  public
     */
    public function runSkip()
    {
        /*
         *  Save the temporary source code (from --SKIPIF-- section)
         *  to a local file.
         */
        if (isset($this->parsedTestFile->fileSection['SKIPIF']) && !empty($this->parsedTestFile->fileSection['SKIPIF'])) {
            $isSaved = $this->saveTestFile($this->tempTestFiles['test_skipif'],
                                           $this->parsedTestFile->fileSection['SKIPIF'],
                                           $this->tempTestFiles['temp_skipif']);
            if ($isSaved) {
                $commands     = $this->getCommand($this->tempTestFiles['test_skipif']);
                $actualOutput = $this->executeTest($commands);

                if (preg_match('/skip/i', $actualOutput)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * (Optional) Setup test environment for this test file if available
     *
     * @access  public
     */
    public function setEnv()
    {

    }

    /**
     * Function wrapper to test::setupTestFiles
     *
     * @return  boolean     true/false
     * @access  public
     */
    public function setTestFiles()
    {
        return $this->setupTestFiles();
    }

    /**
     * Returns formatted PHP command
     *
     * @return 	string   The complete formatted cli $phpCommand
     * @access 	public
     */
    public function getCommand($testFilename = null)
    {
        $testFile   = (!is_null($testFilename)) ? $testFilename : $this->tempTestFiles['test_file'];
        $phpBinPath = $this->getBinPath();
        $phpBin     = trim(preg_replace("/\r\n/", "", $phpBinPath));
        $phpCommand = "$phpBin -f \"{$testFile}\" 2>&1";
        return $phpCommand;
    }

    /**
     * Returns path of the PHP binary extracted from the env varialbes PHP
     *
     * @return 	mixed 	PHP bin path otherwise false
     * @access 	private
     */
    private function getBinPath()
    {
        $grepBinPath  = trim(shell_exec('which grep'));
        $envBinPath   = trim(shell_exec('which env'));
        $cmdGetPhpEnv = trim(shell_exec("$envBinPath | $grepBinPath PHP"));

        if (!empty($cmdGetPhpEnv)) {
            $phpEnvArray = explode("=", $cmdGetPhpEnv);
            $phpBinPath  = $phpEnvArray[1];

            return $phpBinPath;
        }
        return false;
    }
}

?>
