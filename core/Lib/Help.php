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
 * agnos help document 
 */
$help = <<<HELP
Usage : agnos.php [options] -f <test file>
        agnos.php [options] -r <dir>

Options
-h          Show this help and exit
-a          Show agnos info, author, version, and other shameless plugins
-f          Parse and execute a <test file>
-r          Recursively parse and execute a <dir> of test file(s)
-c          Number of test cycle for all the test cases 
-p          Number of test cycle per test file
-v          Show verbose output

--version   Version number
--dir       Recursively parse and execute a <dir> of test file(s)
--tmpdir    Temporary Directory
--help      Show this help and exit

HELP;

?>