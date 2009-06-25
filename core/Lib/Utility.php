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
require_once 'Configuration.php';
require_once 'FileDir.php';

/**
 * Base class for function/methods used in core agnos framework
 *
 */
class Core_Lib_Utility
{
	function __construct () {
	}

	/**
	 * Searches for test files recursively in a given directory
	 *
	 * @param  string  $directory
	 * @param  string  file $extension in the form of ".extname"
	 * @return array   $test_files
	 */
	public function findFiles($directory, $extension = null)
	{
	    if (!empty($directory) && is_dir($directory)) {
	        
           	static $test_files = array();
           	$opendir           = opendir($directory) or agnosConsole::realTime("FATAL", "cannot open directory", $directory);
            $test_idx          = 0;
            
           	while (($name = readdir($opendir)) !== FALSE) {
                if (is_dir("{$directory}" . DIRECTORY_SEPARATOR . "{$name}") && !in_array($name, array('.', '..', 'CVS', '.svn'))) {
                	self::findFiles("{$directory}" . DIRECTORY_SEPARATOR . "{$name}");
                }
    
                /*
                 * Get supported test file extensions
                 */
                $config     = & new CONFIGURATION();
                $extensions = $config->extensions;

                agnosConsole::message("Searching [ $name ]", true);
                
                foreach($extensions as $ext) {
                    if (substr($name, -(strlen($ext))) == $ext) {
                        $testfile     = realpath("{$directory}" . DIRECTORY_SEPARATOR . "{$name}");
                        $test_files[] = $testfile;
                        $test_idx++;
                    }
                }
    
           	} # end-while
           	
           	closedir($opendir);
            
           	$test_files = array_unique($test_files);
			sort($test_files);

           	return $test_files;
	    }
	    
	    agnosConsole::realTime("FATAL", "Invalid directory", $directory);
		return false;
	}
   
    /**
     * Recursively removes all temporary test files starting from 
     * the current working directory where agnos.php is invoked
     * or from the directory specified.
     *
     * @param   string  Directory name (optional)
     * @access  public
     */
    public function clean($dir = null, $extension = null)
    {
        if (is_null($extension)) {
            if (!is_null($dir)) {
                if (is_array($dir)) {
                    foreach ($dir as $file) {
                        @unlink($file);
                    }
                    return true;
                } elseif (is_file($dir)) {
                    @unlink($dir);
                    return true;
                } 
            }
        } else {
            /*
             * Delete file by extension
             */
            if (is_array($extension)) {
                foreach($extension as $ext) {
                    # delete file
                }
            } else {
                # delete file
            }
        }
        
        return false;
    }
    
    /**
     * Returns a canonical absolute path of the test file(s).
     *
     * This function validates if the file really exist or is not a directory 
     * 
     * @param   mixed   An array of $path or a string $path only.
     * @return  mixed   An array of $path or a string $path only.
     *  
     * @access  public  
     */
    public function setUpTestFile($filename)
    {
        if (is_array($filename)) {
            foreach($filename as $file) {
                $fp = agnosFileDir::getFullPath($file);
                if (!agnosFileDir::isValidFile($fp)) {
                    agnosConsole::realTime("FATAL", "Invalid or file not found", $file);
                    return false;                                        
                } 
                $canonicalPath[] = $fp;
            }
            return $canonicalPath;
        } else {
            $fp = agnosFileDir::getFullPath($filename);
            if (agnosFileDir::isValidFile($fp)) {
                return $fp;  
            }
        }
        
        agnosConsole::realTime("FATAL", "Invalid file found", $filename);
        return false;
    }
    
    /**
     * Merges all the raw test files
     *
     * @param   array   $file
     * @param   array   $dir
     * 
     * @access  public
     */
    public function mergeTests($file = null, $dir = null)
    {
        if (!is_null($file) || !is_null($dir)) {
            if (is_array($file)) {
                return (array)$file + (array)$dir;
            } else {
                return array_merge((array)$file, $dir);
            }
        }
        agnosConsole::error("FATAL : No test file selected");
        return false;
    }
    
    /**
     * Returns a string \n delimited of an array value
     *
     * @param array     Array of strings
     * @param string    Array key (optional)
     * 
     * @access  public
     */
    public function arrayToString($container, $key = null) 
    {
        if (is_array($container)) {
			$string = '';
            foreach($container as $value) {
                if (!is_null($key)) {
                    $string .= $value[$key] . "\n";
                }
            }
            return trim($string);
        }
        return false;
    }
    
    /**
     * Saves information to a local file
     *
     * @param   string  File filename
     * @param   string  File contents
     * 
     * @access  public
     */
    public function saveText($filename, $contents)
    {
        if (!empty($filename) && !empty($contents)) {
            if(@file_put_contents($filename, $contents) === false) {
                agnosConsole::error("Unable to write file '$filename'");
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Returns a file extension of the given file or full path file
     *
     * @param   string  filename
     * @return  string  file extension
     */
    public function getFileExtension($filename)
    {
        if (!empty($filename)) {
            $config     =& new CONFIGURATION();
            $extensions = $config->extensions;

            foreach($extensions as $ext) {
                $ext_len  = strlen($ext);
                $file_ext = substr($filename, -($ext_len)); 
                if ( $file_ext == $ext) {
                    return $file_ext;
                }
            }
        }
        return false;
    }
}

?>
