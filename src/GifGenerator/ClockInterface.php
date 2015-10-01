<?php

namespace GifGenerator;

/**
 *
 * @author tp
 */
interface ClockInterface {

    /**
     * @return string clock name
     */
    public function getName();

    /**
     * @return /DateTime deadline date and time
     */
    public function getdeadlineDateTime();

    /**
     * @return string time zone
     */
    public function getTimezone();

    /**
     * @return string symbol between countdown date elements
     */
    public function getSeperator();

    /**
     * @return integer length of days countdown date element
     */
    public function getDaysLen();

    /**
     * @return string font file path
     */
    public function getFontFilePath();

    /**
     * @return integer size of font
     */
    public function getFontsize();

    /**
     * @return integer font start x
     */
    public function getFontx();

    /**
     * @return integer font start y
     */
    public function getFonty();

    /**
     * @return integer font red
     */
    public function getFontr();

    /**
     * @return integer font green
     */
    public function getFontg();

    /**
     * @return integer font blue
     */
    public function getFontb();

    /**
     * @return integer font angle
     */
    public function getFontangle();

    /**
     * @return string background image file path
     */
    public function getBackgroundImageFilePath();

}
