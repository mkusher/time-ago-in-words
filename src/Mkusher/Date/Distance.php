<?php
/**
 * This file is part of the sm package.
 *
 * (c) Aleh Kashnikau <aleh.kashnikau@gmail.com>
 *
 * Created: 18/02/2014 9:49 PM
 */

namespace MKusher\Date;

use \DateTime;
use \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;

class Distance {

    /**
     * @return int
     */
    public function getSeconds()
    {
        if(empty($this->distance['seconds']))
            $this->distance['seconds'] = round(abs($this->getTo() - $this->getFrom()));
        return $this->distance['seconds'];
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        if(empty($this->distance['minutes']))
            $this->distance['minutes'] = round(abs($this->getTo() - $this->getFrom())/60);
        return $this->distance['minutes'];
    }

    /**
     * @return int
     */
    public function getHours()
    {
        if(empty($this->distance['hours']))
            $this->distance['hours'] = round(abs($this->getTo() - $this->getFrom())/3600);
        return $this->distance['hours'];
    }

    /**
     * @return bool
     */
    public function isToday()
    {
        if(empty($this->distance['is_today'])){
            $today = self::transformTimestamp( DateTime::createFromFormat('d.m.YHis', date('d.m.Y')."000000") );
            $this->distance['is_today'] = $this->getFrom() >= $today;
        }
        return $this->distance['is_today'];
    }

    /**
     * @return bool
     */
    public function isYesterday()
    {
        if(empty($this->distance['is_yesterday'])){
            $yersterday = self::transformTimestamp( DateTime::createFromFormat('d.m.YHis', date('d.m.Y')."000000") ) - 3600*24;
            $this->distance['is_yesterday'] = $this->getFrom() - $yersterday >= 0;
        }
        return $this->distance['is_yesterday'];
    }

    public function __construct($from, $to = null)
    {
        $this->setFrom($from);
        $this->setTo($to);
        $this->distance = array();
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param DateTime|int $from timestamp
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = self::transformTimestamp($from);
        $this->distance = array();
        return $this;
    }

    /**
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param DateTime|int|null $to timestamp
     * @return $this
     */
    public function setTo($to=null)
    {
        $to = empty($to) ? new \DateTime('now') : $to;
        $this->to = self::transformTimestamp($to);
        $this->distance = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param $timestamp
     * @return DateTime|int
     */
    public static function transformTimestamp($timestamp)
    {
        $datetime_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer(null, null, 'Y-m-d H:i:s');
        $timestamp_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer();
        # Transforming to Timestamp
        if (!($timestamp instanceof DateTime) && !is_numeric($timestamp)) {
            $timestamp = $datetime_transformer->reverseTransform($timestamp);
            $timestamp = $timestamp_transformer->transform($timestamp);
        } elseif($timestamp instanceof DateTime) {
            $timestamp = $timestamp_transformer->transform($timestamp);
        }
        return $timestamp;
    }

    /**
     * @var DateTime
     */
    private $from;

    /**
     * @var DateTime
     */
    private $to;

    /**
     * @var array
     */
    private $distance;
} 