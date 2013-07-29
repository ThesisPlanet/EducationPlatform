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

class Course extends EntityRepository
{

    public function search ($terms)
    {
        $query = $this->_em->createQuery(
                'SELECT c FROM App\Entity\Course c WHERE c.title LIKE ?1 AND c.isSearchable = 1');
        $query->setParameter(1, "%" . $terms . "%");
        $users = $query->getResult();
        if (is_array($users)) {
            return $users;
        } else {
            return array();
        }
    }

    public function findAllTopicsFromEnabledCourses ()
    {
        $query = $this->_em->createQuery(
                'SELECT c.topic FROM App\Entity\Course c WHERE c.isEnabled = true GROUP BY c.topic ORDER BY c.topic ASC');
        $r = $query->getResult();
        $arrOut = array();
        foreach ($r as $key => $returnVal) {
            $arrOut[$key] = $returnVal['topic'];
        }
        return $arrOut;
    }

    public function findAllEnabledOrderedByRecency ()
    {
        $query = $this->_em->createQuery(
                'SELECT c FROM App\Entity\Course c WHERE c.isEnabled = true ORDER BY c.id DESC');
        return $query->getResult();
    }

    public function findAllEnabledOrderedByTitle ()
    {
        $query = $this->_em->createQuery(
                'SELECT c FROM App\Entity\Course c WHERE c.isEnabled = true ORDER BY c.title ASC');
        return $query->getResult();
    }

    public function findAllEnabledOrderedBySubscriberCount ()
    {
        $qb = $this->createQueryBuilder('\App\Entity\Course');
        $qb->select('c, count(s.id) as subscriptionCount')
            ->add('from', '\App\Entity\Course c')
            ->where('c.isEnabled = true')
            ->addGroupBy('c.id')
            ->join('c._subscriptions', 's')
            ->orderBy('subscriptionCount', 'DESC');

        $result = $qb->getQuery()->getResult();

        return array_map(
                function  ($r)
                {
                    return $r[0];
                }, $result);

        /*
         * $query = $this->_em->createQuery( 'SELECT c FROM App\Entity\Course c
         * JOIN c._subscriptions s WHERE c.isEnabled = true GROUP BY c.id ORDER
         * BY s.id DESC, c.title ASC'); return $query->getResult();
         */
    }

    public function findAllDisabledOrderedByTitle ()
    {}

    public function findExistingTopicsForEnabledCourses ()
    {}
}