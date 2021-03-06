<?php

use voku\helper\UTF8;
use voku\helper\UTF8 as u;

/**
 * Class Utf8ToAsciiTest
 */
class Utf8ToAsciiTest extends \PHPUnit\Framework\TestCase
{
  public function test_utf8()
  {
    $str = 'testiñg';
    self::assertSame('testing', u::toAscii($str));
  }

  public function test_ascii()
  {
    $str = 'testing';
    self::assertSame('testing', u::toAscii($str));
  }

  public function test_invalid_char()
  {
    $str = "tes\xE9ting";
    self::assertSame('testing', u::toAscii($str));
  }

  public function test_empty_str()
  {
    $str = '';
    self::assertEmpty(u::toAscii($str));
  }

  public function test_nul_and_non_7_bit()
  {
    $str = "a\x00ñ\x00c";
    self::assertSame('anc', u::toAscii($str));
  }

  public function test_nul()
  {
    $str = "a\x00b\x00c";
    self::assertSame('abc', u::toAscii($str));
  }

  public function testToASCII()
  {
    $testsStrict = array();
    if (UTF8::intl_loaded() === true) {

      // ---

      $testString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-unicode-chart.txt');
      $resultString = UTF8::file_get_contents(__DIR__ . '/fixtures/sample-ascii-chart.txt');

      self::assertSame($resultString, UTF8::to_ascii($testString, '?', true));

      // ---

      $testsStrict = array(
          1                                          => '1',
          -1                                         => '-1',
          ' '                                        => ' ',
          ''                                         => '',
          'أبز'                                      => 'abz',
          "\xe2\x80\x99"                             => '\'',
          'Ɓtest'                                    => 'Btest',
          '  -ABC-中文空白-  '                           => '  -ABC-zhong wen kong bai-  ',
          "      - abc- \xc2\x87"                    => '      - abc- ++',
          'abc'                                      => 'abc',
          'deja vu'                                  => 'deja vu',
          'déjà vu'                                  => 'deja vu',
          'déjà σσς iıii'                            => 'deja sss iiii',
          "test\x80-\xBFöäü"                         => 'test-oau',
          'Internationalizaetion'                    => 'Internationalizaetion',
          "中 - &#20013; - %&? - \xc2\x80"            => 'zhong - &#20013; - %&? - EUR',
          'Un été brûlant sur la côte'               => 'Un ete brulant sur la cote',
          'Αυτή είναι μια δοκιμή'                    => 'Aute einai mia dokime',
          'أحبك'                                     => 'ahbk',
          'キャンパス'                                    => 'kyanhasu',
          'биологическом'                            => 'biologiceskom',
          '정, 병호'                                    => 'jeong, byeongho',
          'ますだ, よしひこ'                                => 'masuta, yoshihiko',
          'मोनिच'                                    => 'monica',
          'क्षȸ'                                     => 'kasadb',
          'أحبك 😀'                                  => 'ahbk ?',
          'ذرزسشصضطظعغػؼؽؾؿ 5.99€'                   => 'dhrzsshsdtz\'gh[?][?][?][?][?] 5.99EUR',
          'ذرزسشصضطظعغػؼؽؾؿ £5.99'                   => 'dhrzsshsdtz\'gh[?][?][?][?][?] PS5.99',
          '׆אבגדהוזחטיךכלםמן $5.99'                  => '[?]\'bgdhwzhtykklmmn $5.99',
          '日一国会人年大十二本中長出三同 ¥5990'                    => 'ri yi guo hui ren nian da shi er ben zhong zhang chu san tong Y=5990',
          '5.99€ 日一国会人年大十 $5.99'                     => '5.99EUR ri yi guo hui ren nian da shi $5.99',
          'בגדה@ضطظعغػ.com'                          => 'bgdh@dtz\'gh[?].com',
          '年大十@ضطظعغػ'                               => 'nian da shi@dtz\'gh[?]',
          'בגדה & 年大十'                               => 'bgdh & nian da shi',
          '国&ם at ضطظعغػ.הוז'                        => 'guo&m at dtz\'gh[?].hwz',
          'my username is @בגדה'                     => 'my username is @bgdh',
          'The review gave 5* to ظعغػ'               => 'The review gave 5* to z\'gh[?]',
          'use 年大十@ضطظعغػ.הוז to get a 10% discount' => 'use nian da shi@dtz\'gh[?].hwz to get a 10% discount',
          '日 = הط^2'                                 => 'ri = ht^2',
          'ךכלם 国会 غػؼؽ 9.81 m/s2'                   => 'kklm guo hui gh[?][?][?] 9.81 m/s2',
          'The #会 comment at @בגדה = 10% of *&*'     => 'The #hui comment at @bgdh = 10% of *&*',
          '∀ i ∈ ℕ'                                  => '[?] i [?] N',
          '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'                => '? ? ?  ? ? ? ahbk',
      );
    }

    $tests = array(
        1                               => '1',
        -1                              => '-1',
        ' '                             => ' ',
        ''                              => '',
        'أبز'                           => 'abz',
        "\xe2\x80\x99"                  => '\'',
        'Ɓtest'                         => 'Btest',
        '  -ABC-中文空白-  '                => '  -ABC-Zhong Wen Kong Bai -  ',
        "      - abc- \xc2\x87"         => '      - abc- ++',
        'abc'                           => 'abc',
        'deja vu'                       => 'deja vu',
        'déjà vu '                      => 'deja vu ',
        'déjà σσς iıii'                 => 'deja sss iiii',
        'κόσμε'                         => 'kosme',
        "test\x80-\xBFöäü"              => 'test-oau',
        'Internationalizaetion'         => 'Internationalizaetion',
        "中 - &#20013; - %&? - \xc2\x80" => 'Zhong  - &#20013; - %&? - EUR',
        'Un été brûlant sur la côte'    => 'Un ete brulant sur la cote',
        'Αυτή είναι μια δοκιμή'         => 'Aute einai mia dokime',
        'أحبك'                          => 'aHbk',
        'キャンパス'                         => 'kiyanpasu',
        'биологическом'                 => 'biologicheskom',
        '정, 병호'                         => 'jeong, byeongho',
        'ますだ, よしひこ'                     => 'masuda, yosihiko',
        'मोनिच'                         => 'monic',
        'क्षȸ'                          => 'kssdb',
        'أحبك 😀'                       => 'aHbk ?',
        '∀ i ∈ ℕ'                       => '[?] i [?] N',
        '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'     => '? ? ?  ? ? ? aHbk',
    );

    for ($i = 0; $i <= 2; $i++) { // keep this loop for simple performance tests
      foreach ($tests as $before => $after) {
        self::assertSame($after, UTF8::to_ascii($before), 'tested: ' . $before);
        self::assertSame($after, UTF8::str_transliterate($before), 'tested: ' . $before);
      }
    }

    foreach ($testsStrict as $before => $after) {
      self::assertSame($after, UTF8::to_ascii($before, '?', true), 'tested: ' . $before);
      self::assertSame($after, UTF8::toAscii($before, '?', true), 'tested: ' . $before);
      self::assertSame($after, UTF8::str_transliterate($before, '?', true), 'tested: ' . $before);
    }
  }
}
