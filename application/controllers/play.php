<?php
/**
 * Description of game
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;
use \Curl\Curl;

class Play extends Admin {
    protected function _makeColor(&$dest, $hexCode) {
        $text_color = Shared\Image::rgbFromHex($hexCode);
        $tt_color = imagecolorallocate($dest, $text_color['r'], $text_color['g'], $text_color['b']); // Create color from hex code

        return $tt_color;
    }

    protected function _setup($path, $game, $participant) {
        $user = $this->user;
        
        $user_img = "{$path}user-".$user->fbid.".jpg";
        if (!file_exists($user_img)) {
            if (!copy('http://graph.facebook.com/'.$user->fbid.'/picture?width='.$game->usr_w.'&height='.$game->usr_h, $user_img)) {
                die('Could not copy image');
            }
        }
        $user_img = Shared\Image::resize($user_img, $game->usr_w, $game->usr_h);

        $src_file = $path . $game->base_im;
        $extension = pathinfo($src_file, PATHINFO_EXTENSION);
        if ($participant) {
            $filename = $participant->image;
        } else {
            $filename = Shared\Markup::uniqueString() . ".$extension";
        }
        $final_img = $path . $filename;
        copy($src_file, $final_img);

        return array(
            'dest' => Shared\Image::resource($final_img),
            'usr' => Shared\Image::resource($user_img),
            'file' => $final_img,
            'filename' => $filename
        );
    }

    /**
     * @param object $game
     * @param object $campaign object of class \Campaign
     * @param boolean $play_agin If user wishes to play the game again
     *
     * @return string String containing the filename of the resultant image
     */
    protected function _imagetextprocess($game, $campaign, $play_again = true) {
        $participant = Participant::first(array("user_id = ?" => $this->user->id, "campaign_id = ?" => $campaign->id));
        if ($participant && !$play_again) {
            return $participant->image;
        }

        $path = APP_PATH.'/public/assets/uploads/images/';
        $vars = $this->_setup($path, $game, $participant);
        $dest = $vars['dest'];

        $items = ImageTextItem::all(array("imagetext_id = ?" => $game->id, "meta_key = ?" => "gender", "meta_value = ?" => strtolower($this->user->gender)));
        $key = rand(0, count($items) - 1);
        $item = $items[$key];
        
        imagecopymerge($dest, $vars['usr'], $game->usr_x, $game->usr_y, 0, 0, $game->usr_w, $game->usr_h, 100);
        
        $item_img = Shared\Image::resize($path . $item->image, $game->src_w, $game->src_h);
        $item_res = Shared\Image::resource($item_img);

        $font = APP_PATH.'/public/assets/fonts/monaco.ttf';
        $tt_color = $this->_makeColor($dest, $game->txt_color);
        imagettftext($dest, $game->txt_size, 0, $game->txt_x, $game->txt_y, $tt_color, $font, $item->text);
        $tt_color = $this->_makeColor($dest, $game->utxt_color);
        imagettftext($dest, $game->utxt_size, 0, $game->utxt_x, $game->utxt_y, $tt_color, $font, $this->user->name);
        
        imagecopymerge($dest, $item_res, $game->src_x, $game->src_y, 0, 0, $game->src_w, $game->src_h, 100);

        unlink($vars['file']);
        Shared\Image::create($dest, $vars['file']);

        $this->_saveParticipant($participant, $campaign, $vars);
        return $participant->image;
    }

    protected function _imageprocess($game, $campaign, $play_again = true) {
        $participant = Participant::first(array("user_id = ?" => $this->user->id, "campaign_id = ?" => $campaign->id));
        if ($participant && !$play_again) {
            return $participant->image;
        }

        $path = APP_PATH.'/public/assets/uploads/images/';
        $vars = $this->_setup($path, $game, $participant);
        $dest = $vars['dest'];

        $items = ImageItem::all(array("image_id = ?" => $game->id));
        $key = rand(0, count($items) - 1);
        $item = $items[$key];
        
        imagecopymerge($dest, $vars['usr'], $game->usr_x, $game->usr_y, 0, 0, $game->usr_w, $game->usr_h, 100);
        
        $item_img = Shared\Image::resize($path . $item->image, $game->src_w, $game->src_h);
        $item_res = Shared\Image::resource($item_img);

        $font = APP_PATH.'/public/assets/fonts/monaco.ttf';
        $tt_color = $this->_makeColor($dest, $game->utxt_color);
        imagettftext($dest, $game->utxt_size, 0, $game->utxt_x, $game->utxt_y, $tt_color, $font, $this->user->name);
        
        imagecopymerge($dest, $item_res, $game->src_x, $game->src_y, 0, 0, $game->src_w, $game->src_h, 100);

        unlink($vars['file']);
        Shared\Image::create($dest, $vars['file']);

        $this->_saveParticipant($participant, $campaign, $vars);
        return $participant->image;
    }

    protected function _textprocess($game, $campaign, $play_again = true) {
        $participant = Participant::first(array("user_id = ?" => $this->user->id, "campaign_id = ?" => $campaign->id));
        if ($participant && !$play_again) {
            return $participant->image;
        }

        $path = APP_PATH.'/public/assets/uploads/images/';
        $vars = $this->_setup($path, $game, $participant);
        $dest = $vars['dest'];

        $items = TextItem::all(array("text_id = ?" => $game->id));
        $key = rand(0, count($items) - 1);
        $item = $items[$key];
        
        imagecopymerge($dest, $vars['usr'], $game->usr_x, $game->usr_y, 0, 0, $game->usr_w, $game->usr_h, 100);

        // replace $font with font path
        $font = APP_PATH.'/public/assets/fonts/monaco.ttf';
        $tt_color = $this->_makeColor($dest, $game->txt_color);
        imagettftext($dest, $game->txt_size, $game->txt_angle, $game->txt_x, $game->txt_y, $tt_color, $font, $item->text);
        $tt_color = $this->_makeColor($dest, $game->utxt_color);
        imagettftext($dest, $game->utxt_size, 0, $game->utxt_x, $game->utxt_y, $tt_color, $font, $this->user->name);

        unlink($vars['file']);
        Shared\Image::create($dest, $vars['file']);

        $this->_saveParticipant($participant, $campaign, $vars);
        return $participant->image;
    }

    protected function _shuffleprocess($game, $campaign, $play_again = true) {
        $participant = Participant::first(array("user_id = ?" => $this->user->id, "campaign_id = ?" => $campaign->id));
        if ($participant && !$play_again) {
            return $participant->image;
        }

        $items = ShuffleItem::all(array("shuffle_id = ?" => $game->id, "meta_key = ?" => "gender", "meta_value = ?" => strtolower($this->user->gender)));
        $key = rand(0, count($items) - 1);
        $item = $items[$key];

        $vars = array(); $user = $this->user; $path = APP_PATH.'/public/assets/uploads/images/';
        $user_img = "{$path}user-".$user->fbid.".jpg";
        if (!file_exists($user_img)) {
            if (!copy('http://graph.facebook.com/'.$user->fbid.'/picture?width='.$item->usr_w.'&height='.$item->usr_h, $user_img)) {
                die('Could not copy image');
            }
        }
        
        $src_file = $path . $item->base_im;
        $extension = pathinfo($src_file, PATHINFO_EXTENSION);
        if ($participant) {
            $filename = $participant->image;
        } else {
            $filename = Shared\Markup::uniqueString() . ".$extension";
        }

        $final_img = $path . $filename;
        copy($src_file, $final_img);

        $dest = Shared\Image::resource($final_img);
        $vars['file'] = $final_img; $vars['filename'] = $filename;
        $img = Shared\Image::resize($user_img, $item->usr_w, $item->usr_h);
        $vars['usr'] = Shared\Image::resource($img);
        
        imagecopymerge($dest, $vars['usr'], $item->usr_x, $item->usr_y, 0, 0, $item->usr_w, $item->usr_h, 100);

        // add text
        $tt_color = $this->_makeColor($dest, $item->txt_color);
        $font = APP_PATH.'/public/assets/fonts/monaco.ttf';
        imagettftext($dest, $item->txt_size, 0, $item->txt_x, $item->txt_y, $tt_color, $font, $user->name);
        Shared\Image::create($dest, $vars['file']);

        $this->_saveParticipant($participant, $campaign, $vars);
        return $participant->image;
    }

    private function _saveParticipant($participant, $campaign, $vars) {
        if (!$participant) {
            $participant = new Participant(array(
                "user_id" => $this->user->id,
                "campaign_id" => $campaign->id,
                "live" => true
            ));
        }
        $participant->image = $vars['filename'];
        $participant->save();

        $p = Registry::get("MongoDB")->participants;
        $record = $p->findOne(array('participant_id' => (int) $participant->id, 'campaign_id' => (int) $campaign->id));
        if (!$record) {
            $p->insert(array(
                'participant_id' => (int) $participant->id,
                'campaign_id' => (int) $campaign->id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'image' => $vars['filename'],
                'url' => 'game/result/'.$participant->id
            ));
        }
    }
}
