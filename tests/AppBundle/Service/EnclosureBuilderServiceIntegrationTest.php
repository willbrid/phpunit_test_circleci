<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Entity\Security;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnclosureBuilderServiceIntegrationTest extends KernelTestCase
{
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        self::bootKernel();

        $this->truncateEntities();
    }

    public function testItBuildsEnclosureWithDefaultSpecification()
    {
        /* @var EnclosureBuilderService $enclosureBuilderService
        $enclosureBuilderService = self::$kernel->getContainer()
                                                ->get('test.' . EnclosureBuilderService::class); */

        $em = $this->getEntityManager();

        $dinoFactory = $this->createMock(DinosaurFactory::class);
        $dinoFactory->expects($this->any())
                    ->method('growFromSpecification')
                    ->willReturnCallback(function($spec) {
                        return new Dinosaur();
                    });

        $enclosureBuilderService = new EnclosureBuilderService(
            $em,
            $dinoFactory
        );
        $enclosureBuilderService->buildEnclosure();

        $count = (int) $em->getRepository(Security::class)
            ->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(1, $count, 'Amount of securities is not the same.');

        $count = (int) $em->getRepository(Dinosaur::class)
                          ->createQueryBuilder('d')
                          ->select('count(d.id)')
                          ->getQuery()
                          ->getSingleScalarResult();

        $this->assertSame(3, $count, 'Amount of dinosaurs is not the same.');
    }

    // just copy this method! :)
    private function truncateEntities()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return self::$kernel->getContainer()
                            ->get('doctrine')
                            ->getManager();
    }

    /* just copy this method! :)
    private function truncateEntities(array $entities)
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );

            $connection->executeUpdate($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }
    */
}