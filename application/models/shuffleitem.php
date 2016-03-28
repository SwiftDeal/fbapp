<?php

/**
 * @author Faizan Ayubi
 */
class ShuffleItem extends Shared\Model {

    /**
     * @column
     * @readwrite
     * @type integer
     * @index
     */
    protected $_shuffle_id;

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
     * @label Base image link resource
     */
    protected $_base_im;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label x-coordinate of source point
     * @validate required, max(10)
     */
    protected $_usr_x;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label y-coordinate of source point
     * @validate required, max(10)
     */
    protected $_usr_y;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label Source width
     * @validate required, max(10)
     */
    protected $_usr_w;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label Source height
     * @validate required, max(10)
     */
    protected $_usr_h;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label x-coordinate of text
     * @validate required, max(10)
     */
    protected $_txt_x;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     * @label y-coordinate of text
     * @validate required, max(10)
     */
    protected $_txt_y;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @label The font size. Depending on your version of GD, this should be specified as the pixel size (GD1) or point size (GD2)
     */
    protected $_txt_size;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @label The angle in degrees, with 0 degrees being left-to-right reading text. Higher values represent a counter-clockwise rotation. For example, a value of 90 would result in bottom-to-top reading text
     */
    protected $_txt_angle;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @label The color index
     */
    protected $_txt_color;
}
