<?php

/**
 * @author Faizan Ayubi
 */
class ImageItem extends Shared\Model {

    /**
     * @column
     * @readwrite
     * @type integer
     * @index
     */
    protected $_image_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @index
     */
    protected $_meta_key;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @index
     */
    protected $_meta_value;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     */
    protected $_image;
}