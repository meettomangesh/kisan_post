<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\PromoCodes;
use App\Models\PromoCodeFormat;
use DB;
use PDO;

class PromoCodeMaster extends Model
{
    use SoftDeletes;

    public $table = 'promo_code_master';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'reward_type', 'reward_type_x_value', 'type', 'promo_code', 'qty', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function campaignMaster(){
        return $this->belongsTo(CampaignMaster::class, 'campaign_master_id');
    } 

    public function campaignCategory(){
        return $this->belongsTo(CampaignCategoriesMaster::class, 'campaign_category_id');
    } 

    public function promoCodes()
    {
        return $this->hasMany(PromoCodes::class, 'promo_code_master_id');
    }

    public function promoCodesFormat()
    {
        return $this->hasOne(PromoCodeFormat::class, 'promo_code_master_id');
    }


    public static function addUpdateCampaignOffer($params)
    {
        try {
            $inputData = json_encode($params);
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL addUpdateCampaignOffer(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);
            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }
    }
}
