<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;

class UserFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstname('Artem');
        $user->setLastname('Samokhin');
        $user->setGender('male');
        $user->setPhone(123456789);
        $user->setBirthday(new DateTime('1995-07-16'));
        $user->setEmail('artjom.manuilov@yahoo.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsAuthorised(true);
        $user->setIsDeleted(false);
        $user->setIsActive(true);
        $user->setImage(null);
        $user->setPassword($this->encoder->encodePassword($user,'root12345'));

        $manager->persist($user);
        $manager->flush();
    }
}
