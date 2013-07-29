<?php
/**
 * Class to override the receive function to allow for processing an actual file from a unit test.
 * @author Jack.Peterson
 *
 */
namespace App\Test\File\Transfer\Adapter;

class HttpSimulate extends \Zend_File_Transfer_Adapter_Http
{

    protected $_incomingFiles;

    public function setFiles ($fileArray)
    {
        $this->_incomingFiles = $fileArray;
        $this->_prepareFiles();
        //$this->addValidator('Upload', false, $this->_files);
    }

    public function __construct ($options = array())
    {
        if (ini_get('file_uploads') == false) {
            require_once 'Zend/File/Transfer/Exception.php';
            throw new \Zend_File_Transfer_Exception(
                    'File uploads are not allowed in your php config!');
        }
        
        $this->setOptions($options);
    }

    /**
     * Sets a validator for the class, erasing all previous set
     *
     * @param string|array $validator
     *            Validator to set
     * @param string|array $files
     *            Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setValidators (array $validators, $files = null)
    {
        $this->clearValidators();
        return $this->addValidators($validators, $files);
    }

    /**
     * Remove an individual validator
     *
     * @param string $name            
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function removeValidator ($name)
    {
        return parent::removeValidator($name);
    }

    /**
     * Remove an individual validator
     *
     * @param string $name            
     * @return Zend_File_Transfer_Adapter_Abstract
     */
    public function clearValidators ()
    {
        parent::clearValidators();
        
        return $this;
    }

    /**
     * Checks if the files are valid
     *
     * @param string|array $files
     *            (Optional) Files to check
     * @return boolean True if all checks are valid
     */
    public function isValid ($files = null)
    {
        // Workaround for WebServer not conforming HTTP and omitting
        // CONTENT_LENGTH
        $content = 0;
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $content = $_SERVER['CONTENT_LENGTH'];
        } else 
            if (! empty($_POST)) {
                $content = serialize($_POST);
            }
        
        // Workaround for a PHP error returning empty $_FILES when form data
        // exceeds php settings
        if (empty($this->_files) && ($content > 0)) {
            if (is_array($files)) {
                $files = current($files);
            }
            
            $temp = array(
                    $files => array(
                            'name' => $files,
                            'error' => 1
                    )
            );
            $validator = $this->_validators['\App\Test\Validate\File\Upload'];
            $validator->setFiles($temp)->isValid($files, null);
            $this->_messages += $validator->getMessages();
            return false;
        }
        
        return parent::isValid($files);
    }

    /**
     * move_uploaded_file doesn't allow for simulating a file upload.
     *
     * @see Zend_File_Transfer_Adapter_Http::receive()
     */
    public function receive ($files = null)
    {
        if (! $this->isValid($files)) {
            return false;
        }
        
        $check = $this->_getFiles($files);
        foreach ($check as $file => $content) {
            if (! $content['received']) {
                $directory = '';
                $destination = $this->getDestination($file);
                if ($destination !== null) {
                    $directory = $destination . DIRECTORY_SEPARATOR;
                }
                
                $filename = $directory . $content['name'];
                $rename = $this->getFilter('Rename');
                if ($rename !== null) {
                    $tmp = $rename->getNewName($content['tmp_name']);
                    if ($tmp != $content['tmp_name']) {
                        $filename = $tmp;
                    }
                    
                    if (dirname($filename) == '.') {
                        $filename = $directory . $filename;
                    }
                    
                    $key = array_search(get_class($rename), 
                            $this->_files[$file]['filters']);
                    unset($this->_files[$file]['filters'][$key]);
                }
                // Replaced move_uploaded_file with rename.
                if (! rename($content['tmp_name'], $filename)) {
                    if ($content['options']['ignoreNoFile']) {
                        $this->_files[$file]['received'] = true;
                        $this->_files[$file]['filtered'] = true;
                        continue;
                    }
                    
                    $this->_files[$file]['received'] = false;
                    return false;
                }
                
                if ($rename !== null) {
                    $this->_files[$file]['destination'] = dirname($filename);
                    $this->_files[$file]['name'] = basename($filename);
                }
                
                $this->_files[$file]['tmp_name'] = $filename;
                $this->_files[$file]['received'] = true;
            }
            
            if (! $content['filtered']) {
                if (! $this->_filter($file)) {
                    $this->_files[$file]['filtered'] = false;
                    return false;
                }
                
                $this->_files[$file]['filtered'] = true;
            }
        }
        
        return true;
    }

    protected function _prepareFiles ()
    {
        $this->_files = array();
        
        foreach ($this->_incomingFiles as $form => $content) {
            if (is_array($content['name'])) {
                foreach ($content as $param => $file) {
                    foreach ($file as $number => $target) {
                        $this->_files[$form . '_' . $number . '_'][$param] = $target;
                        $this->_files[$form]['multifiles'][$number] = $form . '_' .
                                 $number . '_';
                    }
                }
                
                $this->_files[$form]['name'] = $form;
                foreach ($this->_files[$form]['multifiles'] as $key => $value) {
                    $this->_files[$value]['options'] = $this->_options;
                    $this->_files[$value]['validated'] = false;
                    $this->_files[$value]['received'] = false;
                    $this->_files[$value]['filtered'] = false;
                    
                    $mimetype = $this->_detectMimeType($this->_files[$value]);
                    $this->_files[$value]['type'] = $mimetype;
                    
                    $filesize = $this->_detectFileSize($this->_files[$value]);
                    $this->_files[$value]['size'] = $filesize;
                    
                    if ($this->_options['detectInfos']) {
                        $this->_incomingFiles[$form]['type'][$key] = $mimetype;
                        $this->_incomingFiles[$form]['size'][$key] = $filesize;
                    }
                }
            } else {
                $this->_files[$form] = $content;
                $this->_files[$form]['options'] = $this->_options;
                $this->_files[$form]['validated'] = false;
                $this->_files[$form]['received'] = false;
                $this->_files[$form]['filtered'] = false;
                
                $mimetype = $this->_detectMimeType($this->_files[$form]);
                $this->_files[$form]['type'] = $mimetype;
                
                $filesize = $this->_detectFileSize($this->_files[$form]);
                $this->_files[$form]['size'] = $filesize;
                
                if ($this->_options['detectInfos']) {
                    $this->_incomingFiles[$form]['type'] = $mimetype;
                    $this->_incomingFiles[$form]['size'] = $filesize;
                }
            }
        }
        
        return $this;
    }
}