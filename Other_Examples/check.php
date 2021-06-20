<?php 

// Примеры использования разных функций 



$pattern ='/^[-a-zA-Z0-9_\x{30A0}-\x{30FF}'.'\x{3040}-\x{309F}\x{4E00}-\x{9FBF}\s]*$/u';
         
$match_string = '印刷最安 ニキビ跡除去 ゲームボーイ';

if (preg_match($pattern, $match_string)) {
    echo "Found - pattern $pattern";
} else {
    echo "Not found - pattern $pattern";
}

echo intval(42);                      // 42
echo intval(4.2);                     // 4
echo intval('42');                    // 42
echo intval('+42');                   // 42
echo intval('-42');                   // -42
echo intval(042);                     // 34 (octal as starts with zero)
echo intval('042');                   // 42
echo intval(1e10);                    // 1410065408
echo intval('1e10');                  // 1
echo intval(0x1A);                    // 26 (hex as starts with 0x)
echo intval(42000000);                // 42000000
echo intval(420000000000000000000);   // 0
echo intval('420000000000000000000'); // 2147483647
echo intval(42, 8);                   // 42
echo intval('42', 8);                 // 34
echo intval(array());                 // 0
echo intval(array('foo', 'bar'));     // 1