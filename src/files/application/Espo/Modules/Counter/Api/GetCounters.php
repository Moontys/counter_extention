<?php
namespace Espo\Modules\Counter\Api;

use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\ORM\EntityManager;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Modules\Counter\Entities\Counter;

class GetCounters implements Action
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager) 
    {
        $this->entityManager = $entityManager;
    }

    public function process(Request $request): Response
    {
        $couterRepository = $this->entityManager->getRDBRepositoryByClass(Counter::class);  // Kiek yra įrašų?

        $counters = $couterRepository->find();

        $counterData = [];

        foreach($counters as $counter){
            $counterData[] = [
                'diskSize' => $counter->get('diskSize'),
                'size' => $counter->get('size'),
                'numberOfRecords' => $counter->get('numberOfRecords'),
                'numberOfUsers' => $counter->get('numberOfUsers')
            ];
        }

        return ResponseComposer::json($counterData);
    }
}