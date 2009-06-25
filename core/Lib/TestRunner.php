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

class Core_Lib_TestRunner {
    
    function __construct(){}
    
    /**
     * Executes the given test file, this time no other checking/setup is
     * involved, just run it and get the result.
     * 
     * This is the main bread and butter of the whole test framework. If theres
     * anything that needs to be polished or improved should be this method. 
     *
     * @param  string  $commands to execute the test file
     * @param  mixed   environment variable needed by the test file before firing
     *                 the $commands
     * @param  string  standard input
     * @return mixed   Returns data output after the $commands is invoked
     * 
     * @access public
     */    
    public function console($cmd, $env = null, $stdin = null)
    {
        if (!empty($cmd)) {
            
            $data       = "";
            $env        = ($env == null) ? $_ENV : $env;
            $cwd        = (($c = getcwd()) != false) ? $c : null;
            $pipes      = array();
            $descriptor = array 
                          (
                              0 => array('pipe', 'r'),
                              1 => array('pipe', 'w'),
                              2 => array('pipe', 'w')
                          );
                               
            $proc = proc_open($cmd, $descriptor, 
                              $pipes, $cwd, $env, array("suppress_errors" => true));
        
            if ($proc && is_resource($proc)) {
                  
                if (is_string($stdin)) {
                    fwrite($pipes[0], $stdin);
                }
        
                fclose($pipes[0]);
            
                while (true) {
                    /* hide errors from interrupted syscalls */
                    $r = $pipes;
                    $w = null;
                    $e = null;
                    $n = @stream_select($r, $w, $e, 300);
            
                    if ($n === 0) {
                        /* timed out */
                        $data .= "\n ** ERROR: process timed out **\n";
                        proc_terminate($proc);
                        return $data;
                    } else if ($n > 0) {
                        $line = fread($pipes[1], 8192);
                        if (strlen($line) == 0) {
                            /* EOF */
                            break;
                        }
                        $data .= $line;
                    }
                }

				# TODO: add more handler for $stat return value
                $stat = proc_get_status($proc);
                if ($stat['signaled']) {
                    $data .= "\nTermsig=".$stat['stopsig'];
                }
                
                # TODO: implement (proc_close($proc) >> 8) & 0xff;
                $code = proc_close($proc);
                        
                return $data;
            }
            
            /* close pipe */
            fclose($pipes[0]);
        }
        return false;        
    }
}
?>
