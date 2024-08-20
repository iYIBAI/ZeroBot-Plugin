<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plugins\SocialActivity\SocialActivityPlugin;
use App\Models\User;
use App\Service\BaseService;
use App\Models\SocialActivity;

class TestController extends Controller
{
    public function test($value='')
    {

        User::search([['name', 'like'], 'email'])->whereToday()->get();
        $message = '{"self_id":1229580290,"user_id":1178401374,"time":1718170340,"message_id":-2147483519,"real_id":-2147483519,"message_seq":-2147483519,"message_type":"group","sender":{"user_id":1178401374,"nickname":"七月萝Julradish","card":"","role":"member"},"raw_message":"社团活动报名，1天","font":14,"sub_type":"normal","message":[{"data":{"text":"创建社团活动：123，2024-09-03，人数1人，天数1"},"type":"text"}],"message_format":"array","post_type":"message","group_id":628982893}';

        $message = '{"self_id":1229580290,"user_id":854862025,"time":1724062287,"message_id":-2147483321,"real_id":-2147483321,"message_seq":-2147483321,"message_type":"private","sender":{"user_id":854862025,"nickname":"YIBAI","card":""},"raw_message":"\u521b\u5efa\u793e\u56e2\u6d3b\u52a8\uff1a123\uff0c2024-09-03\uff0c\u4eba\u65701\u4eba\uff0c\u5929\u65701","font":14,"sub_type":"friend","message":[{"data":{"text":"\u521b\u5efa\u793e\u56e2\u6d3b\u52a8\uff1a123\uff0c2024-09-03\uff0c\u4eba\u65701\u4eba\uff0c\u5929\u65701"},"type":"text"}],"message_format":"array","post_type":"message"}';
        
        $message = json_decode($message);

        $signUp = SocialActivityPlugin::create($message);
        $info = SocialActivityPlugin::info($message);
        dd($signUp, $info);
    }
}
