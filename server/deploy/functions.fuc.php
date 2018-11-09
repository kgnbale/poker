<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * functions
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/11
 */


/**
 * 自闭合html修复函数
 * 使用方法:
 * <code>
 * $input = '这是一段被截断的html文本<a href="#"';
 * echo Typecho_Common::fixHtml($input);
 * //output: 这是一段被截断的html文本
 * </code>
 *
 * @access public
 * @param string $string 需要修复处理的字符串
 * @return string
 */
function fixHtml($string) {
    //关闭自闭合标签
    $startPos = strrpos($string, "<");

    if (false == $startPos) {
        return $string;
    }

    $trimString = substr($string, $startPos);

    if (false === strpos($trimString, ">")) {
        $string = substr($string, 0, $startPos);
    }

    //非自闭合html标签列表
    preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
    preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

    if (!empty($startTags[1]) && is_array($startTags[1])) {
        krsort($startTags[1]);
        $closeTagsIsArray = is_array($closeTags[1]);
        foreach ($startTags[1] as $key => $tag) {
            $attrLength = strlen($startTags[2][$key]);
            if ($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1])) {
                continue;
            }
            if (!empty($closeTags[1]) && $closeTagsIsArray) {
                if (false !== ($index = array_search($tag, $closeTags[1]))) {
                    unset($closeTags[1][$index]);
                    continue;
                }
            }
            $string .= "</{$tag}>";
        }
    }

    return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string);
}

//生成友好时间形式
function dateword($from) {
    static $now = NULL;
    $now == NULL && $now = time();
    !is_numeric($from) && $from = strtotime($from);
    $seconds = $now - $from;
    $minutes = floor($seconds / 60);
    $hours = floor($seconds / 3600);
    $day = round((strtotime(date('Y-m-d', $now)) - strtotime(date('Y-m-d', $from))) / 86400);
    if ($seconds == 0) {
        return '刚刚';
    }
    if (($seconds >= 0) && ($seconds <= 60)) {
        return "{$seconds}秒前";
    }
    if (($minutes >= 0) && ($minutes <= 60)) {
        return "{$minutes}分钟前";
    }
    if (($hours >= 0) && ($hours <= 24)) {
        return "{$hours}小时前";
    }
    if ((date('Y') - date('Y', $from)) > 0) {
        return date('Y-m-d', $from);
    }

    switch ($day) {
        case 0:
            return date('今天H:i', $from);
            break;

        case 1:
            return date('昨天H:i', $from);
            break;

        default:
            //$day += 1;
            return "{$day} 天前";
            break;
    }
}

/**
 * 宽字符串截字函数
 *
 * @access public
 * @param string $str 需要截取的字符串
 * @param integer $start 开始截取的位置
 * @param integer $length 需要截取的长度
 * @param string $trim 截取后的截断标示符
 * @return string
 */
function xsubStr($str, $start, $length, $trim = "...") {
    if (!strlen($str)) {
        return '';
    }

    $iLength = strLen($str) - $start;
    $tLength = $length < $iLength ? ($length - strLen($trim)) : $length;

    if (function_exists('mb_get_info') && function_exists('mb_regex_encoding')) {
        $str = mb_substr($str, $start, $tLength, 'UTF-8');
    }
    else if (preg_match_all("/./u", $str, $matches)) {
        $str = implode('', array_slice($matches[0], $start, $tLength));
    }
    else {
        $str = xsubStr($str, $start, $tLength);
    }

    return $length < $iLength ? ($str . $trim) : $str;
}

/**
 * 获取gravatar头像地址
 *
 * @param string $mail
 * @param int $size
 * @param string $rating
 * @param string $default
 * @param bool $isSecure
 * @return string
 */
function gravatar($mail, $size=64) {
    $rating = "PG";
    $default = null;
    $isSecure = false;
    $url = $isSecure ? 'https://secure.gravatar.com' : 'http://www.gravatar.com';
    $url .= '/avatar/';

    if (!empty($mail)) {
        $url .= md5(strtolower(trim($mail)));
    }

    $url .= '?s=' . $size;
    $url .= '&amp;r=' . $rating;
    $url .= '&amp;d=' . $default;

    return $url;
}

function filter(&$text) {

    $badword = load('badword');//[];
    $badword1 = array_combine($badword,array_fill(0,count($badword),'*'));

    $text = strtr($text, $badword1);
}