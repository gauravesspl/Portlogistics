<?php
namespace App\Services;


class PlanService {

    /**
     * Prepare Custom Array Planning Data
     * 
     * @param array $planningArray planning data
     * 
     * @return array $customArrayData
     */
    public function prepareCustomData($planningArray)
    {
        $customArrayData = [];
        $customArrayData['id'] = $planningArray['id'];
        $customArrayData['origin_id'] = $planningArray['origin_id'];
        $customArrayData['date_from'] = $planningArray['date_from'];
        $customArrayData['date_to'] = $planningArray['date_to'];
        $customArrayData['location'] = $planningArray['location']['location'];
        $customArrayData['cargo'] = ['id' => $planningArray['cargo']['id'], 'name' => $planningArray['cargo']['name']];
        $customArrayData['vessel'] = ['id' => $planningArray['vessel']['id'], 'name' => $planningArray['vessel']['name']];
        foreach ($planningArray['consignees'] as $consignee) {
            $customArrayData['consignees'][] = ['consignee_id' => $consignee['consignee_id'], 'name' => $consignee['name']];
        }
        foreach ($planningArray['plots'] as $plot) {
            $customArrayData['plots'][] = ['consignee_id' => $plot['consignee_id'], 'destination_id' => $plot['destination_id'], 'location' => $plot['location']];
        }
        foreach ($planningArray['trucks'] as $truck) {
            $customArrayData['trucks'][] = ['id' => $truck['truck_id'], 'truck_no' => $truck['truck_no']];
        }
        return $customArrayData;
    }
}