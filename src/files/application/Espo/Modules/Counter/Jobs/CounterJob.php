<?php

namespace Espo\Modules\Counter\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\ORM\Repository\RDBRepository;

class CounterJob implements Job
{
    private EntityManager $entityManager;   // Attributes of Class

    public function __construct(EntityManager $entityManager) { // Depednacy Injection

        $this->entityManager = $entityManager;
    }
    
    public function run(Data $data): void
    {   
        $userRepository = $this->entityManager->getRDBRepositoryByClass(User::class);   // Counting users 
        $numberOfUser = $userRepository->count();

        $numberOfRowsFromAllEntities = $this->countAllTableRows(); // Counting rows from Entities

        $counter = $this->entityManager->getNewEntity('Counter');

        $totalSpace = disk_total_space('/') / pow(1024, 3);
        $projectSize = $this->getDirectorySize($_SERVER['DOCUMENT_ROOT']) / pow(1024, 3);

        $counter->set('diskSize', (float) $totalSpace);
        $counter->set('size', (float) $projectSize);
        $counter->set('numberOfRecords', $numberOfRowsFromAllEntities);
        $counter->set('numberOfUsers', $numberOfUser);

        $this->entityManager->saveEntity($counter);
    }

    private function countAllTableRows(): int
    {
        $totalCount = 0;
        $totalEntityList = $this->entityManager->getMetadata()->getEntityTypeList();

        foreach($totalEntityList as $entity){
            if($this->entityManager->hasRepository($entity) === false) {
                continue;
            }
            $entityRepository = $this->entityManager->getRepository($entity);

            if(!$entityRepository instanceof RDBRepository){
                continue;
            }

            try{
                $totalCount = $entityRepository->count() + $totalCount;
            }catch(\PDOException){
                continue;
            }
        }

        return $totalCount;
    }

    private function getDirectorySize($path) 
    {
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
}

