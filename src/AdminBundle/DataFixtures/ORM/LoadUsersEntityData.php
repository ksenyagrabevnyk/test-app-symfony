<?php

namespace AdminBundle\DataFixtures\ORM;

use AdminBundle\Entity\MediaType;
use AdminBundle\Entity\Roles;
use AdminBundle\Entity\Users;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadUsersEntityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $user = new Users();
        $user
            ->setUsername('admin')
            ->setFirstName('firstName')
            ->setSecondName('secondName')
            ->setThirdName('thirdName')
            ->setCity('city')
            ->setDescription('description')
            ->setEmail('admin@mail.com')
            ->setPassword('admin')
            ->setRole($this->getReference('admin_role'))
            ->setEnabled(1)
        ;
        $superUser = new Users();
        $superUser
            ->setUsername('superadmin')
            ->setFirstName('firstName')
            ->setSecondName('secondName')
            ->setThirdName('thirdName')
            ->setCity('city')
            ->setDescription('description')
            ->setEmail('superadmin@mail.com')
            ->setPassword('superadmin')
            ->setRole($this->getReference('super_admin_role'))
            ->setEnabled(1)
        ;


        $manager->persist($user);
        $manager->persist($superUser);
        $manager->flush();
        $this->addReference('admin', $user);
        $this->addReference('super_admin', $superUser);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}