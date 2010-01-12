<?php

/*
 *  $Id: CapsuleTask.php 557 2009-08-29 13:54:38Z mrook $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>. 
 */

require_once "phing/Task.php";

/**
 * Saves currently defined properties into a specified file
 * 
 * @author Andrei Serdeliuc
 * @extends Task
 * @version   $Id: CapsuleTask.php 557 2009-08-29 13:54:38Z mrook $
 * @package   phing.tasks.ext
 */
class ExportProperties extends Task
{
    /**
     * Array of project properties
     * 
     * (default value: null)
     * 
     * @var array
     * @access private
     */
    private $_properties = null;
    
    /**
     * Target file for saved properties
     * 
     * (default value: null)
     * 
     * @var string
     * @access private
     */
    private $_targetFile = null;
    
    /**
     * Exclude properties starting with these prefixes
     * 
     * @var array
     * @access private
     */
    private $_disallowedPropertyPrefixes = array(
        'host.',
        'phing.',
        'os.',
        'php.',
        'line.',
        'env.',
        'user.'
    );

    /**
     * setter for _targetFile
     * 
     * @access public
     * @param string $file
     * @return bool
     */
    public function setTargetFile($file)
    {
        if(!is_dir(dirname($file))) {
            throw new BuildException("Parent directory of target file doesn't exist");
        }
        
        if(!is_writable(dirname($file)) && (file_exists($file) && !is_writable($file))) {
            throw new BuildException("Target file isn't writable");
        }
        
        $this->_targetFile = $file;
        return true;
    }
    
    /**
     * setter for _disallowedPropertyPrefixes
     * 
     * @access public
     * @param string $file
     * @return bool
     */
    public function setDisallowedPropertyPrefixes($prefixes)
    {
        $this->_disallowedPropertyPrefixes = explode(",", $prefixes);
        return true;
    }    

    /**
     * Sets the currently declared properties
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_properties = $this->getProject()->getProperties();
    }

    public function main()
    {
        if(is_array($this->_properties) && !empty($this->_properties) && null !== $this->_targetFile) {
            $propertiesString = '';
            foreach($this->_properties as $propertyName => $propertyValue) {
                if(!$this->isDisallowedPropery($propertyName)) {
                    $propertiesString .= $propertyName . "=" . $propertyValue . PHP_EOL;
                }
            }
            
            if(!file_put_contents($this->_targetFile, $propertiesString)) {
                throw new BuildException('Failed writing to ' . $this->_targetFile);
            }
        }
    }
    
    /**
     * Checks if a property name is disallowed
     * 
     * @access protected
     * @param string $propertyName
     * @return bool
     */
    protected function isDisallowedPropery($propertyName)
    {
        foreach($this->_disallowedPropertyPrefixes as $property) {
            if(substr($propertyName, 0, strlen($property)) == $property) {
                return true;
            }
        }
        
        return false;
    }
}

?>