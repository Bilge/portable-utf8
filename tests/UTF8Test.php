<?php

use voku\helper\UTF8;

class UTF8Test extends PHPUnit_Framework_TestCase
{

  public function testStrlen()
  {
    $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

    $this->assertEquals(70, strlen($string));
    $this->assertEquals(67, UTF8::strlen($string));

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    $this->assertEquals(53, strlen($string_test1));
    $this->assertEquals(50, UTF8::strlen($string_test2));
  }

  public function testHtmlspecialchars()
  {
    $testArray = array(
        "<a href='κόσμε'>κόσμε</a>" => "&lt;a href='κόσμε'&gt;κόσμε&lt;/a&gt;",
        "<白>"                       => "&lt;白&gt;",
        "öäü"                       => "öäü",
        " "                         => " ",
        ""                          => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::htmlspecialchars($actual));
    }
  }

  public function testHtmlentities()
  {
    $testArray = array(
        "<白>" => "&lt;白&gt;",
        "öäü" => "&ouml;&auml;&uuml;",
        " "   => " ",
        ""    => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::htmlentities($actual));
    }
  }

  public function testFitsInside()
  {
    $testArray = array(
        'κόσμε'  => array(5 => true),
        'test'   => array(4 => true),
        ''       => array(0 => true),
        ' '      => array(0 => false),
        'abcöäü' => array(2 => false)
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $size => $expected) {
        $this->assertEquals($expected, UTF8::fits_inside($actual, $size), 'error by ' . $actual);
      }
    }
  }

  public function testIsUtf8()
  {
    $testArray = array(
        'κ'                => true,
        'abc'              => true,
        'abcöäü'           => true,
        '白'                => true,
        ''                 => true,
        ' '                => true,
        "\xf0\x28\x8c\x28" => false
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::is_utf8($actual), 'error by ' . $actual);
    }
  }

  public function testCountChars()
  {
    $testArray = array(
        'κaκbκc' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'κ' => 3
        ),
        'cba'    => array(
            'a' => 1,
            'b' => 1,
            'c' => 1
        ),
        'abcöäü' => array(
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'ä' => 1,
            'ö' => 1,
            'ü' => 1
        ),
        '白白'     => array('白' => 2),
        ''       => array()
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::count_chars($actual), 'error by ' . $actual);
    }
  }

  public function testStringHasBom()
  {
    $testArray = array(
        UTF8::bom() . 'κ'      => true,
        'abc'                  => false,
        UTF8::bom() . 'abcöäü' => true,
        '白'                    => false,
        UTF8::bom()            => true
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::string_has_bom($actual), 'error by ' . $actual);
    }
  }

  public function testStrrev()
  {
    $testArray = array(
        'κ-öäü'  => 'üäö-κ',
        'abc'    => 'cba',
        'abcöäü' => 'üäöcba',
        '-白-'    => '-白-',
        ''       => '',
        ' '      => ' '
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::strrev($actual), 'error by ' . $actual);
    }
  }

  public function testIsAscii()
  {
    $testArray = array(
        'κ'      => false,
        'abc'    => true,
        'abcöäü' => false,
        '白'      => false,
        ''       => true
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::is_ascii($actual), 'error by ' . $actual);
    }
  }

  public function testRemoveDuplicates()
  {
    $testArray = array(
        "öäü-κόσμεκόσμε-äöü"   => array(
            "öäü-κόσμε-äöü" => "κόσμε"
        ),
        "äöüäöüäöü-κόσμεκόσμε" => array(
            "äöü-κόσμε" => array(
                "äöü",
                "κόσμε"
            )
        )
    );

    foreach ($testArray as $actual => $data) {
      foreach ($data as $expected => $filter) {
        $this->assertEquals($expected, UTF8::remove_duplicates($actual, $filter));
      }
    }
  }

  public function testRange()
  {
    $expected = array(
        "κ",
        "ι",
        "θ",
        "η",
        "ζ"
    );

    $this->assertEquals($expected, UTF8::range("κ", "ζ"));
    $this->assertEquals(0, count(UTF8::range("κ", "")));

  }

  public function testHash()
  {
    $testArray = array(
        2,
        8,
        0,
        100,
        1234
    );

    foreach ($testArray as $testValue) {
      $this->assertEquals($testValue, UTF8::strlen(UTF8::hash($testValue)));
    }
  }

  public function testCallback()
  {
    $actual = UTF8::callback(
        array(
            'voku\helper\UTF8',
            'strtolower'
        ), "Κόσμε-ÖÄÜ"
    );
    $expected = array(
        "κ",
        "ό",
        "σ",
        "μ",
        "ε",
        "-",
        "ö",
        "ä",
        "ü"
    );
    $this->assertEquals($expected, $actual);
  }

  public function testAccess()
  {
    $testArray = array(
        ""          => array(1 => ""),
        "中文空白"      => array(2 => "空"),
        "中文空白-test" => array(3 => "白"),
    );

    foreach ($testArray as $actualString => $testDataArray) {
      foreach ($testDataArray as $stringPos => $expectedString) {
        $this->assertEquals($expectedString, UTF8::access($actualString, $stringPos));
      }
    }
  }

  public function testStrSort()
  {
    $tests = array(
        ""               => "",
        "  -ABC-中文空白-  " => "    ---ABC中文白空",
        "      - ÖÄÜ- "  => "        --ÄÖÜ",
        "öäü"            => "äöü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "空白文中CBA---    ",
        "      - ÖÄÜ- "  => "ÜÖÄ--        ",
        "öäü"            => "üöä"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, false, true));
    }

    $tests = array(
        "    "           => " ",
        "  -ABC-中文空白-  " => " -ABC中文白空",
        "      - ÖÄÜ- "  => " -ÄÖÜ",
        "öäü"            => "äöü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, true));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "空白文中CBA- ",
        "      - ÖÄÜ- "  => "ÜÖÄ- ",
        "öäü"            => "üöä"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::str_sort($before, true, true));
    }
  }

  public function testUrlSlug()
  {
    $tests = array(
        "  -ABC-中文空白-  " => "abc-中文空白",
        "      - ÖÄÜ- "  => "öäü",
        "öäü"            => "öäü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "abc",
        "      - ÖÄÜ- "  => "öäü",
        "  öäüabc"            => "öäüa"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before, 4));
    }

    $tests = array(
        "  -ABC-中文空白-  " => "abc",
        "      - ÖÄÜ- "  => "o-a-u",
        "öäü"            => "o-a-u"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::url_slug($before, -1, true));
    }
  }

  public function testString()
  {
    $this->assertEquals("", UTF8::string(array()));
    $this->assertEquals(
        "öäü", UTF8::string(
        array(
            246,
            228,
            252
        )
    )
    );
    $this->assertEquals(
        "ㅡㅡ", UTF8::string(
        array(
            12641,
            12641
        )
    )
    );
  }

  public function testStripTags()
  {
    $this->assertEquals("", UTF8::strip_tags(""));
    $this->assertEquals("中文空白 ", UTF8::strip_tags("<nav>中文空白 </nav>"));
    $this->assertEquals("wtf", UTF8::strip_tags("<ㅡㅡ></ㅡㅡ><div></div><input type='email' name='user[email]' /><a>wtf</a>"));
  }

  public function testStrPad()
  {
    $firstString = "Though wise men at their end know dark is right,\nBecause their words had forked no lightning they\n";
    $secondString = "Do not go gentle into that good night.";
    $expectedString = $firstString . $secondString;
    $actualString = UTF8::str_pad($firstString, UTF8::strlen($firstString) + UTF8::strlen($secondString), $secondString);

    $this->assertEquals($expectedString, $actualString);

    $this->assertEquals("中文空白______", UTF8::str_pad("中文空白", 10, "_", STR_PAD_RIGHT));
    $this->assertEquals("______中文空白", UTF8::str_pad("中文空白", 10, "_", STR_PAD_LEFT));
    $this->assertEquals("___中文空白___", UTF8::str_pad("中文空白", 10, "_", STR_PAD_BOTH));
  }

  /**
   * @dataProvider trimProvider
   */
  public function testTrim($input, $output)
  {
    $this->assertSame($output, UTF8::trim($input));
  }

  /**
   * @return array
   */
  public function trimProvider()
  {
    return array(
        array(
            '',
            '',
        ),
        array(
            '　中文空白　 ',
            '中文空白',
        ),
        array(
            'do not go gentle into that good night',
            'do not go gentle into that good night',
        ),
    );
  }

  public function testClean()
  {
    $examples = array(
      // Valid UTF-8
      "κόσμε"                    => array("κόσμε" => "κόσμε"),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"        => array("κόσμε-öäü" => "κόσμε-öäü"),
      // Valid ASCII
      "a"                        => array("a" => "a"),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"            => array("a-öäü" => "a-öäü"),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                 => array("ñ" => "ñ"),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                 => array("�(" => "("),
      // Invalid Sequence Identifier
      "\xa0\xa1"                 => array("��" => ""),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"             => array("₡" => "₡"),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"             => array("�(�" => "("),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"             => array("�(" => "("),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"         => array("𐌼" => "𐌼"),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"         => array("�(��" => "("),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"         => array("�(�" => "("),
      // Invalid 4 Octet Sequence (in 4th Octet)
      "\xf0\x28\x8c\x28"         => array("�(�(" => "(("),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"     => array("�" => ""),
      // Valid 6 Octet Sequence (but not Unicode!)
      "\xfc\xa1\xa1\xa1\xa1\xa1" => array("�" => ""),
    );

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        $this->assertEquals($after, UTF8::cleanup($testString));
      }
    }
  }

  public function testCleanup()
  {
    $examples = array(
      // Valid UTF-8 + UTF-8 NO-BREAK SPACE
      "κόσμε\xc2\xa0"                        => array("κόσμε" => "κόσμε "),
      // Valid UTF-8 + Invalied Chars
      "κόσμε\xa0\xa1-öäü"                    => array("κόσμε-öäü" => "κόσμε-öäü"),
      // Valid ASCII
      "a"                                    => array("a" => "a"),
      // Valid ASCII + Invalied Chars
      "a\xa0\xa1-öäü"                        => array("a-öäü" => "a-öäü"),
      // Valid 2 Octet Sequence
      "\xc3\xb1"                             => array("ñ" => "ñ"),
      // Invalid 2 Octet Sequence
      "\xc3\x28"                             => array("�(" => "("),
      // Invalid Sequence Identifier
      "\xa0\xa1"                             => array("��" => ""),
      // Valid 3 Octet Sequence
      "\xe2\x82\xa1"                         => array("₡" => "₡"),
      // Invalid 3 Octet Sequence (in 2nd Octet)
      "\xe2\x28\xa1"                         => array("�(�" => "("),
      // Invalid 3 Octet Sequence (in 3rd Octet)
      "\xe2\x82\x28"                         => array("�(" => "("),
      // Valid 4 Octet Sequence
      "\xf0\x90\x8c\xbc"                     => array("𐌼" => "𐌼"),
      // Invalid 4 Octet Sequence (in 2nd Octet)
      "\xf0\x28\x8c\xbc"                     => array("�(��" => "("),
      // Invalid 4 Octet Sequence (in 3rd Octet)
      "\xf0\x90\x28\xbc"                     => array("�(�" => "("),
      // Invalid 4 Octet Sequence (in 4th Octet)
      " \xf0\x28\x8c\x28"                    => array("�(�(" => " (("),
      // Valid 5 Octet Sequence (but not Unicode!)
      "\xf8\xa1\xa1\xa1\xa1"                 => array("�" => ""),
      // Valid 6 Octet Sequence (but not Unicode!) + UTF-8 EN SPACE
      "\xfc\xa1\xa1\xa1\xa1\xa1\xe2\x80\x82" => array("�" => " "),
    );

    foreach ($examples as $testString => $testResults) {
      foreach ($testResults as $before => $after) {
        $this->assertEquals($after, UTF8::cleanup($testString));
      }
    }

  }

  public function testWhitespace()
  {
    $whitespaces = UTF8::whitespace_table();
    foreach ($whitespaces as $whitespace) {
      $this->assertEquals(" ", UTF8::clean($whitespace, false, true));
    }
  }

  public function testLtrim()
  {
    $tests = array(
        "  -ABC-中文空白-  " => "-ABC-中文空白-  ",
        "      - ÖÄÜ- "  => "- ÖÄÜ- ",
        "öäü"            => "öäü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::ltrim($before));
    }
  }

  public function testRtrim()
  {
    $tests = array(
        "-ABC-中文空白-  "        => "-ABC-中文空白-",
        "- ÖÄÜ-             " => "- ÖÄÜ-",
        "öäü"                 => "öäü"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::rtrim($before));
    }
  }

  public function testStrtolower()
  {
    $tests = array(
        "ABC-中文空白"    => "abc-中文空白",
        "ÖÄÜ"         => "öäü",
        "öäü"         => "öäü",
        "κόσμε"       => "κόσμε",
        "Κόσμε"       => "κόσμε",
        "ㅋㅋ-Lol"      => "ㅋㅋ-lol",
        "ㅎㄹ..-Daebak" => "ㅎㄹ..-daebak",
        "ㅈㅅ-Sorry"    => "ㅈㅅ-sorry",
        "ㅡㅡ-WTF"      => "ㅡㅡ-wtf"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::strtolower($before));
    }
  }

  public function testStrtoupper()
  {
    $tests = array(
        "abc-中文空白"     => "ABC-中文空白",
        "öäü"          => "ÖÄÜ",
        "öäü test öäü" => "ÖÄÜ TEST ÖÄÜ",
        "ÖÄÜ"          => "ÖÄÜ",
        "中文空白"         => "中文空白"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::strtoupper($before));
    }
  }

  public function testMin()
  {
    $tests = array(
        "abc-中文空白"     => "-",
        "öäü"          => "ä",
        "öäü test öäü" => " ",
        "ÖÄÜ"          => 'Ä',
        "中文空白"         => "中"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::min($before));
    }
  }

  public function testMax()
  {
    $tests = array(
        "abc-中文空白"     => "空",
        "öäü"          => "ü",
        "öäü test öäü" => "ü",
        "ÖÄÜ"          => 'Ü',
        "中文空白"         => "空"
    );

    foreach ($tests as $before => $after) {
      $this->assertEquals($after, UTF8::max($before));
    }
  }

  public function testUcfirst()
  {
    $this->assertEquals("Öäü", UTF8::ucfirst("Öäü"));
    $this->assertEquals("Öäü", UTF8::ucfirst("öäü"));
    $this->assertEquals("Κόσμε", UTF8::ucfirst("κόσμε"));
    $this->assertEquals("ABC-ÖÄÜ-中文空白", UTF8::ucfirst("aBC-ÖÄÜ-中文空白"));
  }

  public function testLcfirst()
  {
    $this->assertEquals("öäü", UTF8::lcfirst("Öäü"));
    $this->assertEquals("κόσμε", UTF8::lcfirst("Κόσμε"));
    $this->assertEquals("aBC-ÖÄÜ-中文空白", UTF8::lcfirst("ABC-ÖÄÜ-中文空白"));
  }

  public function testStrirpos()
  {
    $this->assertEquals(6, UTF8::strripos("κόσμε-κόσμε", "Κ"));
    $this->assertEquals(11, UTF8::strripos("test κόσμε κόσμε test", "Κ"));
    $this->assertEquals(7, UTF8::strripos("中文空白-ÖÄÜ-中文空白", "ü"));
  }

  public function testStrrpos()
  {
    $this->assertEquals(6, UTF8::strrpos("κόσμε-κόσμε", "κ"));
    $this->assertEquals(13, UTF8::strrpos("test κόσμε κόσμε test", "σ"));
    $this->assertEquals(9, UTF8::strrpos("中文空白-ÖÄÜ-中文空白", "中"));
  }

  public function testStrpos()
  {
    $this->assertEquals(0, UTF8::strpos("κόσμε-κόσμε-κόσμε", "κ"));
    $this->assertEquals(7, UTF8::strpos("test κόσμε test κόσμε", "σ"));
    $this->assertEquals(8, UTF8::strpos("ABC-ÖÄÜ-中文空白-中文空白", "中"));
  }

  public function testStripos()
  {
    $this->assertEquals(4, UTF8::stripos("öäü-κόσμε-κόσμε-κόσμε", "Κ"));
    $this->assertEquals(5, UTF8::stripos("Test κόσμε test κόσμε", "Κ"));
    $this->assertEquals(4, UTF8::stripos("ABC-ÖÄÜ-中文空白-中文空白", "ö"));
  }

  public function testOrd()
  {
    $testArray = array(
        "中" => "20013",
        "κ" => "954",
        "ö" => "246",
        "{" => "123",
        " " => "32",
        ""  => "0",
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::ord($actual));
    }
  }

  public function testHtmlEncode()
  {
    $testArray = array(
        "{-test" => "&#123;&#45;&#116;&#101;&#115;&#116;",
        "中文空白"   => "&#20013;&#25991;&#31354;&#30333;",
        "κόσμε"  => "&#954;&#8057;&#963;&#956;&#949;",
        "öäü"    => "&#246;&#228;&#252;",
        " "      => "&#32;",
        ""       => "",
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::html_encode($actual));
    }
  }

  public function testSingleChrHtmlEncode()
  {
    $testArray = array(
        "{" => "&#123;",
        "中" => "&#20013;",
        "κ" => "&#954;",
        "ö" => "&#246;",
        ""  => ""
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::single_chr_html_encode($actual));
    }
  }

  public function testChrSizeList()
  {
    $testArray = array(
        "中文空白"      => array(
            3,
            3,
            3,
            3
        ),
        "öäü"       => array(
            2,
            2,
            2
        ),
        "abc"       => array(
            1,
            1,
            1
        ),
        ""          => array(),
        "中文空白-test" => array(
            3,
            3,
            3,
            3,
            1,
            1,
            1,
            1,
            1
        ),
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::chr_size_list($actual));
    }
  }

  public function testWordCount()
  {
    $testArray = array(
        "中文空白"        => 1,
        "öäü öäü öäü" => 3,
        "abc"         => 1,
        ""            => 0,
        " "           => 0
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::word_count($actual));
    }
  }

  public function testMaxChrWidth()
  {
    $testArray = array(
        "中文空白" => 3,
        "öäü"  => 2,
        "abc"  => 1,
        ""     => 0
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::max_chr_width($actual));
    }
  }

  public function testSplit()
  {
    $this->assertEquals(
        array(
            "中",
            "文",
            "空",
            "白"
        ), UTF8::split("中文空白")
    );
    $this->assertEquals(
        array(
            "中文",
            "空白"
        ), UTF8::split("中文空白", 2)
    );
    $this->assertEquals(array("中文空白"), UTF8::split("中文空白", 4));
    $this->assertEquals(array("中文空白"), UTF8::split("中文空白", 8));
  }

  public function testChunkSplit()
  {
    $result = UTF8::chunk_split("ABC-ÖÄÜ-中文空白-κόσμε", 3);
    $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

    $this->assertEquals($expected, $result);
  }
}
