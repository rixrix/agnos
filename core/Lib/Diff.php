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
 * Base class for Diffing utilities
 *
 */
class Core_Lib_Diff 
{
    function __construct() 
    {
    }
	
    /**
     * Compares two sets of string case-sensitively.
     *
     * @param  string  $_line1
     * @param  string  $_line2
     * @param  boolean $_is_regex
     * 
     * @return boolean True if line1/line2 is equal; False otherwise
     * @access public
     */
    public function diffLine($_line1, $_line2, $_is_regex = false) 
    {
        if ($_is_regex == true) {
            return preg_match('/^\b'.$_line1.'\b/', $_line2);	       	
	} else {
            if (strcmp($_line1, $_line2) == 0) {
                return true;
	    }
	}
	   return false;
    }
	
    /**
     * Generates diff of the "EXPECT"ed out and the actual output
     * 
     * @param   string  The EXPECTed output
     * @param   string  The actual output 
     * @param   mixed   The EXPECTed regular expression output
     * 
     * @return  string  String with diff marking on it. Diff -Naur style
     * 
     * @access  public
     */	
    public function diffGenerate($the_expected_output, $the_actual_output, $the_expected_output_regex = false)
    {
        $exp_actual_output         = explode("\n", $the_actual_output);
        $exp_expected_output       = explode("\n", $the_expected_output); 
        $exp_expected_output_regex = ($the_expected_output_regex == false) ?
                                      $the_expected_output : explode("\n", $the_expected_output_regex);
                                             
        $diff = $this->diffArrayGenerate($exp_expected_output_regex, $exp_actual_output,
                                         $the_expected_output_regex, $exp_expected_output);
                                    
        $diff = implode("\r\n", $diff);
    
        return $diff;	    
    }
	
    /**
     * Generates diff output string from the two array 
     *
     * @param  array    $ar1
     * @param  array    $ar2
     * @param  boolean  $is_reg
     * @param  string   $w
     * @return string
     * 
     * @todo   refactor
     */
    public function diffArrayGenerate($ar1, $ar2, $is_reg = false, $w) 
    {
        $diff = array();
        $old1 = array();
        $old2 = array();
                
        $idx1 = 0; $ofs1 = 0; $cnt1 = count($ar1);
        $idx2 = 0; $ofs2 = 0; $cnt2 = count($ar2);
    
        while ($idx1 < $cnt1 && $idx2 < $cnt2) {
            /*
             * Compare array values of $ar1 against $ar2 by its index specified
             * by $idx1 and $idx2.
             */
            if ($this->diffLine($ar1[$idx1], $ar2[$idx2], $is_reg)) {
                $idx1++;
                $idx2++;
                continue;
            } else {
                $c1 = $this->diffArrayCount($ar1, $ar2, $is_reg, $w, $idx1+1, $idx2,   $cnt1, $cnt2, 10);
                $c2 = $this->diffArrayCount($ar1, $ar2, $is_reg, $w, $idx1,   $idx2+1, $cnt1, $cnt2, 10);
                if ($c1 > $c2) {
                    $old1[$idx1] = sprintf("%03d- ", $idx1+1).$w[$idx1++];
                    $last        = 1;
                } else if ($c2 > 0) {
                    $old2[$idx2] = sprintf("%03d+ ", $idx2+1).$ar2[$idx2++];
                    $last        = 2;
                } else {
                    $old1[$idx1] = sprintf("%03d- ", $idx1+1).$w[$idx1++];
                    $old2[$idx2] = sprintf("%03d+ ", $idx2+1).$ar2[$idx2++];
                }
            }
        }
    
        reset($old1); $k1 = key($old1); $l1 = -2;
        reset($old2); $k2 = key($old2); $l2 = -2;
        
        while ($k1 !== NULL  || $k2 !== NULL) {
            if ($k1 == $l1+1 || $k2 === NULL) {
                $l1     = $k1;
                $diff[] = current($old1);
                $k1     = next($old1) ? key($old1) : NULL;
            } else if ($k2 == $l2+1 || $k1 === NULL) {
                $l2     = $k2;
                $diff[] = current($old2);
                $k2     = next($old2) ? key($old2) : NULL;
            } else if ($k1 < $k2) {
                $l1     = $k1;
                $diff[] = current($old1);
                $k1     = next($old1) ? key($old1) : NULL;
            } else {
                $l2     = $k2;
                $diff[] = current($old2);
                $k2     = next($old2) ? key($old2) : NULL;
            }
        }
        
        while ($idx1 < $cnt1) {
            $diff[] = sprintf("%03d- ", $idx1+1).$w[$idx1++];
        }
        
        while ($idx2 < $cnt2) {
            $diff[] = sprintf("%03d+ ", $idx2+1).$ar2[$idx2++];
        }
        
        return $diff;		
    }
	
    /**
     * Returns array count difference
     *
     * @param  array    $ar1
     * @param  array    $ar2
     * @param  boolean  $is_reg
     * @param  string   $w
     * @param  integer  $idx1
     * @param  integer  $idx2
     * @param  integer  $cnt1
     * @param  integer  $cnt2
     * @param  integer  $steps
     * 
     * @return integer  count
     * 
     * @todo   refactor and document 
     */
    public function diffArrayCount($ar1, $ar2, $is_reg, $w, $idx1, $idx2, $cnt1, $cnt2, $steps)
    {
        $equal = 0;
        while (($idx1 < $cnt1) && ($idx2 < $cnt2) && $this->diffLine($ar1[$idx1], $ar2[$idx2], $is_reg)) {
                $idx1++;
                $idx2++;
                $equal++;
                $steps--;
        }
        
        if (--$steps > 0) {
            $eq1 = 0;
            $st  = $steps / 2;
            for ($ofs1 = $idx1+1; $ofs1 < $cnt1 && $st-- > 0; $ofs1++) {
                $eq = $this->diffArrayCount($ar1,$ar2,$is_reg,$w,$ofs1,$idx2,$cnt1,$cnt2,$st);
                if ($eq > $eq1) {
                    $eq1 = $eq;
                }
            }
            
            $eq2 = 0;
            $st  = $steps;
            
            for ($ofs2 = $idx2+1; $ofs2 < $cnt2 && $st-- > 0; $ofs2++) {
                $eq = $this->diffArrayCount($ar1,$ar2,$is_reg,$w,$idx1,$ofs2,$cnt1,$cnt2,$st);
                if ($eq > $eq2) {
                    $eq2 = $eq;
                }
            }
            
            if ($eq1 > $eq2) {
                $equal += $eq1;
            } else if ($eq2 > 0) {
                $equal += $eq2;
            }
        }
        return $equal;	    
    }
}

?>
