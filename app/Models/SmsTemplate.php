<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use PDO; 

class SmsTemplate extends ApiModel 
{
        /**
     * table name used by model 
     * @var string
     */
    public $tableName = 'sms_templates'; //Initialize table name for model
    
    /**
     * Accept parameters to get sms templetes
     * @param type $params
     * @return Boolean
     */

    public function getSmsTemplates($params = []) {
        try 
        {
            $stmt = $this->pdo->prepare("CALL getSmsTemplates(?)");
            $stmt->execute(array($params['template_name']));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result)
            {
                return $result['message'];
            }
            else
            {
                return "";
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
