<?php

/**
 * @author Faizan Ayubi
 */
class Participant extends Shared\Model {

	/**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;

	/**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_campaign_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     */
    protected $_image;

}