<?php
/**
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;
use \Curl\Curl;

class Config extends Play {

	/**
     * @before _secure, changeLayout, _admin
     */
	public function imagetext() {
		$this->seo(array("title" => "ImageText Game", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $fields = array("src_x", "src_y", "src_h", "src_w", "usr_x", "usr_y", "txt_x", "txt_y", "usr_w", "usr_h", "txt_size", "txt_angle", "txt_color", "utxt_x", "utxt_y", "utxt_size", "utxt_angle", "utxt_color");
        
        if (RequestMethods::post("action") == "campaign") {
            $imagetext = new \ImageText(array(
                "base_im" => $this->_upload("base_im")
            ));
            foreach ($fields as $key => $value) {
                $imagetext->$value = RequestMethods::post($value, "0");
            }
            $imagetext->live = true;
            $imagetext->save();

            $campaign = new \Campaign(array(
                "title" => RequestMethods::post("title"),
                "description" => RequestMethods::post("description"),
                "image" => $this->_upload("promo_im"),
                "type" => "imagetext",
                "type_id" => $imagetext->id
            ));
            $campaign->save();

            $this->redirect("/config/imagetextitem/".$imagetext->id);
        }
	}

    /**
     * @before _secure, changeLayout, _admin
     */
    public function imagetextitem($imagetext_id) {
        $this->seo(array("title" => "Looklike Content", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $imagetext = ImageText::first(array("id = ?" => $imagetext_id));

        if (RequestMethods::post("action") == "shuffle") {
            $imagetextitem = new ImageTextItem(array(
                "imagetext_id" => $imagetext->id,
                "meta_key" => "gender",
                "meta_value" => RequestMethods::post("gender"),
                "image" => $this->_upload("image"),
                "live" => true,
                "text" => RequestMethods::post("text")
            ));
            $imagetextitem->save();
            $view->set("success", true);
        }
        $imagetextitems = ImageTextItem::all(array("imagetext_id = ?" => $imagetext->id));

        $view->set("imagetext", $imagetext);
        $view->set("imagetextitems", $imagetextitems);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function text() {
        $this->seo(array("title" => "Text Game", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $fields = array("usr_x", "usr_y", "txt_x", "txt_y", "usr_w", "usr_h", "txt_size", "txt_angle", "txt_color", "utxt_x", "utxt_y", "utxt_size", "utxt_angle", "utxt_color");
        
        if (RequestMethods::post("action") == "campaign") {
            $text = new \Text(array(
                "base_im" => $this->_upload("base_im")
            ));
            foreach ($fields as $key => $value) {
                $text->$value = RequestMethods::post($value, "0");
            }
            $text->live = true;
            $text->save();

            $campaign = new \Campaign(array(
                "title" => RequestMethods::post("title"),
                "description" => RequestMethods::post("description"),
                "image" => $this->_upload("promo_im"),
                "type" => "text",
                "type_id" => $text->id
            ));
            $campaign->save();

            $this->redirect("/config/textitem/".$text->id);
        }
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function textitem($text_id) {
        $this->seo(array("title" => "ImageText Content", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $text = Text::first(array("id = ?" => $text_id));

        if (RequestMethods::post("action") == "shuffle") {
            $item = new TextItem(array(
                "text_id" => $text->id,
                "meta_key" => "",
                "meta_value" => "",
                "live" => true,
                "text" => RequestMethods::post("text")
            ));
            $item->save();
        }
        $items = TextItem::all(array("text_id = ?" => $text->id));

        $view->set("text", $text);
        $view->set("items", $items);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function image() {
        $this->seo(array("title" => "Image Game", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $fields = array("src_x", "src_y", "src_h", "src_w", "usr_x", "usr_y", "usr_w", "usr_h", "utxt_x", "utxt_y", "utxt_size", "utxt_angle", "utxt_color");
        
        if (RequestMethods::post("action") == "campaign") {
            $image = new \Image(array(
                "base_im" => $this->_upload("base_im")
            ));
            foreach ($fields as $key => $value) {
                $image->$value = RequestMethods::post($value, "0");
            }
            $image->live = true;
            $image->save();

            $campaign = new \Campaign(array(
                "title" => RequestMethods::post("title"),
                "description" => RequestMethods::post("description"),
                "image" => $this->_upload("promo_im"),
                "type" => "image",
                "type_id" => $image->id
            ));
            $campaign->save();

            $this->redirect("/config/imageitem/".$image->id);
        }
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function imageitem($image_id) {
        $this->seo(array("title" => "Image Content", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $image = Image::first(array("id = ?" => $image_id));

        if (RequestMethods::post("action") == "shuffle") {
            $item = new ImageItem(array(
                "image_id" => $image->id,
                "meta_key" => RequestMethods::post("meta_key", ""),
                "meta_value" => RequestMethods::post("meta_value", ""),
                "image" => $this->_upload("image"),
                "live" => true
            ));
            $item->save();
        }
        $items = ImageItem::all(array("image_id = ?" => $image->id));

        $view->set("image", $image);
        $view->set("items", $items);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function shuffle() {
        $this->seo(array("title" => "Shuffle Game", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        if (RequestMethods::post("action") == "campaign") {
            $shuffle = new \Shuffle();
            $shuffle->live = true;
            $shuffle->save();

            $campaign = new \Campaign(array(
                "title" => RequestMethods::post("title"),
                "description" => RequestMethods::post("description", ""),
                "image" => $this->_upload("promo_im"),
                "type" => "shuffle",
                "type_id" => $shuffle->id
            ));
            $campaign->save();

            $this->redirect("/config/shuffleitem/".$shuffle->id);
        }
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function shuffleitem($shuffle_id) {
        $this->seo(array("title" => "Shuffle Content", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $fields = array("usr_x", "usr_y", "usr_w", "usr_h", "txt_x", "txt_y", "txt_size", "txt_angle", "txt_color");

        $shuffle = Shuffle::first(array("id = ?" => $shuffle_id));

        if (RequestMethods::post("action") == "shuffle") {
            $item = new ShuffleItem(array(
                "shuffle_id" => $shuffle->id,
                "meta_key" => "gender",
                "meta_value" => RequestMethods::post("gender"),
                "base_im" => $this->_upload("base_im"),
                "live" => true
            ));
            foreach ($fields as $key => $value) {
                $item->$value = RequestMethods::post($value, "0");
            }
            $item->save();
        }
        $items = ShuffleItem::all(array("shuffle_id = ?" => $shuffle->id));

        $view->set("shuffle", $shuffle);
        $view->set("items", $items);
    }

    /**
     * @before _secure, changeLayout, _admin
     */
    public function publish($id, $status) {
        $this->edit('Campaign', $id, 'live', $status);
    }

}