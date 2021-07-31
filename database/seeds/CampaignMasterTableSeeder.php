<?php

use App\Models\CampaignMaster;
use Illuminate\Database\Seeder;

class CampaignMasterTableSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'                   => 1,
                'name'                 => 'Order Wise',
                'campaign_category_id' => 1,
                'created_by'           => 1
            ],
            [
                'id'                   => 2,
                'name'                 => 'Category Wise',
                'campaign_category_id' => 1,
                'created_by'           => 1
            ],
            [
                'id'                   => 3,
                'name'                 => 'Sub Category Wise',
                'campaign_category_id' => 1,
                'created_by'           => 1
            ],
            [
                'id'                   => 4,
                'name'                 => 'Registration',
                'campaign_category_id' => 2,
                'created_by'           => 1
            ],
            [
                'id'                   => 5,
                'name'                 => 'Birthday',
                'campaign_category_id' => 2,
                'created_by'           => 1
            ],
            [
                'id'                   => 6,
                'name'                 => 'Anniversary',
                'campaign_category_id' => 2,
                'created_by'           => 1
            ],
            [
                'id'                   => 7,
                'name'                 => 'Referral Registration (Referrer)',
                'campaign_category_id' => 3,
                'created_by'           => 1
            ],
            [
                'id'                   => 8,
                'name'                 => 'Referral Registration (Referee)',
                'campaign_category_id' => 3,
                'created_by'           => 1
            ],
            [
                'id'                   => 9,
                'name'                 => 'Referral 1st Order (Referrer)',
                'campaign_category_id' => 3,
                'created_by'           => 1
            ],
            [
                'id'                   => 10,
                'name'                 => 'Referral 1st Order (Referee)',
                'campaign_category_id' => 3,
                'created_by'           => 1
            ],
            [
                'id'                   => 11,
                'name'                 => 'Boost Sale',
                'campaign_category_id' => 4,
                'created_by'           => 1
            ],
        ];

        CampaignMaster::insert($data);
    }
}
