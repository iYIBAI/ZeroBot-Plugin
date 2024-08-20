<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Client\Connector;
use React\EventLoop\Factory as LoopFactory;
use App\Plugins\Plugins;

class OneBotWsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:one-bot-ws-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'OneBot ws链接';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $loop = LoopFactory::create();
        $connector = new Connector($loop);

        $connector(config('app.zerobot_ws'))
            ->then(function ($conn) {
                $conn->on('message', function ($msg) use ($conn) {
                    try {
                        $msg = json_decode($msg);
                        if(!empty($msg) && isset($msg->post_type) && $msg->post_type == 'message'){
                            if(isset($message->group_id)){
                                $message_type = 'group';
                                $action = 'send_group_msg';
                            }else{
                                $message_type = 'private';
                                $action = 'send_private_msg';
                            }
                            $responses = Plugins::handle($msg);
                            if(!empty($responses)){
                                foreach ($responses as $key => $response) {
                                    $message = [
                                        'message_type' => $message_type,
                                        'group_id' => $msg->group_id ?? 0,
                                        'user_id' => $msg->user_id ?? 0,
                                        'message' => [
                                            'type' => 'text',
                                            'data' => [
                                                'text' => $response,
                                            ]
                                        ],
                                    ];
                                    $message = [
                                        'action' => $action,
                                        'params' => $message,
                                    ];
                                    $conn->send(json_encode($message));
                                }
                            }
                        }
                        
                    } catch (\Exception $e) {
                        dump($e);
                    }
                });

                $conn->on('close', function ($code = null, $reason = null) {
                    echo "Connection closed ({$code} - {$reason})\n";
                });

                // Sending a message to the WebSocket server
                $conn->send('Hello Server!');

                // You can add more logic here to keep the connection alive
            }, function ($e) use ($loop) {
                echo "Could not connect: {$e->getMessage()}\n";
                $loop->stop();
            });

        $loop->run();


    }
}
