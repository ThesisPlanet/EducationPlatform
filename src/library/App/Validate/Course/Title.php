<?php
/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Validate\Course;

class Title extends \Zend_validate_Abstract
{

    const NOT_AVAILABLE = 'There is already a course with that name';

    const INVALID_TITLE = 'invalidTitle';

    const STRING_EMPTY = 'stringEmpty';

    const RESERVED = 'reserved';

    protected $_messageTemplates = array(
            self::NOT_AVAILABLE => "'%value%' already exists. Please pick another title.",
            self::INVALID_TITLE => "'%value%' is not a valid title. A title must contain letters and/or numbers. spaces are allows.",
            self::STRING_EMPTY => "Please provide a title.",
            self::RESERVED => "a phrase in your username has been reserved to help prevent confusion."
    );

    public function isValid ($value)
    {
        $this->_setValue($value);
        if (trim($value == null)) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }
        if (! is_string($value)) {
            $this->_error(self::INVALID_TITLE);
            return false;
        }
        // reserved
        if (! preg_match("/^([a-zA-Z0-9._ \\-]+)$/", $value)) {
            $this->_error(self::INVALID_TITLE);
            return false;
        }
        // defer database to the last minute (load reduction)
        $service = new \App\Service\Course();
        $course = $service->findOneByTitle($value);
        if (is_object($course)) {
            $this->_error(self::NOT_AVAILABLE);
            return false;
        }
        return true;
    }
}
