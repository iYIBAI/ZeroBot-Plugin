<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialActivity extends BaseModel
{
    use HasFactory;

    protected $casts  = [
        'sign_up' => 'json',
    ];

    protected $fillable = [
        'name',
        'activity_date',
        'man_limit',
        'days_active',
    ];

    public function formatForOnebot()
    {
        $text = $this->name . PHP_EOL;
        $text .= '人数：' . ( $this->man_limit ? $this->man_limit . '人' : '不限') . PHP_EOL;
        $text .= '日期：' . $this->activity_date . PHP_EOL;
        $text .= '已报名人数：' . (!empty($this->sign_up) ? count($this->sign_up) : 0) . PHP_EOL;
        if(!empty($this->sign_up)){
            $text .= $this->signupFormat($this->sign_up, $this->days_active);
        }

        return $text;
    }

    public function signupFormat($sign_up, $days_active)
    {
        $sugnUp = [];
        foreach ($sign_up as $key => $value) {
            $item = $value['nickname'] . '（' . $key . '）';
            if($this->days_active > 1){
                $item .= '，' . $value['days'] . '天';
            }
            $sugnUp[] = $item;
        }

        return PHP_EOL . implode(PHP_EOL, $sugnUp);
    }
}
