<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160120215730 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $databaseName = $this->container->getParameter('database_name');

        //$this->addSql("CREATE DATABASE " . $databaseName);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $databaseName = $this->container->getParameter('database_name');

        $this->addSql("DROP DATABASE " . $databaseName);
    }
}
