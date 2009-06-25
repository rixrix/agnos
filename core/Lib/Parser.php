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
 * Base class for file parser
 */
class Core_Lib_Parser 
{
    /**
     * Test filename in fullpath
     * 
     * @var public 
     */
    public $testFile = null;
	
    /**
     * Holds information if the current test file is broked
     * 
     * @var public
     */
    public $borked = false;
	
    /**
     * Broked information/message
     * 
     * @var    string
     * @access public
     */
    public $borkedMessage = null;
	
    /**
     * Array of borked informations
     *
     * @var    array
     * @access public
     */
    public $borkedInformations = array();
	
    /**
     * Holds information of the different section of the given test file
     * 
     * @var    array
     * @access public
     */
    public $fileSection = array (
              'TEST'     => '',
              'SKIPIF'   => '',
              'GET'      => '',
              'COOKIE'   => '',
              'POST_RAW' => '',
              'POST'     => '',
              'UPLOAD'   => '',
              'ARGS'     => '', 	        
	   );

    /**
     * Base class constructor
     *
     * @param   string  test filename in full path
     * @return  boolean returns false if $testFile is empty
     */	       
    function __construct($testFile) 
    {
    	if (!empty($testFile)) {
    	    $this->testFile = $testFile;	
    	} else {
            return false;
    	}
    }
    
    /**
     * Parses the specified test file. 
     * 
     * On success, writes information to $fileSection class variable
     *
     * @param   string  $_fileToParse
     * @return  boolean true/false
     */
    public function parse() 
    {
    	$section   = 'TEST';
    	$secfile   = false;
    	$secdone   = false;
    	
    	/*
    	 * Open the test file
    	 */
    	$fp = fopen($this->testFile, "rt") or agnoConsole::error("Cannot open test file: $this->testFile");
    	
        if (!feof($fp)) {
    		$line = fgets($fp);
    	} else {
            $this->borked        = true;
            $this->borkedMessage = "empty test [$this->testFile]";		
    	}
    	
    	if (strncmp('--TEST--', $line, 8)) {
    		$this->borked        = true;
    		$this->borkedMessage = "Tests must start with --TEST-- [$this->testFile]"; 
    	}

    	/**
    	 * Continue parsing the test file as long as it isn't borked
    	 */
    	if (!$this->borked) {
	    	/*
	    	 * Get info from our opened test line-by-line
	    	 */
	    	while (!feof($fp)) {
	    		$line = fgets($fp);
	    		
	    		/*
	    		 * Look for possible section at the beginning of every line with 
	    		 * the following format :
	    		 *     --A-Z--
	    		 * e.g.
	    		 *     --TEST--
	    		 *     --FILE--
	    		 *     --EXPECTED--
	    		 */
	    		if (preg_match('/^--([_A-Z]+)--/', $line, $matches)) {
	    			$section = $matches[1];
	    			$this->fileSection[$section] = '';
	    			$secfile = $section == 'FILE' || $section == 'FILEOF';
	    			$secdone = false;
	    			continue;
	    		}
	    		
	    		/*
	    		 * Add the $line value into our fileSection entry
	    		 */
	    		if (!$secdone) {
	    			$this->fileSection[$section] .= $line;
	    		}
	    		
	    		/*
	    		 * Check if we reach at DONE section
	    		 */
	    		if ($secfile && preg_match('/^===DONE==$/', $line)) {
	    			$secdone = true;
	    		}
	    	} #end while-section
	    	
	    	/*
	    	 * Close our file handle
	    	 */
	    	fclose($fp);
	    	
	    	return true;
    	} else {
	       /*
	        * Probably something is borked. Save the information into our
	        * class variable for later use.
	        */
            $this->parserSetBorked();    		    		
    	}
    	
    	return false;
    }

    /**
     * Sets the borked information into $borkedInformations class variable
     * for later use.
     *
     * @return boolean  If we successfully set the borked information
     * @var public
     */
    public function parserSetBorked()
    {
    	if ($this->borked) {
            $this->borkedInformations = array (
                'name'      => $this->testFile,
                'test_name' => '',
                'output'    => '',
                'diff'      => '',
                'info'      => "" . $this->borkedMessage . " [$this->testFile]",
            );    		
    	}
    	return true;
    }
}

?>
