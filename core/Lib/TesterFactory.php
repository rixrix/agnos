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
 * Factory class facilitate in loading appropriate class tester according
 * to the file extension.
 * 
 * This class greatly enhance the possibility of supporting multiple programming
 * language support.
 *
 * @access  public
 */
class Core_Lib_TesterFactory {
    
    /**
     * Holds filename extension 
     *
     * @var     string
     * @access  private
     */
    private $fileExt;
    
    /**
     * Test file fullpath
     *
     * @var     string
     * @access  private
     */
    private $testFile;
    
    /**
     * Meta information of the $testFile
     *
     * @var     array
     * @access  private
     */
    private $ParseTestFile;
    
    /**
     * Factory constructor
     *
     * @param string $testFile
     * @param string Meta information of the testfile
     */
    function __construct($testFile, $ParseTestFile) {
        $this->fileExt = agnosUtil::getFileExtension($testFile);
        if ($this->fileExt !== false) {
            $this->testFile      = $testFile;
            $this->ParseTestFile = $ParseTestFile;
        }
    }
    
    /**
     * Create an instance of the file tester based on the recognized file type
     *
     * @param   none
     * @return  object  Instance of a class tester
     */
    public function createCoreInstance()
    {
        switch($this->fileExt) {
            case '.phpt' :
                return new agnosCorePhp($this->testFile, $this->ParseTestFile);
                break;
            default :
                return false;
                break;
        }
    }
}
?>
