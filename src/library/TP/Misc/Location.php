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
namespace TP\Misc;
class Location
{
    public static function getStates ()
    {
        $states = array('AL' => 'ALABAMA', 'AK' => 'ALASKA', 'AZ' => 'ARIZONA', 
        'AR' => 'ARKANSAS', 'CA' => 'CALIFORNIA', 'CO' => 'COLORADO', 
        'CT' => 'CONNECTICUT', 'DE' => 'DELAWARE', 
        'DC' => 'DISTRICT OF COLUMBIA', 'FL' => 'FLORIDA', 'GA' => 'GEORGIA', 
        'HA' => 'HAWAII', 'ID' => 'IDAHO', 'IL' => 'ILLINOIS', 'IN' => 'INDIANA', 
        'IA' => 'IOWA', 'KS' => 'KANSAS', 'KY' => 'KENTUCKY', 
        'LA' => 'LOUISIANA', 'ME' => 'MAINE', 'MD' => 'MARYLAND', 
        'MA' => 'MASSACHUSETTS', 'MI' => 'MICHIGAN', 'MN' => 'MINNESOTA', 
        'MS' => 'MISSISSIPPI', 'MO' => 'MISSOURI', 'MT' => 'MONTANA', 
        'NE' => 'NEBRASKA', 'NV' => 'NEVADA', 'NH' => 'NEW HAMPSHIRE', 
        'NJ' => 'NEW JERSEY', 'NM' => 'NEW MEXICO', 'NY' => 'NEW YORK', 
        'NC' => 'NORTH CAROLINA', 'ND' => 'NORTH DAKOTA', 'OH' => 'OHIO', 
        'OK' => 'OKLAHOMA', 'OR' => 'OREGON', 'PA' => 'PENNSYLVANIA', 
        'RI' => 'RHODE ISLAND', 'SC' => 'SOUTH CAROLINA', 'SD' => 'SOUTH DAKOTA', 
        'TN' => 'TENNESSEE', 'TX' => 'TEXAS', 'UT' => 'UTAH', 'VT' => 'VERMONT', 
        'VA' => 'VIRGINIA', 'WA' => 'WASHINGTON', 'WV' => 'WEST VIRGINIA', 
        'WI' => 'WISCONSIN', 'WY' => 'WYOMING');
        return $states;
    }
    public static function getCitiesByState ()
    {}
}
