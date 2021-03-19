<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210318111955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C96653C55F64');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C96654B9D732');
        $this->addSql('DROP INDEX UNIQ_5373C96653C55F64 ON country');
        $this->addSql('DROP INDEX UNIQ_5373C96654B9D732 ON country');
        $this->addSql('ALTER TABLE country ADD updated_at DATETIME NOT NULL, ADD map_name VARCHAR(255) DEFAULT NULL, ADD map_original_name VARCHAR(255) DEFAULT NULL, ADD map_mime_type VARCHAR(255) DEFAULT NULL, ADD map_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD icon_name VARCHAR(255) DEFAULT NULL, ADD icon_original_name VARCHAR(255) DEFAULT NULL, ADD icon_mime_type VARCHAR(255) DEFAULT NULL, ADD icon_size INT DEFAULT NULL, ADD icon_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', DROP map_id, CHANGE icon_id map_size INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country ADD map_id INT NOT NULL, ADD icon_id INT DEFAULT NULL, DROP updated_at, DROP map_name, DROP map_original_name, DROP map_mime_type, DROP map_size, DROP map_dimensions, DROP icon_name, DROP icon_original_name, DROP icon_mime_type, DROP icon_size, DROP icon_dimensions');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C96653C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C96654B9D732 FOREIGN KEY (icon_id) REFERENCES icon (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C96653C55F64 ON country (map_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5373C96654B9D732 ON country (icon_id)');
    }
}
