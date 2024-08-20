<?php

namespace App\Plugins;

use App\Plugins\SocialActivity\SocialActivityPlugin;

class Plugins 
{
    protected static $plugins = [
        SocialActivityPlugin::class
    ];
    
    public static function handle($message)
    {
        $responses = [];
        foreach (self::$plugins as $plugin) {
            $keywords = $plugin::$keywords;
            foreach ($keywords as $keyword => $func) {
                if(stristr($message->raw_message, $keyword) === false){
                    continue;
                }
                $response = $plugin::$func($message);
                if($response){
                    $responses[] = $response;
                }
            }
        }

        return $responses;
    }
}
