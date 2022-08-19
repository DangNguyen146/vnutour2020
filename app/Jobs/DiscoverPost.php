<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;

use App\Player;
use App\Tag;
use App\Event;

class DiscoverPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    public function request($method, $url, $option = [], $check = true)
    {
        if(!array_key_exists('headers', $option)){
            $option['headers'] = [];
        }

        $option['headers']['User-Agent'] = 'Nokia3110c/2.0 (07.01) Profile/MIDP-2.0 Configuration/CLDC-1.1 UCWEB/2.0 (Java; U; MIDP-2.0; en-US; nokia3110c) U2/1.0.0 UCBrowser/8.9.0.251 U2/1.0.0 Mobile UNTRUSTED/1.0';

        if(!$this->client){
            $this->client = new \GuzzleHttp\Client();;
        }

        if(!in_array('allow_redirects', $option)){
            $option['allow_redirects'] = false;
        }

        try{
            $response = $this->client->request($method, $url, $option);

        }catch(\GuzzleHttp\Exception\ClientException $e){
            
            throw $e;
        }catch(\Exception $e){
            throw $e;
        }
        
        
        return $response;
    }
}
