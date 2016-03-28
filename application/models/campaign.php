<?php

/**
 * @author Hemant Mann
 */
class Campaign extends Shared\Model {
	/**
     * @column
     * @readwrite
     * @type text
     * @length 50
     * @index
     * @validate required, max(50)
     */
    protected $_type;

    /**
     * @column
     * @readwrite
     * @type integer
     * @index
     * @validate required, numeric
     */
    protected $_type_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @validate required, max(100)
     */
    protected $_title;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @validate required, max(100)
     */
    protected $_image = "";

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_description;
}