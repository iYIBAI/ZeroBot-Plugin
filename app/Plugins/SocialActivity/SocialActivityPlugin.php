<?php

namespace App\Plugins\SocialActivity;

use App\Models\SocialActivity;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SocialActivityPlugin
{
    public static $keywords = [
        '社团活动教程' => 'help',
        '社团活动信息' => 'info',
        '创建社团活动' => 'create',
        '社团活动报名' => 'signUp',
        '社团活动抽签' => 'draw',
    ];

    public static function info($message)
    {
        $activity = SocialActivity::where('activity_date', '>', now())->orderByDesc('id')->first();
        if(empty($activity)){
            return '当前没用社区活动';
        }

        return $activity->formatForOnebot();
    }

    public static function create($message)
    {
        if(!self::checkSuperUser($message->sender->user_id)){
            return false;
        }

        if($message->message_type != 'private'){
            return '请私聊创建';
        }

        $activity = SocialActivity::where('activity_date', '>', now())->orderByDesc('id')->first();
        if(!empty($activity)){
            return '已经有一个进行中的活动了';
        }

        $data = explode('：', $message->raw_message);
        if(count($data) != 2){
            return '创建失败，信息不完整';
        }
        $info = explode('，', $data[1]);
        if(count($info) != 4){
            return '创建失败，信息不完整';
        }

        $name = $info[0] ?? '';
        $activity_date = $info[1] ?? now();
        $man_limit = $info[2] ?? 0;
        $man_limit = filter_var($man_limit, FILTER_SANITIZE_NUMBER_INT);
        $days_active = $info[3] ?? 0;
        $days_active = filter_var($days_active, FILTER_SANITIZE_NUMBER_INT);

        if($activity_date < now()){
            return '日期不能比今天早哦~';
        }

        $socialActivity = new SocialActivity([
            'name' => $name,
            'activity_date' => $activity_date,
            'man_limit' => $man_limit,
            'days_active' => $days_active,
        ]);

        $socialActivity->save();
        return '创建成功';
    }

    public static function signUp($message)
    {
        $activity = SocialActivity::where('activity_date', '>', now())->orderByDesc('id')->first();
        if(empty($activity)){
            return '当前没有可报名的社团活动';
        }

        $sign_up = $activity->sign_up;
        if(isset($sign_up[$message->sender->user_id])){
            return '你已经报名不要重复报名';
        }
        $days = 1;
        if($activity->days_active > 1){
            $days = preg_replace('/[^0-9]/', '', $message->raw_message);
            if(!$days){
                return '活动有好几天哦~，你要报名多少天？'.PHP_EOL.'报名实例'.PHP_EOL.'社团活动报名，x天';
            }
            if($days > $activity->days_active){
                return '报名天数不能比活动天数长哦~';
            }
            if($days <= 0){
                return '报名天数不对哦~';
            }

        }

        $sign_up[$message->sender->user_id] = [
            'nickname' => $message->sender->nickname,
            'user_id' => $message->sender->user_id,
            'days' => $days,
        ];
        $activity->sign_up = $sign_up;
        $activity->save();
        return '报名成功';
    }

    public static function draw($message)
    {
        if(!self::checkSuperUser($message->sender->user_id)){
            return false;
        }
        $activity = SocialActivity::where('activity_date', '>', now())->orderByDesc('id')->first();
        if(empty($activity)){
            return '当前没有可报名的社团活动';
        }
        if($activity->man_limit == 0){
            return '当前活动不限制人数，不需要抽签';
        }
        if($activity->man_limit >= count($activity->sign_up)){
            return '报名人数小于等于活动人数，不需要抽签';
        }
        
        //权重
        $weights = [];
        foreach ($activity->sign_up as $key => $value) {
            $weights += $value['days'];
            for ($i=0; $i < $value['days']; $i++) { 
                $weights[] = $key;
            }
        }

        // 随机抽签
        $lucks = [];
        while (count($lucks) < $activity->man_limit) {
            $random = Arr::random($array);
            in_array($random, $lucks) ?: $lucks[] = $activity->sign_up[$random];
        }

        return '抽签完成'.PHP_EOL.$activity->signupFormat($lucks, $activity->days_active);
    }

    public static function help($message)
    {
        if(!self::checkSuperUser($message->sender->user_id)){
            return false;
        }

        $help = '社团活动帮助信息'.PHP_EOL.PHP_EOL;
        $help .= '社团活动信息：获取最近进行中的活动。包含报名情况。'.PHP_EOL;
        $help .= '创建社团活动'.PHP_EOL;
        $help .= '创建一个新的社团活动，但是的有进行中的活动，不能再创建了。请使用中文标点符号。示例如下：'.PHP_EOL;
        $help .= '   创建社团活动：7月子鱼社团摊，日期2023-02-03，人数1人，天数1'.PHP_EOL;
        $help .= PHP_EOL;
        $help .= '报名社团活动'.PHP_EOL;
        $help .= '报名参加最近进行中的活动，示例如下：'.PHP_EOL;
        $help .= '   社团活动报名，1天。'.PHP_EOL;
        $help .= PHP_EOL;
        $help .= '社团活动抽签'.PHP_EOL;
        $help .= '当报名人数超出活动人数限制，才进行抽签，示例如下：'.PHP_EOL;
        $help .= '   社团活动抽签'.PHP_EOL;

        return $help;
    }

    public static function checkSuperUser($user)
    {
        $super_user = config('pulgin.super_user');
        if(empty($super_user)){
            return false;
        }
        $super_user = explode(',', $super_user);
        if(!in_array($user, $super_user)){
            return false;
        }

        return true;
    }
}
