<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;

use App\Fbuser;
use App\Reaction;
use App\Post;

class FetchReact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $client;
    protected $jar;
    protected $account;


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
        // if(now()->greaterThan(new \Carbon\Carbon(config("app.finish_at")))){
        //     return;
        // }
        
        echo "Check if login is necessary\n";
        $this->login();

        echo "\nFetch players by post reaction\n";
        $this->fetchPostReact("3077368202316123");

        echo "\nFetch players by post comments\n";
        //Clear is_like_post of player
        // Player::where('skip', false)->update(["is_reject_comment"=>false]);
        $this->fetchPostComment("3077368202316123");
        
        echo "\nCalculating point of all eligible players\n";
        // Player::where('skip', false)->update(["is_has_hashtag"=>false]);
        $this->calcPointOfAllEligiblePlayers();

        echo "\nCalculating extra point of all players\n";
        Player::where('skip', false)->update(["detail"=>null]);
        $this->runCalcExtraPoint();

        echo "\nCalculating total point of all players\n";
        $this->runCalcTotalPoint();

        echo "\nCalculating ranking of all players\n";
        Player::where('skip', false)->update(["ranking"=>null]);
        $this->calcRanking();

        //Clear cache
        cache()->forever('lastSync', now());

        Log::info('Đã đồng bộ dữ liệu với Facebook xong');
    }


    public function fetchPostReaction($id){
        $post = Post::where('post_id', $id)->first();
        if(!$post){
            return;
        }
        $post_uuid = $post->id;
        $response = $this->request("GET",'https://m.facebook.com/'. $id, ['allow_redirect'=>true]);
        $bodyText = $response->getBody()->getContents();

        preg_match('/\/ufi\/reaction\/profile\/browser\/\?ft_ent_identifier=(\d+)/', $bodyText, $identifier);
        if(!$identifier){
            Log::error("Can not fetch Post Reaction. Account: ".$this->account);
            exit();
        }
        
        $identifier = $identifier[1];
        
        $response = $this->request("GET",'https://m.facebook.com/ufi/reaction/profile/browser/fetch/?limit=1000&total_count=1&ft_ent_identifier='. $identifier);
        $bodyText = $response->getBody()->getContents();
        
        $content = preg_match_all('/<table class="i j ba"><tbody><tr>(.*?)<\/tr><\/tbody><\/table>/', $bodyText, $reactions);
        $reactedUsers = [];
        foreach($reactions[1] as $reaction){
            preg_match('/<h3 class="be"><a href="([^"]+)">(.*?)<\/a><\/h3>/', $reaction, $accounts);
            $fbUserUrl = $accounts[1];
            $fbUserName = $accounts[2];

            preg_match('/class="bc p" alt="([^"]+)"/', $reaction, $reactType);
            $reactType = $reactType[1];
            
            $fbUser = Fbuser::where("fburl", $fbUserUrl)->first();
            if (!$fbUser){
                $fbUser = new Fbuser();
                $fbUser->fburl = $fbUserUrl;
                $fbUser->name = $fbUserName;
                $fbUser->save();
            }

            //Check if already reacted
            $saveReaction = Reaction::where('post_id',  $post_uuid)
                ->where('fbuser_id' , $fbUser->id)
                ->first();

            if(!$saveReaction){
                $saveReaction = new Reaction();
                $saveReaction->post_id =  $post_uuid;
                $saveReaction->fbuser_id = $fbUser->id;
                $saveReaction->type = $reactType;
                $saveReaction->save();
            }else if($saveReaction->type != $reactType && !$saveReaction->active){
                $saveReaction->type = $reactType;
                $saveReaction->active = true;
                $saveReaction->save();
            }
            array_push($reactedUsers, $fbUser->id);
        }
        // //Remove un-reacted
        Reaction::whereNotIn('fbuser_id', $reactedUsers)
            ->where('post_id', $post_uuid)
            ->update(['active' => 0]);

        //Counting share
        $response = $a->request("GET",'https://m.facebook.com/'. $id,
        [
            'allow_redirect'=>true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 7.0; Micromax Q371 Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.100 Mobile Safari/537.36'
            ]
        ]);

        $bodyText = $response->getBody()->getContents();
        preg_match("/(\d+)\s+Shares/", $bodyText, $share);
    }

    public function calcPointOfAllEligiblePlayers()
    {
        //Only check if already like post
        $players = Player::where('skip', false)->get();
        echo "Has ".$players->count()." players\n";
        foreach ($players as $player) {
            //Reset point
            // Player::where("skip", false)->update(["like"=>0, "love"=>0, "wow"=>0, "haha"=>0, "sad"=>0, "angry"=>"0", "point"=>0, "extra_point"=>0, "total_point"=>0]);

            if($player->skip || !$player->eligible){
                continue;
            }

            echo "Processing " . $player->fburl . "   " . $player->name . "\n";
            $this->calcPlayerPoint($player);
        }
    }

    public function calcPlayerPoint($player)
    {
        
        $profileAvatarLink = cache()->remember('profile.'.$player->id, 1200 + random_int(0,14400), function() use ($player){
            echo "    > Freshing player profile link\n";

            $fbUser = $player->fburl;

            //Goto profile to get avatar link
            $profileResponse = $this->request("GET", "https://m.facebook.com".$fbUser);
            $profileResponseString = $profileResponse->getBody()->getContents();

            if(strpos($profileResponseString, "We limit how often you can post")){
                echo "Facebook have limit activities. Stop and try later\n";
                Log::error("Facebook have limit activities. Stop and try later");
                return null;
            }

            //Detect if this is user page
            if(strpos($profileResponseString, "/pages/more/")){
                $player->skip = true;
                $player->save();
                return false;
            }

            preg_match('/(\/photo.php\?|\/profile\/picture\/view\/\?|\/story.php\?)[^"]+/', $profileResponseString, $profileAvatarLink);

            if(!$profileAvatarLink){
                return false;
            }

            return html_entity_decode($profileAvatarLink[0]);
        });

        if($profileAvatarLink === null){
            cache()->forget("profile.".$player->id);
            return false;
        }else if($profileAvatarLink === false){
            return false;
        }

        // echo "Avatar link: ".$profileAvatarLink ."\n";

        $profileAvatarResponse = $this->request("GET", "https://m.facebook.com".$profileAvatarLink);
        $profileAvatarResponseString = $profileAvatarResponse->getBody()->getContents();

        //Detect facebook id
        if(!$player->fbid){
            preg_match('/(\&id=|profile_id=)(\d+)/', $profileAvatarLink, $fbid);
            $fbid = $fbid[2];
            $player->fbid = $fbid;
            $player->save();
            // echo "FB ID: " . $fbid . "\n";
        }

        //Download avatar
        $img = file_get_contents("https://graph.facebook.com/".$player->fbid."/picture?type=large");
        $img = base64_encode($img);
        $player->avatar = $img;
        $player->save();

        // //Check if profile picture has hashtag
        if (!preg_match('/hashtag\/avatar.*?thanh.*?nien.*?chong.*?dich/', $profileAvatarResponseString)){
            return false;
        }

        $player->is_has_hashtag = true;
        $player->save();


        //Check reaction
        preg_match('/\/ufi\/reaction\/profile\/browser\/([^"]+)"/', $profileAvatarResponseString, $reactionSummaryLink);
        $reactionSummaryLink = "/ufi/reaction/profile/browser".$reactionSummaryLink[1];

        $reactionSummaryResponse = $this->request("GET", "https://m.facebook.com".$reactionSummaryLink);
        $reactionSummaryResponseString = $reactionSummaryResponse->getBody()->getContents();

        $like =0;
        $love = 0;
        $sad = 0;
        $angry = 0;
        $wow = 0;
        $haha = 0;

        if(preg_match('/reaction_type=1.*?total_count=(\d+)/', $reactionSummaryResponseString, $like_)){
            $like = intval($like_[1]);
        }
        $player->like =  $like;
        
        if(preg_match('/reaction_type=2.*?total_count=(\d+)/', $reactionSummaryResponseString, $love_)){
            $love = intval($love_[1]);
        }
        $player->love = $love;
        
        if(preg_match('/reaction_type=4.*?total_count=(\d+)/', $reactionSummaryResponseString, $haha_)){
            $haha = intval($haha_[1]);
        }
        $player->haha = $haha;
        
        if(preg_match('/reaction_type=8.*?total_count=(\d+)/', $reactionSummaryResponseString, $angry_)){
            $angry = intval($angry_[1]);
        }
        $player->angry = $angry;
        
        if(preg_match('/reaction_type=7.*?total_count=(\d+)/', $reactionSummaryResponseString, $sad_)){
            $sad = intval($sad_[1]);
        }
        $player->sad = $sad;

        if(preg_match('/reaction_type=3.*?total_count=(\d+)/', $reactionSummaryResponseString, $wow_)){
            $wow = intval($wow_[1]);
        }
        $player->wow = $wow;

        $player->point = 0;
        $player->point += $like;
        $player->point += $love*3;
        $player->point += $wow*3;
        $player->point += $haha*3;
        $player->point -= $sad*2;
        $player->point -= $angry*2;

        // $player->point = $like + ($love+$haha+$wow)*3 - ($sad+$angry)*2;
        $player->eligible = true;
        $player->save();


        //Check time uploaded
        if(preg_match('/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s*at\s*(\d+)\:(\d+).{1,200}Public/', $profileAvatarResponseString, $postedTime)){
            $currentWeekday = now()->format('l');
            $daysInWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
            $currentDayIndex = array_search($currentWeekday, $daysInWeek);
            $postedDayIndex = array_search($postedTime[1], $daysInWeek);
            $daysDiff = ($currentDayIndex +7 - $postedDayIndex) % 7;
            $datetime = \Carbon\Carbon::today()->subDays($daysDiff);
            $datetime = $datetime->addHours($postedTime[2]);
            $datetime = $datetime->addMinutes($postedTime[3]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/Yesterday\s*at\s*(\d+):(\d+).*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = \Carbon\Carbon::yesterday();
            $datetime = $datetime->addHours($postedTime[1]);
            $datetime = $datetime->addMinutes($postedTime[2]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/(\d+)\s*(hrs|hours)\s+ago.*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = now()->subHours($postedTime[1]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/(\d+)\s*(mins|minutes)\s+ago.*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = now()->subMinutes($postedTime[1]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/Just now.*?Public/', $profileAvatarResponseString, $postedTime)){
            $player->changed_avatar_at = now();
            $player->save();
        }
        elseif(preg_match('/((\d+)\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\s*at\s*(\d+):(\d+).*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = new \Carbon\Carbon($postedTime[1]);
            $datetime = $datetime->addHours($postedTime[2]);
            $datetime = $datetime->addMinutes($postedTime[3]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/((\d+)\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)).*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = new \Carbon\Carbon($postedTime[1]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        elseif(preg_match('/(\d+)\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s*(\d+).*?Public/', $profileAvatarResponseString, $postedTime)){
            $datetime = new \Carbon\Carbon($postedTime[1]);
            $player->changed_avatar_at = $datetime;
            $player->save();
        }
        else{
            $now = time();
            \Illuminate\Support\Facades\Storage::put($now.'.txt', $profileAvatarResponseString);
            Log::warning("Unknow datetime format for parse. View reponse in {$now}.log. Player: ".$player->fburl);
        }

        sleep(10);
        return true;
    }

    public function calcPointByTag($player)
    {
        $return = [
            // "point" => 0
        ];

        $personsAlreadyCalculated = [$player->id];

        //Calc F1
        $f1 = $player->tags;
        $fDetails = [
            "players" => [],
            "point" => 0
        ];

        foreach ($f1 as $tag ) {
            $person = $tag->toPerson;
            $playerDetail = [
                "name" => $person->name,
                'comment' => $tag->where,
                'taggedBy' =>$tag->fromPerson->name,
            ]; 

            if(in_array($person->id, $personsAlreadyCalculated)){
                $playerDetail['status'] = "repeated";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            array_push($personsAlreadyCalculated, $person->id);

            if(!$tag->eligible){
                $playerDetail['status'] = "ineligible-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$tag->is_first_tagged){
                $playerDetail['status'] = "not-first-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->eligible){
                $playerDetail['status'] = "ineligible-person";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_like_post){
                $playerDetail['status'] = "unlike-post";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_has_hashtag){
                $playerDetail['status'] = "not-have-hastag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            $fDetails["point"] += $person->point;
            $playerDetail['like'] = $person->like;
            $playerDetail['love'] = $person->love;
            $playerDetail['haha'] = $person->haha;
            $playerDetail['wow'] = $person->wow;
            $playerDetail['angry'] = $person->angry;
            $playerDetail['sad'] = $person->sad;
            $playerDetail['point'] = $person->point;
            $playerDetail['status'] = "success";

            array_push($fDetails["players"], $playerDetail);
        }
        $return['f1'] = $fDetails;


        //Calc F2
        $f2 = Tag::whereIn("from", $f1->pluck("to"))->get();
        $fDetails = [
            "players" => [],
            "point" => 0
        ];

        foreach ($f2 as $tag ) {
            $person = $tag->toPerson;
            $playerDetail = [
                "name" => $person->name,
                'comment' => $tag->where,
                'taggedBy' =>$tag->fromPerson->name,
            ]; 

            if(in_array($person->id, $personsAlreadyCalculated)){
                $playerDetail['status'] = "repeated";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            array_push($personsAlreadyCalculated, $person->id);

            if(!$tag->eligible){
                $playerDetail['status'] = "ineligible-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$tag->is_first_tagged){
                $playerDetail['status'] = "not-first-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->eligible){
                $playerDetail['status'] = "ineligible-person";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_like_post){
                $playerDetail['status'] = "unlike-post";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_has_hashtag){
                $playerDetail['status'] = "not-have-hastag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }


            $fDetails["point"] += $person->point;
            $playerDetail['like'] = $person->like;
            $playerDetail['love'] = $person->love;
            $playerDetail['haha'] = $person->haha;
            $playerDetail['wow'] = $person->wow;
            $playerDetail['angry'] = $person->angry;
            $playerDetail['sad'] = $person->sad;
            $playerDetail['point'] = $person->point;
            $playerDetail['status'] = "success";
            array_push($fDetails["players"], $playerDetail);
        }
        $return['f2'] = $fDetails;


        //Calc F3
        $f3 = Tag::whereIn("from", $f2->pluck("to"))->get();
        $fDetails = [
            "players" => [],
            "point" => 0
        ];

        foreach ($f3 as $tag ) {
            $person = $tag->toPerson;
            $playerDetail = [
                "name" => $person->name,
                'comment' => $tag->where,
                'taggedBy' =>$tag->fromPerson->name,            ]; 

            if(in_array($person->id, $personsAlreadyCalculated)){
                $playerDetail['status'] = "repeated";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            array_push($personsAlreadyCalculated, $person->id);

            if(!$tag->eligible){
                $playerDetail['status'] = "ineligible-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$tag->is_first_tagged){
                $playerDetail['status'] = "not-first-tag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->eligible){
                $playerDetail['status'] = "ineligible-person";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_like_post){
                $playerDetail['status'] = "unlike-post";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }

            if(!$person->is_has_hashtag){
                $playerDetail['status'] = "not-have-hastag";
                array_push($fDetails["players"], $playerDetail);
                continue;
            }


            $fDetails["point"] += $person->point;
            $playerDetail['like'] = $person->like;
            $playerDetail['love'] = $person->love;
            $playerDetail['haha'] = $person->haha;
            $playerDetail['wow'] = $person->wow;
            $playerDetail['angry'] = $person->angry;
            $playerDetail['sad'] = $person->sad;
            $playerDetail['point'] = $person->point;
            $playerDetail['status'] = "success";
            array_push($fDetails["players"], $playerDetail);
        }
        $return['f3'] = $fDetails;
        return $return;
    }

    public function runCalcExtraPoint()
    {
        $players = Player::all();
        echo "Has ".$players->count()." players\n";
        foreach ($players as $player) {
            if($player->skip || !$player->eligible){
                continue;
            }
            if(!$player->changed_avatar_at){
                continue;
            }
            echo "Processing " . $player->fburl . "   " . $player->name . "\n";
            $result = $this->calcPointByTag($player);

            $extraPoint = $result['f1']['point']*0.2 + $result['f2']['point']*0.1 + $result['f3']['point']*0.05;
            Player::where('id', $player->id)->update(['extra_point'=>$extraPoint, 'detail'=>$result]);
        }
    }

    public function runCalcTotalPoint()
    {
        $players = Player::all();
        echo "Has ".$players->count()." players\n";
        foreach ($players as $player) {
            if($player->skip || !$player->eligible){
                continue;
            }
            echo "Processing " . $player->fburl . "   " . $player->name . "\n";
            $player->total_point = $player->point + $player->extra_point;
            $player->save();
        }
    }

    public function calcRanking()
    {
        $players = Player::whereSkip(false)
        ->where("is_like_post", true)
        ->where('is_has_hashtag', true)
        ->orderBy("total_point", 'desc')->get();

        for ($i=0; $i < $players->count(); $i++) { 
            $players[$i]->ranking = $players[$i]->total_point ?  $i + 1 : null;
            $players[$i]->save();
        }
    }

    public function login()
    {
        //Choose random fb account to login
        if(!$this->account){
            $this->account = explode("|", config("app.facebook_email"));
            $this->account = $this->account[array_rand($this->account)];
            echo "   > Using account: ".$this->account ."\n";
        }

        //Obtain token
        $response = $this->request("GET",'https://m.facebook.com/login/?ref=dbl&fl');
        //Check if already logged in
        if($response->getStatusCode() == 302){
            $location = $response->getHeader('location')[0];
            if(strpos($location, "home.php") > 0){
                echo "Already login. Use last session\n";
                return true;
            }
        }

        //Remove old cookie
        // cache()->forget("facebook." . $this->account);;
        // unset($this->client);
        // $this->client = null;

        $response = $this->request("GET",'https://m.facebook.com/login/?ref=dbl&fl', ['allow_redirect'=>true]);
        $bodyText = $response->getBody()->getContents();

        if(strpos($bodyText, "mbasic_logout_button")){
            echo "Already login. Use last session\n";
            return true;
        }

        $loginPayload = [];

        try{

            preg_match_all('/<input\s*type="hidden"\s*name="([^"]+)" value="([^"]+)"/', $bodyText, $formInput);
            
            
            for($i = 0; $i < count($formInput[0]); $i++){
                $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
            }

        }catch(\Exception $e){
            Log::error("Can not find necessary input to login ".$this->account);
            echo "Can not find necessary input to login ".$this->account;
            exit();
        }

        $loginPayload["email"] = $this->account;
        $loginPayload["pass" ] = config("app.facebook_password");
        $loginPayload["login"] = "Đăng nhập";

        $response = $this->request("POST",'https://m.facebook.com/login/device-based/regular/login/?refsrc=https%3A%2F%2Fm.facebook.com%2F&lwv=100&refid=8',[
            "form_params" => $loginPayload,
            "allow_redirect" => true
            ], false);

        
        $bodyText = $response->getBody()->getContents();        
        

        if(strpos($bodyText, "checkpointSubmitButton-actual-button") == true){
            echo "Extra verify required\n";

            //Try trouble
            $troubleResponse = $this->request("GET",'https://m.facebook.com/checkpoint/?having_trouble=1', ['allow_redirect'=>true]);
            $troubleResponseText = $troubleResponse->getBody()->getContents();   
            if(strpos($troubleResponseText, "approve_from_another_device") == true){
                $bodyText = $troubleResponseText;
                echo "Using approve from other device method\n";
                $loginPayload = [];
                preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                    }
                }

                preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                    }
                }
                $loginPayload['help_selected'] = "approve_from_another_device";
                $loginPayload['submit[Continue]'] = "Continue";
                $response = $this->request("POST",'https://m.facebook.com/login/checkpoint/',[
                    "form_params" => $loginPayload,
                    "allow_redirect" => true
                    ], false);
                $bodyText = $response->getBody()->getContents();

                if(strpos($bodyText, "submit[Approved]") == true){
                    echo "You have 60s to approve from other devices\n";
                    sleep(60);
                    $loginPayload = [];
                    preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                    if($formInput){
                        for($i = 0; $i < count($formInput[0]); $i++){
                            $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                        }
                    }

                    preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                    if($formInput){
                        for($i = 0; $i < count($formInput[0]); $i++){
                            $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                        }
                    }
                    $loginPayload['submit[Approved]'] = "Approved";
                    unset($loginPayload['submit[Back]']);
                    $response = $this->request("POST",'https://m.facebook.com/login/checkpoint/',[
                        "form_params" => $loginPayload,
                        "allow_redirect" => true
                        ], false);
                    $bodyText = $response->getBody()->getContents();
                }           
            }else{
            

                $loginPayload = [];
                //Check point
                preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                    }
                }

                preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                    }
                }

                $response = $this->request("POST",'https://m.facebook.com//login/checkpoint/',[
                    "form_params" => $loginPayload,
                    "allow_redirect" => true
                    ], false);
                $bodyText = $response->getBody()->getContents();   
                echo "Checkpoint passed\n";

                //Step 2
                $loginPayload = [];
                preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                    }
                }

                preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                    }
                }

                preg_match('/option value="([^"]+)">(.*?)<\/option>/', $bodyText, $verifyOption);
                if($verifyOption){
                    $loginPayload['verification_method'] = $verifyOption[1];
                    echo "Please follow step: ".$verifyOption[2]."\n";
                    Log::error("Login verification is required. Please ".$verifyOption[2]." with account ".$this->account."\nRecheck after 60 seconds\n");
                }

                $response = $this->request("POST",'https://m.facebook.com//login/checkpoint/',[
                    "form_params" => $loginPayload,
                    "allow_redirect" => true
                    ], false);
                $bodyText = $response->getBody()->getContents(); 
                echo "Waiting for 60s\n";
                sleep(60);

                //Step 3
                $loginPayload = [];
                preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                    }
                }

                preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                    }
                }
                $response = $this->request("POST",'https://m.facebook.com//login/checkpoint/',[
                    "form_params" => $loginPayload,
                    "allow_redirect" => true
                    ], false);
                $bodyText = $response->getBody()->getContents();


                //Submit verification
                $loginPayload = [];
                preg_match_all('/<input.*?name="([^"]+)" value="([^"]*)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[1][$i]] = $formInput[2][$i];
                    }
                }

                preg_match_all('/<input.*?value="([^"]*)".*?name="([^"]+)"/', $bodyText, $formInput);
                if($formInput){
                    for($i = 0; $i < count($formInput[0]); $i++){
                        $loginPayload[$formInput[2][$i]] = $formInput[1][$i];
                    }
                }
                $response = $this->request("POST",'https://m.facebook.com/login/checkpoint/',[
                    "form_params" => $loginPayload,
                    "allow_redirect" => true
                    ], false);
                $bodyText = $response->getBody()->getContents();
                }
        }
        
        if(strpos($bodyText, "sign_up") === false){
            //Save cookie
            $cookie = $this->jar->toArray();
            Cache::forever("facebook.". $this->account, $cookie);

            echo "Login sucessfully\n";
            return true;
        }

        Log::error("Cannot login to facebook with account ".$this->account);
        echo "Cannot login to facebook with account ".$this->account;
        exit();
        
    }

    public function initClient()
    {
        //Get saved cookie
        $cookie = Cache::get("facebook." . $this->account);

        if($cookie){
            $this->jar = new \GuzzleHttp\Cookie\CookieJar(false, $cookie);
        }else{
            $this->jar = new \GuzzleHttp\Cookie\CookieJar();
        }
        $this->client = new \GuzzleHttp\Client();

    }

    public function request($method, $url, $option = [], $check = true)
    {
        if(!array_key_exists('headers', $option)){
            $option['headers'] = [];
        }

        if (!array_key_exists('User-Agent', $option['headers'])){
            $option['headers']['User-Agent'] = 'Nokia3110c/2.0 (07.01) Profile/MIDP-2.0 Configuration/CLDC-1.1 UCWEB/2.0 (Java; U; MIDP-2.0; en-US; nokia3110c) U2/1.0.0 UCBrowser/8.9.0.251 U2/1.0.0 Mobile UNTRUSTED/1.0';
        }
        
        //    $option['headers']['User-Agent'] = 'Mozilla/5.0 (Linux; Android 7.0; Micromax Q371 Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.100 Mobile Safari/537.36';
  
        if(!$this->client){
            $this->initClient();
        }

        $option['cookies'] = $this->jar;

        if(!in_array('allow_redirects', $option)){
            $option['allow_redirects'] = false;
        }

        try{
            $response = $this->client->request($method, $url, $option);
            
            if($response->getStatusCode() == 302){
                $location = $response->getHeader('location')[0];
                if(strpos($location, "login.php") > 0){
                    $this->login();
                    return $this->request($method, $url, $option, $check);
                }
            }

        }catch(\GuzzleHttp\Exception\ClientException $e){
            if(strpos($e->getMessage(), 'login_error')){
                $this->login();
                return $this->request($method, $url, $option, $check);
            }
            
            throw $e;
        }catch(\Exception $e){
            throw $e;
        }
        
        
        return $response;
    }

    
}
