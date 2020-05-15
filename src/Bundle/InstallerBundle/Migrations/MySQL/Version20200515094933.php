<?php declare(strict_types=1);

namespace Integrated\Bundle\InstallerBundle\Migrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Integrated\Bundle\InstallerBundle\Doctrine\ORM\Migration\AbstractMigration;
use Integrated\Bundle\UserBundle\Model\Scope;

final class Version20200515094933 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $manager = $this->getEntityManager();
        $repository = $manager->getRepository(Scope::class);
        if (!$scope = $repository->findOneBy(['admin' => true])) {
            $scope = new Scope();
            $scope
                ->setName('Integrated')
                ->setAdmin(true)
            ;

            $manager->persist($scope);
            $manager->flush();
        }
    }

    public function down(Schema $schema) : void
    {
    }
}
