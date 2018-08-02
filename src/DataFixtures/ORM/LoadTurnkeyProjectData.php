<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TurnkeyProject;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadTurnkeyProjectData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $turnkeyProject1 = new TurnkeyProject(
            'Stop mégots !',
            'Campagnes de sensibilisation et de revalorisation des mégots jetés',
            $this->getReference('cpc001'),
            'Les mégots sont jetés en abondance dans la rue. ',
            'S\'inscrivant dans la dynamique du Plan Climat, notre Projet vise à sensibiliser les consommateurs à jeter les mégots dans les contenants prévus à cet effet.',
            'Notre équipe accueille tous volontaires intéressés pour nous aider à concrétiser notre projet, par une contribution ponctuelle ou suivie.',
            true,
            false,
            1,
            'stop-megots'
        );
        $this->addReference('turnkey-project-environment', $turnkeyProject1);

        $turnkeyProject2 = new TurnkeyProject(
            'Un stage pour tous',
            'Aider les collégiens à trouver un stage même sans réseau',
            $this->getReference('cpc002'),
            'Les collégiens ont parfois des difficultés à trouver un stage de découverte par manque de relations, de réseau.',
            'Le projet a pour objectif de mettre en relation ces élèves avec un réseau de professionnels volontaires pour les accueillir.',
            'Des réunions',
            true,
            true,
            2,
            'un-stage-pour-tous'
        );
        $this->addReference('turnkey-project-education', $turnkeyProject2);

        $turnkeyProject3 = new TurnkeyProject(
            'Art\'s connection',
            'Ateliers de rencontre autour de l\'art',
            $this->getReference('cpc003'),
            'Les lieux et espaces de culture sont rarement accessibles à tous et donnent peu l\'occasion de tisser du lien social.',
            'Nous proposons d\'organiser des ateliers d\'art participatif associant des artistes aux citoyens ',
            'Création d\'un nouveau lieu ou espace proposant à des citoyens de réaliser une oeuvre encadrée par des artistes.',
            false,
            true,
            3,
            'art-s-connection'
        );
        $this->addReference('turnkey-project-culture', $turnkeyProject3);

        $turnkeyProject4 = new TurnkeyProject(
            'Cafés Citoyens',
            'Citoyens de la Cité, vous avez des projets ? Nous vous aidons à les concrétiser!',
            $this->getReference('cpc004'),
            'Les quartiers populaires sont un vrai réservoir de créativité, et les idées y pullulent. Mais trop souvent celles-ci restent à l\'état d\'idées, faute d\'encouragement et d\'accompagnement.',
            'Nous proposons de recréer un lieu de convivialité où il sera possible d\'échanger, de débattre, de confronter ses idées autour d\'une boisson chaude.',
            'Réalisation des cafés citoyens',
            false,
            false,
            4,
            'cafes-citoyens'
        );
        $this->addReference('turnkey-project-social-link', $turnkeyProject4);

        $turnkeyProject5 = new TurnkeyProject(
            'La santé pour tous !',
            'Sensibilisation à la santé dans les écoles',
            $this->getReference('cpc005'),
            ' Les étudiants et professeurs d\'université rencontrent des difficultés dans sa mise en œuvre locale.',
            'Le Projet consiste à faciliter l\'organisation et la mise en œuvre du Service Sanitaire dans une ou plusieurs écoles',
            'Recruter des professionnels',
            true,
            true,
            5,
            'la-sante-pour-tous'
        );
        $this->addReference('turnkey-project-health', $turnkeyProject5);

        $manager->persist($turnkeyProject1);
        $manager->persist($turnkeyProject2);
        $manager->persist($turnkeyProject3);
        $manager->persist($turnkeyProject4);
        $manager->persist($turnkeyProject5);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadCitizenProjectCategoryData::class,
        ];
    }
}
