<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323142533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, team1Score INT DEFAULT NULL, team2Score INT DEFAULT NULL, tournament_id INT NOT NULL, team1_id INT NOT NULL, team2_id INT NOT NULL, winner_id INT DEFAULT NULL, INDEX IDX_FF232B3133D1A3E7 (tournament_id), INDEX IDX_FF232B31E72BCFA4 (team1_id), INDEX IDX_FF232B31F59E604A (team2_id), INDEX IDX_FF232B315DFCD4B8 (winner_id), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tournaments (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31E72BCFA4 FOREIGN KEY (team1_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31F59E604A FOREIGN KEY (team2_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B315DFCD4B8 FOREIGN KEY (winner_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE teams ADD tournament_id INT NOT NULL, DROP points, DROP gamesPlayed, DROP draws');
        $this->addSql('ALTER TABLE teams ADD CONSTRAINT FK_96C2225833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id)');
        $this->addSql('CREATE INDEX IDX_96C2225833D1A3E7 ON teams (tournament_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3133D1A3E7');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31E72BCFA4');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31F59E604A');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B315DFCD4B8');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE tournaments');
        $this->addSql('ALTER TABLE teams DROP FOREIGN KEY FK_96C2225833D1A3E7');
        $this->addSql('DROP INDEX IDX_96C2225833D1A3E7 ON teams');
        $this->addSql('ALTER TABLE teams ADD gamesPlayed INT NOT NULL, ADD draws INT NOT NULL, CHANGE tournament_id points INT NOT NULL');
    }
}
