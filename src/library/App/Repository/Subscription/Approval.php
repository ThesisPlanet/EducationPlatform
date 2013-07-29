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
namespace App\Repository\Subscription;
use Doctrine\ORM\EntityRepository;
class Approval extends EntityRepository
{
    const NOT_FOUND = "no approval found.";
    public function findByCourse ($courseId)
    {
        $stmt = 'SELECT s FROM App\Entity\Subscription\Approval s WHERE s._course = :course ORDER BY s.id ASC';
        return $this->_em->createQuery($stmt)
            ->setParameter('course', $courseId)
            ->getResult();
    }
    public function findByCourseAndEmail ($courseId, $email)
    {
        $stmt = 'SELECT s FROM App\Entity\Subscription\Approval s WHERE s._course = :course AND s.email = :email';
        $q = $this->_em->createQuery($stmt)
            ->setParameter('course', $courseId)
            ->setParameter('email', $email);
        $approvals = $q->getResult();
        if (is_array($approvals)) {
            if (count($approvals) > 0)
                return $approvals[0];
        } else {
            throw new \exception(self::NOT_FOUND);
        }
    }
    public function findByEmail ($email)
    {
        $stmt = 'SELECT s FROM App\Entity\Subscription\Approval s WHERE s.email = :email ORDER BY s.id ASC';
        return $this->_em->createQuery($stmt)
            ->setParameter('email', $email)
            ->getResult();
    }
    public function findByUser ($userId)
    {
        $stmt = 'SELECT s FROM App\Entity\Subscription\Approval s WHERE s._user = :user ORDER BY s.id ASC';
        return $this->_em->createQuery($stmt)
            ->setParameter('_user', $userId)
            ->getResult();
    }
}