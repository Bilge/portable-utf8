<?php

use voku\helper\UTF8;

class UTF8Test extends PHPUnit_Framework_TestCase
{

  function testStrlen()
  {
    $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

    $this->assertEquals(70, strlen($string));
    $this->assertEquals(67, UTF8::strlen($string));

    $string_test1 = strip_tags($string);
    $string_test2 = UTF8::strip_tags($string);

    $this->assertEquals(53, strlen($string_test1));
    $this->assertEquals(50, UTF8::strlen($string_test2));
  }

  public function testIsAscii()
  {
    $testArray = array(
      'κ' => false,
      'abc' => true,
      'abcöäü' => false,
      '白' => false,
      '' => true
    );

    foreach ($testArray as $actual => $expected) {
      $this->assertEquals($expected, UTF8::is_ascii($actual));
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

  public function testCleanup()
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

  public function testChunkSplit()
  {
    $result = UTF8::chunk_split("ABC-ÖÄÜ-中文空白-κόσμε", 3);
    $expected = "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε";

    $this->assertEquals($expected, $result);
  }
}
