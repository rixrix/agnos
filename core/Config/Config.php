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

/*
 * System default settings
 */
$CLI_SHORT_OPTIONS = 'hf:r:!';
$CLI_LONG_OPTIONS  = array("version");

/**
 * Configuration class
 *
 */
class CONFIGURATION {

    /**
     * Show verbose screen logs
     *
     * @var     boolean
     * @access  public
     */
    public $verbose = false;

    /**
     * Recognised agnos extensions
     *
     * @var     array
     * @access  public
     */
    public $extensions = array (
                '.phpt',
           );

    /**
     * Temporary test files
     *
     * @var     array
     * @access  public
     */
    public $tempFiles = array (
                '.diff',
                '.log',
                '.exp',
                '.out',
                '.mem',
                '.skip.php',
                '.clean.php',
                '.tmp'
           );
}

class CONFIG {
	
	  public static $default = array (
	    'version' => '0.1.0',
	    'cli' => 
	       array (
	         'short' => 'hf:r:!',
	         'long' => array("version") 
	       ),
	    'logger' => array (
	         'level' => 'info',
	         'file' => array (
	         )
	       ),
	    'extensions' =>	array (
	         'atest' => array ('atest'),
	         'php' => array ('phpt'),
	       ),
	    'language' => array (
	         'php' => array ('.diff', '.log', '.exp', '.out', '.mem', 'skip.php', '.clean.php', '.tmp'),
	       ),     
	  );
}
?>