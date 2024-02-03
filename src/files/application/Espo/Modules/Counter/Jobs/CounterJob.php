<?php

namespace Espo\Modules\Counter\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\ORM\EntityManager;
use Espo\Modules\Counter\Entities\Counter;
use Espo\Entities\User;

class CounterJob implements Job
{
    private EntityManager $entityManager; // Atributes of Class

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        $couterRepository = $this->entityManager->getRDBRepositoryByClass(Counter::class);  // Kiek yra įrašų?

        $userRepository = $this->entityManager->getRDBRepositoryByClass(User::class);   // Kiek yra USER'ių?

        $numberOfCounterEntities = $couterRepository->count()+1;

        $numberOfUser = $userRepository->count();

        $counter = $this->entityManager->getNewEntity('Counter');

        $counter->set('diskSize', 1.2);

        $counter->set('size', 7.2);

        $counter->set('numberOfRecords', $numberOfCounterEntities);

        $counter->set('numberOfUsers', $numberOfUser);

        $this->entityManager->saveEntity($counter);
    }
}

