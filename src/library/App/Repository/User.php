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
namespace App\Repository;
use Doctrine\ORM\EntityRepository;
class User extends EntityRepository
{
    CONST NOT_FOUND = "not_found";
    public function findByEmail ($email)
    {
        $query = $this->_em->createQuery(
        'SELECT u FROM App\Entity\User u WHERE u.email = ?1');
        $query->setParameter(1, $email);
        $users = $query->getResult();
        if (is_array($users)) {
            if (count($users) > 0)
                return $users[0];
        } else {
            throw new \exception(self::NOT_FOUND);
        }
    }
}