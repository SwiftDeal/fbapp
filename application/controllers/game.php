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
        $info = getimagesize(APP_PATH . "/public/assets/uploads/images/" . $participant->image);

        $this->seo(array(
            "title" => $campaign->title, 
            "description" => $campaign->description,
            "photo" => $image,
            "view" => $this->getLayoutView()
        ));
        $this->layoutView->set("width", $info[0])
                        ->set("height", $info[1]);
        $view = $this->getActionView(); $session = Registry::get("session");

        $items = Participant::all(array(), array("DISTINCT campaign_id"), "created", "desc", 3, 1);
        $view->set("items", $items);

        $view->set("campaign", $campaign)
            ->set("participant", $participant);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function all() {
        $this->seo(array("title" => "All Games", "view" => $this->getLayoutView()));
        $view = $this->getActionView(); $session = Registry::get("session");

        $limit = RequestMethods::get("limit", 10);
        $page = RequestMethods::get("page", 1);

        $campaign = Campaign::all(array(), array("*"), "created", "desc", $limit, $page);
        $count = Campaign::count();

        $msg = $session->get('Game\delete:$msg');
        if ($msg) {
            $session->erase('Game\delete:$msg');
            $view->set("message", $msg);
        }

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
        $facebook->post('https://graph.facebook.com/?id='. "http://". $_SERVER["HTTP_HOST"] ."/game/result/".$participant->id. '&scrape=true');
        $facebook->close();

        $view->set("items", $items);
        $view->set("img", $img);
        $view->set("participant", $participant);
        $view->set("campaign", $campaign);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function delete($game_id) {
        $this->noview(); $session = Registry::get("session");
        $campaign = Campaign::first(["id = ?" => $game_id]);
        if (!$campaign || $campaign->live) {
            $session->set('Game\delete:$msg', 'Campaign not found!! or Game is published');
            $this->redirect(RequestMethods::server("HTTP_REFERER", "/admin"));
        }
        $participant = Participant::first(["campaign_id = ?" => $campaign->id]);
        if ($participant) {
            $session->set('Game\delete:$msg', 'Participant exists! Failed to delete');
            $this->redirect(RequestMethods::server("HTTP_REFERER", "/admin"));
        }

        $model = $campaign->type; $game_item = $model . "item";
        $game = $model::first(["id = ?" => $campaign->type_id]);
        if (!$game) {
            $this->redirect("/404");
        }
        $game_item = $game_item::first([$model."_id = ?" => $game->id]);
        if ($game_item) {
            $game_item->delete();
        }

        $game->delete();
        $campaign->delete();
        $session->set('Game\delete:$msg', 'Game deleted');
        $this->redirect(RequestMethods::server("HTTP_REFERER", "/admin"));
    }
}
