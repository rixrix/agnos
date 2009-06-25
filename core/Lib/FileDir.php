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

/**
 * File and Folder/Directory class
 */
class Core_Lib_FileDir {
    
    /**
     * Check if file is a valid file
     *
     * @param   string  $filename
     * @return  boolean true/false
     * @access  public
     */
    public function isValidFile($filename)
    {
        if (is_file($filename) || file_exists($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Checks folder contents
     *
     * @param   string  Folder name
     * @return  boolean true/false
     * @access  public
     */
    public function isFolderEmpty($folder)
    {
    }
    
    /**
     * Returns canonical path of given file or dir
     *
     * @param   string  $name
     * @return  boolean true/false
     */
    public function getFullPath($name)
    {
        $realPath = realpath($name);
        if ($realPath != false)
        {
            return $realPath;
        }
        return false;
    }
    
    /**
     * Creates a local directory
     *
     * @param   string  $dirName
     * @param   integer $mode
     * @return  boolean true on success; otherwise false
     */
    public function createDir($dirName, $mode = 0700)
    {
        if (!is_dir($dirName)) {
            return @mkdir($dirName, $mode);
        }
        return true;
    }
}
?>