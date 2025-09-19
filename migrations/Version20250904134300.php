<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904134300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) DEFAULT NULL, city VARCHAR(128) DEFAULT NULL, siret VARCHAR(14) DEFAULT NULL, siren VARCHAR(9) DEFAULT NULL, region VARCHAR(128) DEFAULT NULL, vat_number VARCHAR(20) DEFAULT NULL, activity_type VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, number_of_employee INT DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, comments LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4FBF094FE51E9644 (company_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_client (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, company_type_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, siret VARCHAR(14) DEFAULT NULL, siren VARCHAR(9) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, vat_number VARCHAR(255) DEFAULT NULL, activity_type VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FADB6020979B1AD6 (company_id), INDEX IDX_FADB6020E51E9644 (company_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_client_overload (id INT AUTO_INCREMENT NOT NULL, company_client_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(255) NOT NULL, INDEX IDX_5D2F94902AF0E3D1 (company_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_type (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(64) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(3) NOT NULL, symbol VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_log (id INT AUTO_INCREMENT NOT NULL, smtp_configuration_id INT DEFAULT NULL, company_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', to_email VARCHAR(255) DEFAULT NULL, cc_email VARCHAR(1000) DEFAULT NULL, bcc_email VARCHAR(1000) DEFAULT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, attachement LONGBLOB DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, error_message LONGTEXT DEFAULT NULL, sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6FB4883C0915A50 (smtp_configuration_id), INDEX IDX_6FB4883979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, quotation_id INT DEFAULT NULL, company_id INT DEFAULT NULL, company_client_id INT DEFAULT NULL, project_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', invoice_number VARCHAR(255) NOT NULL, issue_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', due_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, amount_ht NUMERIC(10, 0) NOT NULL, amount_ttc NUMERIC(10, 0) NOT NULL, tax_rate NUMERIC(10, 0) NOT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_90651744B4EA4E60 (quotation_id), INDEX IDX_90651744979B1AD6 (company_id), INDEX IDX_906517442AF0E3D1 (company_client_id), INDEX IDX_90651744166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, invoice_id INT DEFAULT NULL, item_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', description LONGTEXT DEFAULT NULL, quantity NUMERIC(10, 0) NOT NULL, unit VARCHAR(255) NOT NULL, unit_price NUMERIC(10, 0) DEFAULT NULL, total_price NUMERIC(10, 0) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1DDE477B2989F1FD (invoice_id), INDEX IDX_1DDE477B126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', status VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, quantity INT NOT NULL, quatity_reserved INT NOT NULL, quantity_alert_threshold INT NOT NULL, serial_number VARCHAR(255) DEFAULT NULL, unit VARCHAR(255) NOT NULL, price NUMERIC(10, 0) DEFAULT NULL, obsolet TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1F1B251E979B1AD6 (company_id), INDEX IDX_1F1B251E2ADD6D8C (supplier_id), INDEX IDX_1F1B251E38248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_project (item_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_24EEF6BA126F525E (item_id), INDEX IDX_24EEF6BA166D1F9C (project_id), PRIMARY KEY(item_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, item_id INT DEFAULT NULL, performed_by_user_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', maintenance_type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, scheduled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', next_maintenance_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', performed_by_name VARCHAR(255) DEFAULT NULL, cost DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2F84F8E9126F525E (item_id), INDEX IDX_2F84F8E943F2ED96 (performed_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_type (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, invoice_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', amount NUMERIC(10, 0) NOT NULL, payment_ref VARCHAR(255) DEFAULT NULL, payment_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', payment_method VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D28840D2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) DEFAULT NULL, city VARCHAR(128) DEFAULT NULL, region VARCHAR(64) DEFAULT NULL, country VARCHAR(64) DEFAULT NULL, start_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, budget NUMERIC(10, 0) DEFAULT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2FB3D0EE979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quotation (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, project_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, company_client_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, total_amount NUMERIC(10, 0) NOT NULL, valid_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discount NUMERIC(10, 0) DEFAULT NULL, tax NUMERIC(10, 0) DEFAULT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_474A8DB9979B1AD6 (company_id), INDEX IDX_474A8DB9166D1F9C (project_id), INDEX IDX_474A8DB938248176 (currency_id), INDEX IDX_474A8DB92AF0E3D1 (company_client_id), INDEX IDX_474A8DB9B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quotation_item (id INT AUTO_INCREMENT NOT NULL, quotation_id INT DEFAULT NULL, item_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', description LONGTEXT DEFAULT NULL, quantity INT NOT NULL, unit VARCHAR(255) NOT NULL, total_price NUMERIC(10, 0) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_82EF8052B4EA4E60 (quotation_id), INDEX IDX_82EF8052126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtp_configuration (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', smtp_host VARCHAR(255) NOT NULL, smtp_port INT NOT NULL, smtp_encryption VARCHAR(255) DEFAULT NULL, smtp_username VARCHAR(255) NOT NULL, smtp_password VARCHAR(255) NOT NULL, default_from_email VARCHAR(255) DEFAULT NULL, default_from_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_F3ACCB77979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, subscription_plan_id INT DEFAULT NULL, subscription_type_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', status VARCHAR(255) NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ended_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cancelled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A3C664D3979B1AD6 (company_id), INDEX IDX_A3C664D39B8CE200 (subscription_plan_id), INDEX IDX_A3C664D3B6596C08 (subscription_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_plan (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 0) NOT NULL, max_user INT DEFAULT NULL, trial_end_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(20) NOT NULL, features JSON NOT NULL, INDEX IDX_EA664B6338248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_type (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', code VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, zipcode VARCHAR(5) DEFAULT NULL, city VARCHAR(64) DEFAULT NULL, siret VARCHAR(14) DEFAULT NULL, siren VARCHAR(9) DEFAULT NULL, region VARCHAR(128) DEFAULT NULL, vat_number VARCHAR(255) DEFAULT NULL, activity_type VARCHAR(128) DEFAULT NULL, website VARCHAR(1024) DEFAULT NULL, active TINYINT(1) NOT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B2A6C7E979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_order (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, company_id INT DEFAULT NULL, currency_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', order_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expected_delivery_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_amount NUMERIC(10, 0) NOT NULL, comments LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2C3291B22ADD6D8C (supplier_id), INDEX IDX_2C3291B2979B1AD6 (company_id), INDEX IDX_2C3291B238248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_order_item (id INT AUTO_INCREMENT NOT NULL, supplier_order_id INT DEFAULT NULL, item_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', quantity_ordered INT NOT NULL, quantity_reveived INT DEFAULT NULL, unit_price NUMERIC(10, 0) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_331BD2E01605B9 (supplier_order_id), INDEX IDX_331BD2E0126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(64) DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', phone VARCHAR(16) DEFAULT NULL, theme VARCHAR(10) NOT NULL, job_title VARCHAR(64) DEFAULT NULL, user_confirmed TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8D93D649979B1AD6 (company_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_notification_type (user_id INT NOT NULL, notification_type_id INT NOT NULL, INDEX IDX_CCFDA738A76ED395 (user_id), INDEX IDX_CCFDA738D0520624 (notification_type_id), PRIMARY KEY(user_id, notification_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FE51E9644 FOREIGN KEY (company_type_id) REFERENCES company_type (id)');
        $this->addSql('ALTER TABLE company_client ADD CONSTRAINT FK_FADB6020979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company_client ADD CONSTRAINT FK_FADB6020E51E9644 FOREIGN KEY (company_type_id) REFERENCES company_type (id)');
        $this->addSql('ALTER TABLE company_client_overload ADD CONSTRAINT FK_5D2F94902AF0E3D1 FOREIGN KEY (company_client_id) REFERENCES company_client (id)');
        $this->addSql('ALTER TABLE email_log ADD CONSTRAINT FK_6FB4883C0915A50 FOREIGN KEY (smtp_configuration_id) REFERENCES smtp_configuration (id)');
        $this->addSql('ALTER TABLE email_log ADD CONSTRAINT FK_6FB4883979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744B4EA4E60 FOREIGN KEY (quotation_id) REFERENCES quotation (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517442AF0E3D1 FOREIGN KEY (company_client_id) REFERENCES company_client (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE item_project ADD CONSTRAINT FK_24EEF6BA126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_project ADD CONSTRAINT FK_24EEF6BA166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E943F2ED96 FOREIGN KEY (performed_by_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB9979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB9166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB938248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB92AF0E3D1 FOREIGN KEY (company_client_id) REFERENCES company_client (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quotation_item ADD CONSTRAINT FK_82EF8052B4EA4E60 FOREIGN KEY (quotation_id) REFERENCES quotation (id)');
        $this->addSql('ALTER TABLE quotation_item ADD CONSTRAINT FK_82EF8052126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE smtp_configuration ADD CONSTRAINT FK_F3ACCB77979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3B6596C08 FOREIGN KEY (subscription_type_id) REFERENCES subscription_type (id)');
        $this->addSql('ALTER TABLE subscription_plan ADD CONSTRAINT FK_EA664B6338248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE supplier_order ADD CONSTRAINT FK_2C3291B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_order ADD CONSTRAINT FK_2C3291B2979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE supplier_order ADD CONSTRAINT FK_2C3291B238248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE supplier_order_item ADD CONSTRAINT FK_331BD2E01605B9 FOREIGN KEY (supplier_order_id) REFERENCES supplier_order (id)');
        $this->addSql('ALTER TABLE supplier_order_item ADD CONSTRAINT FK_331BD2E0126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user_notification_type ADD CONSTRAINT FK_CCFDA738A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notification_type ADD CONSTRAINT FK_CCFDA738D0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FE51E9644');
        $this->addSql('ALTER TABLE company_client DROP FOREIGN KEY FK_FADB6020979B1AD6');
        $this->addSql('ALTER TABLE company_client DROP FOREIGN KEY FK_FADB6020E51E9644');
        $this->addSql('ALTER TABLE company_client_overload DROP FOREIGN KEY FK_5D2F94902AF0E3D1');
        $this->addSql('ALTER TABLE email_log DROP FOREIGN KEY FK_6FB4883C0915A50');
        $this->addSql('ALTER TABLE email_log DROP FOREIGN KEY FK_6FB4883979B1AD6');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744B4EA4E60');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744979B1AD6');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517442AF0E3D1');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744166D1F9C');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B2989F1FD');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B126F525E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E979B1AD6');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E2ADD6D8C');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E38248176');
        $this->addSql('ALTER TABLE item_project DROP FOREIGN KEY FK_24EEF6BA126F525E');
        $this->addSql('ALTER TABLE item_project DROP FOREIGN KEY FK_24EEF6BA166D1F9C');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9126F525E');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E943F2ED96');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE979B1AD6');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB9979B1AD6');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB9166D1F9C');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB938248176');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB92AF0E3D1');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB9B03A8386');
        $this->addSql('ALTER TABLE quotation_item DROP FOREIGN KEY FK_82EF8052B4EA4E60');
        $this->addSql('ALTER TABLE quotation_item DROP FOREIGN KEY FK_82EF8052126F525E');
        $this->addSql('ALTER TABLE smtp_configuration DROP FOREIGN KEY FK_F3ACCB77979B1AD6');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3979B1AD6');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D39B8CE200');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3B6596C08');
        $this->addSql('ALTER TABLE subscription_plan DROP FOREIGN KEY FK_EA664B6338248176');
        $this->addSql('ALTER TABLE supplier DROP FOREIGN KEY FK_9B2A6C7E979B1AD6');
        $this->addSql('ALTER TABLE supplier_order DROP FOREIGN KEY FK_2C3291B22ADD6D8C');
        $this->addSql('ALTER TABLE supplier_order DROP FOREIGN KEY FK_2C3291B2979B1AD6');
        $this->addSql('ALTER TABLE supplier_order DROP FOREIGN KEY FK_2C3291B238248176');
        $this->addSql('ALTER TABLE supplier_order_item DROP FOREIGN KEY FK_331BD2E01605B9');
        $this->addSql('ALTER TABLE supplier_order_item DROP FOREIGN KEY FK_331BD2E0126F525E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE user_notification_type DROP FOREIGN KEY FK_CCFDA738A76ED395');
        $this->addSql('ALTER TABLE user_notification_type DROP FOREIGN KEY FK_CCFDA738D0520624');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_client');
        $this->addSql('DROP TABLE company_client_overload');
        $this->addSql('DROP TABLE company_type');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE email_log');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_project');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE notification_type');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE quotation');
        $this->addSql('DROP TABLE quotation_item');
        $this->addSql('DROP TABLE smtp_configuration');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_plan');
        $this->addSql('DROP TABLE subscription_type');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE supplier_order');
        $this->addSql('DROP TABLE supplier_order_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_notification_type');
    }
}
