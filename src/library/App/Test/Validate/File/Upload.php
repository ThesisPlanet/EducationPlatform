<?php
/**
 * Replaces the default file upload validator because is_uploaded_file causes a file attack exception to be thrown.
 */
namespace App\Test\Validate\File;

class Upload extends \Zend_Validate_File_Upload
{

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the file was uploaded without errors
     *
     * @param string $value
     *            Single file to check for upload errors, when giving null the
     *            $_FILES array
     *            from initialization will be used
     * @return boolean
     */
    public function isValid ($value, $file = null)
    {
        $this->_messages = null;
        if (array_key_exists($value, $this->_files)) {
            $files[$value] = $this->_files[$value];
        } else {
            foreach ($this->_files as $file => $content) {
                if (isset($content['name']) && ($content['name'] === $value)) {
                    $files[$file] = $this->_files[$file];
                }
                
                if (isset($content['tmp_name']) &&
                         ($content['tmp_name'] === $value)) {
                    $files[$file] = $this->_files[$file];
                }
            }
        }
        
        if (empty($files)) {
            return $this->_throw($file, self::FILE_NOT_FOUND);
        }
        
        foreach ($files as $file => $content) {
            $this->_value = $file;
            switch ($content['error']) {
                case 0:
                    
                    if (! is_uploaded_file($content['tmp_name'])) {
                        // commented this out. We are knowingly 'hacking' the
                        // hypothetical "$_FILES" superglobal.
                        // $this->_throw($file, self::ATTACK);
                    }
                    break;
                
                case 1:
                    $this->_throw($file, self::INI_SIZE);
                    break;
                
                case 2:
                    $this->_throw($file, self::FORM_SIZE);
                    break;
                
                case 3:
                    $this->_throw($file, self::PARTIAL);
                    break;
                
                case 4:
                    $this->_throw($file, self::NO_FILE);
                    break;
                
                case 6:
                    $this->_throw($file, self::NO_TMP_DIR);
                    break;
                
                case 7:
                    $this->_throw($file, self::CANT_WRITE);
                    break;
                
                case 8:
                    $this->_throw($file, self::EXTENSION);
                    break;
                
                default:
                    $this->_throw($file, self::UNKNOWN);
                    break;
            }
        }
        
        if (count($this->_messages) > 0) {
            return false;
        } else {
            return true;
        }
    }
}