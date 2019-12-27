<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180826222742 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE app_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE permission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app (id INT NOT NULL, name VARCHAR(255) NOT NULL, key_application VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C96E70CF5E237E06 ON app (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C96E70CFFF35DE82 ON app (key_application)');
        $this->addSql('CREATE TABLE client (uuid UUID NOT NULL, id_app INT NOT NULL, device_type INT NOT NULL, device_model INT NOT NULL, ip_address VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token_push_messages VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_C744045557CA9895 ON client (id_app)');
        $this->addSql('CREATE TABLE old_hash (uuid UUID NOT NULL, uuid_user UUID NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_F925E31D235218F5 ON old_hash (uuid_user)');
        $this->addSql('CREATE TABLE permission (id INT NOT NULL, role_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E04992AAD60322AC ON permission (role_id)');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, app_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A7987212D ON role (app_id)');
        $this->addSql('CREATE TABLE social_account (uuid UUID NOT NULL, uuid_user UUID NOT NULL, social_id VARCHAR(255) NOT NULL, access_token VARCHAR(255) DEFAULT NULL, token_secret VARCHAR(255) DEFAULT NULL, type_social_network INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_F24D8339235218F5 ON social_account (uuid_user)');
        $this->addSql('CREATE TABLE token (id INT NOT NULL, id_user UUID NOT NULL, id_client UUID NOT NULL, jwttoken VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F37A13B6B3CA4B ON token (id_user)');
        $this->addSql('CREATE INDEX IDX_5F37A13BE173B1B8 ON token (id_client)');
        $this->addSql('CREATE TABLE "user" (uuid UUID NOT NULL, email VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, token_reset_password VARCHAR(255) DEFAULT NULL, authentication_key VARCHAR(255) DEFAULT NULL, status_id INT DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045557CA9895 FOREIGN KEY (id_app) REFERENCES app (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE old_hash ADD CONSTRAINT FK_F925E31D235218F5 FOREIGN KEY (uuid_user) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAD60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A7987212D FOREIGN KEY (app_id) REFERENCES app (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_account ADD CONSTRAINT FK_F24D8339235218F5 FOREIGN KEY (uuid_user) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B6B3CA4B FOREIGN KEY (id_user) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BE173B1B8 FOREIGN KEY (id_client) REFERENCES client (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C744045557CA9895');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT FK_57698A6A7987212D');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13BE173B1B8');
        $this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AAD60322AC');
        $this->addSql('ALTER TABLE old_hash DROP CONSTRAINT FK_F925E31D235218F5');
        $this->addSql('ALTER TABLE social_account DROP CONSTRAINT FK_F24D8339235218F5');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13B6B3CA4B');
        $this->addSql('DROP SEQUENCE app_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE permission_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE token_id_seq CASCADE');
        $this->addSql('DROP TABLE app');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE old_hash');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE social_account');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE "user"');
    }
}
