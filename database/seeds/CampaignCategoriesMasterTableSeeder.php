<?php

use App\Models\CampaignCategoriesMaster;
use Illuminate\Database\Seeder;

class CampaignCategoriesMasterTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'             => 1,
                'name'           => 'Order Based',
                'created_by'     => 1
            ],
            [
                'id'             => 2,
                'name'           => 'Event Based',
                'created_by'     => 1
            ],
            [
                'id'             => 3,
                'name'           => 'Engagement',
                'created_by'     => 1
            ],
            [
                'id'             => 4,
                'name'           => 'Boost Sale',
                'created_by'     => 1
            ],
        ];

        CampaignCategoriesMaster::insert($data);
    }
}
