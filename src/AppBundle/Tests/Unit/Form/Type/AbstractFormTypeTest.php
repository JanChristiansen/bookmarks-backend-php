<?php

namespace AppBundle\Tests\Unit\Form\Type;

use AppBundle\Tests\DoctrineQueryStub;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Gedmo\Tree\TreeListener;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class AbstractFormTypeTest extends TypeTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emRegistry;

    public function setUp()
    {
        $this->entityManager = DoctrineTestHelper::createTestEntityManager();
        $this->emRegistry = $this->createRegistryMock('default', $this->entityManager);

        $listener = new TreeListener();
        $this->entityManager->getEventManager()->addEventListener($listener->getSubscribedEvents(), $listener);

        parent::setUp();

        $this->createSchema();
    }

    /**
     * @return array
     */
    protected function getExtensions()
    {
        return array_merge(
            parent::getExtensions(),
            array(
                new DoctrineOrmExtension($this->emRegistry),
            )
        );
    }

    /**
     * Create a mock of entity manager registry
     *
     * @param string $name
     * @param EntityManager $em
     *
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    protected function createRegistryMock($name, $em)
    {
        $registry = $this->getMock(ManagerRegistry::class);
        $registry->expects($this->any())
            ->method('getManager')
            ->with($this->equalTo($name))
            ->will($this->returnValue($em));

        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($em));

        return $registry;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $queryResult
     */
    protected function createQuery(QueryBuilder $queryBuilder, $queryResult)
    {
        $queryMock = $this->getMockBuilder(DoctrineQueryStub::class)
            ->setMethods(array('execute'))
            ->disableOriginalConstructor()
            ->getMock();
        $queryMock->expects($this->any())->method('setParameters')->willReturn($queryMock);
        $queryMock->expects($this->any())->method('setFirstResult')->willReturn($queryMock);
        $queryMock->expects($this->any())->method('setMaxResults')->willReturn($queryMock);
        $queryMock->expects($this->any())->method('execute')->willReturn($queryResult);

        $this->entityManager->expects($this->any())
            ->method('createQuery')
            ->with($queryBuilder->getDQL())
            ->will($this->returnValue($queryMock));

        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($queryMock));
    }


    /**
     * Create the schema that will be used for testing
     */
    protected function createSchema()
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $classes = [];

        foreach ($this->getEntities() as $entityClass) {
            $classes[] = $this->entityManager->getClassMetadata($entityClass);
        }

        try {
            $schemaTool->dropSchema($classes);
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the entities to map with the entity manager
     * Register entities used in 'entity' fields here
     *
     * @return array
     */
    protected function getEntities()
    {
        return array();
    }

    /**
     * @param $entity
     */
    protected function persist($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);
    }
}
