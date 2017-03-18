<?php

/*
 * 扩展自smarty，避免直接修改smarty文件；
 * 其它文件均继承该文件;
 */
require(dirname(__FILE__).'/Smarty.class.php');
class SmartyConfig extends Smarty{
}