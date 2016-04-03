<?php
/**
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;
use \Curl\Curl;

class Game extends Config {
	
    public function view($title, $id) {
        $campaign = Campaign::first(array("id = ?" => $id));
        $this->seo(array("title" => $campaign->title, "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if ($this->user) {
            $participant = Participant::first(array("campaign_id = ?" => $id, "user_id = ?" => $this->user->id));
        } else {
            $participant = null;
        }
        
        $view->set("campaign", $campaign)
            ->set("participant", $participant);
    }

    public function result($participant_id) {
        $participant = Participant::first(array("id = ?" => $participant_id));
        $user = User::first(array("id = ?" => $participant->user_id), array("name"));
        $campaign = Campaign::first(array("id = ?" => $participant->campaign_id));
        $image = CDN . "uploads/images/" .$participant->image;
        $this->seo(array(
            "title" => $campaign->title, 
            "description" => $campaign->description,
            "photo" => $image,
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $facebook = new Curl();
        /*$facebook->post('https://graph.facebook.com/me/staging_resources', array(
            'access_token' => '1687202661533796|8fea8d2c69a57f8e91fd48e6bd1131ba',
            'object' => '{"url":"http://fbgameapp.com/game/result/'.$participant->id.'","title":"'.$campaign->title.'","image":"'.$image.'"}'
        ));*/
        
        /*$facebook->post('https://graph.facebook.com/app/objects/website', array(
            'access_token' => '1687202661533796|8fea8d2c69a57f8e91fd48e6bd1131ba',
            'object' => '{"url":"http://fbgameapp.com/game/result/'.$participant->id.'","title":"'.$campaign->title.'","image":"'.$image.'"}'
        ));*/
        $facebook->close();

        $items = Participant::all(array(), array("DISTINCT campaign_id"), "created", "desc", 3, 1);
        $view->set("items", $items);

        $view->set("campaign", $campaign);
        $view->set("participant", $participant);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function all() {
        $this->seo(array("title" => "All Games", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $limit = RequestMethods::get("limit", 10);
        $page = RequestMethods::get("page", 1);

        $campaign = Campaign::all(array(), array("*"), "created", "desc", $limit, $page);
        $count = Campaign::count();

        $view->set("campaigns", $campaign);
        $view->set("count", $count);
        $view->set("limit", $limit);
        $view->set("page", $page);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function participants() {
        $this->seo(array("title" => "Game Participants", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $limit = RequestMethods::get("limit", 10);
        $page = RequestMethods::get("page", 1);

        $participants = Participant::all(array(), array("*"), "created", "desc", $limit, $page);
        $count = Participant::count();

        $view->set("participants", $participants);
        $view->set("count", $count);
        $view->set("limit", $limit);
        $view->set("page", $page);
    }

    /**
     * @before _secure
     */
	public function authorize($id, $token) {
        $campaign = Campaign::first(array("id = ?" => $id, "live = ?" => true));

        $session = Registry::get("session");
        if ($token !== $session->get('CampaignAccessToken') || !$campaign) {
            $this->redirect("/index.html");
        }
        $session->erase('CampaignAccessToken');

        $session->set('Game\Authorize:$campaign', $campaign);
        $this->redirect("/game/play");
	}

    /**
     * @before _secure
     */
    public function play() {
        $this->seo(array("title" => "Play Game", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $session = Registry::get("session");

        $campaign = $session->get('Game\Authorize:$campaign');
        if (!$campaign) {
            $this->redirect("/index.html");
        }
        $session->erase('Game\Authorize:$campaign');

        $model = $campaign->type;
        $game = $model::first(array("id = ?" => $campaign->type_id));
        switch ($model) {
            case 'imagetext':
                $img = $this->_imagetextprocess($game, $campaign);
                break;
            
            case 'image':
                $img = $this->_imageprocess($game, $campaign);
                break;

            case 'text':
                $img = $this->_textprocess($game, $campaign);
                break;

            case 'shuffle':
                $img = $this->_shuffleprocess($game, $campaign);
                break;
        }

        $participant = Participant::first(array("user_id = ?" => $this->user->id, "campaign_id = ?" => $campaign->id));
        $items = Participant::all(array(), array("DISTINCT campaign_id"), "created", "desc", 3, 1);

        $title = $this->user->name." ".$campaign->title;
        $image = CDN . "uploads/images/" .$participant->image;
        $facebook = new Curl();
        $facebook->post('https://graph.facebook.com/app/objects/website', array(
            'access_token' => '1687202661533796|8fea8d2c69a57f8e91fd48e6bd1131ba',
            'object' => '{"url":"http://fbgameapp.com/game/result/'.$participant->id.'","title":"'.$title.'","image":"'.$image.'"}'
        ));
        $facebook->close();

        //echo "<pre>", print_r($facebook), "</pre>";

        $view->set("items", $items);
        $view->set("img", $img);
        $view->set("participant", $participant);
        $view->set("campaign", $campaign);
    }
}
