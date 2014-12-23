<?php

use voku\helper\UTF8 as u;

class Utf8StrlenTest extends PHPUnit_Framework_TestCase
{
  public function test_utf8()
  {
    $str = 'Iñtërnâtiônàlizætiøn';
    $this->assertEquals(20, u::strlen($str));
  }

  public function test_utf8_invalid()
  {
    $str = "Iñtërnâtiôn\xE9àlizætiøn";
    $this->assertEquals(20, u::strlen($str));
  }

  public function test_ascii()
  {
    $str = 'ABC 123';
    $this->assertEquals(7, u::strlen($str));
  }

  public function test_empty_str()
  {
    $str = '';
    $this->assertEquals(0, u::strlen($str));
  }
}
