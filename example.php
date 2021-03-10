<?php

namespace Testing;

require __DIR__.'/vendor/autoload.php';

use Kafene\Json\Json;

run_tests([
    ['Encoded data', function ()
    {
        $data = get_json_data();
        $encoder = Json::encode($data);
        $json = $encoder->get();
        test($json === strval($encoder));
        return $json;
    }],
    ['Encoded object', function ()
    {
        $object = create_json_serializable_object();
        $encoder = Json::encode($object);
        $json = $encoder->get();
        test($json === strval($encoder));
        return $json;
    }],
    ['Encoded object using readable()', function ()
    {
        $object = create_json_serializable_object();
        $encoder = Json::encode($object)->readable();
        $json = $encoder->get();
        test($json === (string) $encoder);
        return $json;
    }],
    ['Encoded object using readable() + forceObject()', function ()
    {
        $object = create_json_serializable_object();
        $encoder = Json::encode($object)->readable()->forceObject();
        $json = $encoder->get();
        test($json === (string) $encoder);
        return $json;
    }],
    ['Decoded object', function ()
    {
        $object = create_json_serializable_object();
        $json = Json::encode($object)->readable()->forceObject();
        $decoded = Json::decode($json)->get();
        return $decoded;
    }],
    ['Encoded null', function ()
    {
        $encodedNull = Json::encode(null)->get();
        test($encodedNull === 'null');
        return $encodedNull;
    }],
    ['Encoded number', function ()
    {
        $number = mt_rand(1, 10000);
        $encodedNumber = Json::encode($number)->get();
        test($encodedNumber === (string) $number);
        return $encodedNumber;
    }],
    ['Error during encoding', function ()
    {
        $unencodable = ['I can not be encoded'];
        $unencodable[] = 'Because I contain a circular reference';
        $unencodable[] = &$unencodable;
        $caught = catch_exception(function () use (&$unencodable) {
            $result = Json::encode(['thing' => $unencodable])->get();
            var_dump($result);die;
            test(false, 'this line should never be reached');
        });
        test($caught instanceof \JsonException);
        return [
            'class'   => get_class($caught),
            'message' => $caught->getMessage(),
            'code'    => $caught->getCode(),
        ];
    }],
    ['Defer input - encode', function ()
    {
        $encoder = Json::encode();
        test($encoder->get() === 'null');
        $encoder = $encoder->withInput([55]);
        test($encoder->get() === '[55]');
        test($encoder->forceObject()->get() === '{"0":55}');
        return $encoder->forceObject(false)->get();
    }],
    ['Defer input - decode', function ()
    {
        $originalDecoder = Json::decode('"a string"');
        $decoder = $originalDecoder->withInput('{"0":55}');
        test($originalDecoder !== $decoder, 'withInput() produces a clone');
        test($decoder->assoc()->get() === [55]);
        test($decoder->assoc(false)->get() instanceof \stdClass);
        /* Flags and options like assoc() are retained between calls. */
        test($decoder->get() instanceof \stdClass);
        test($decoder->get()->{'0'} === 55);
        $decoder->assoc(true);
        test(is_array($decoder->get()));
        test($decoder->get() === [55]);
        return $decoder->get();
    }],
    ['Error during decoding - defer input', function ()
    {
        $decoder = Json::decode('500');
        $undecodable = 'this is not valid json';
        $caught = catch_exception(function () use (&$decoder, &$undecodable) {
            $decoder->withInput($undecodable)->get();
            test(false, 'this line should never be reached');
        });
        test($caught instanceof \JsonException);
        return [
            'class'   => get_class($caught),
            'message' => $caught->getMessage(),
            'code'    => $caught->getCode(),
        ];
    }],
    ['Checking for flags', function ()
    {
        $encoder = Json::encode([])->numericCheck();
        test($encoder->hasFlag(JSON_NUMERIC_CHECK) === true);
        test($encoder->hasFlag(JSON_HEX_TAG) === false);
        return [
            'flags' => [
                'JSON_NUMERIC_CHECK' => $encoder->hasFlag(JSON_NUMERIC_CHECK),
                'JSON_HEX_TAG' => $encoder->hasFlag(JSON_HEX_TAG),
            ],
        ];
    }],
]);

// =============================================================================
// =============================================================================
// =============================================================================

function get_json_data() {
    return [
        'one'    => 1,
        'object' => (object) ['test' => [1, 2, 3]],
    ];
}

function create_json_serializable_object() {
    return new class implements \JsonSerializable {
        public function jsonSerialize() {
            return array_merge(get_json_data(), ['class' => static::class]);
        }
    };
}

function catch_exception(\Closure $fn) {
    $caught = null;
    try {
        $fn();
        \assert(false, 'this line should never be reached');
    } catch (\Throwable $e) {
        $caught = $e;
    }
    return $caught;
}

function test($assertion, string $description = '') {
    if (!$assertion) {
        $caller = debug_backtrace()[0];
        $msg = 'Assertion failed on line '.$caller['line'];
        $msg .= $description ? ' - '.$description : '';
        throw new \Exception($msg);
    }
}

function run_tests(array $tests) {
    $startTime = microtime(true);
    $separator = str_repeat('=', 80);
    $eol = eol();
    $error = false;
    echo isCli() ? '' : '<pre style="border:1px solid #ccc;padding:1em;">';
    foreach ($tests as list ($testName, $testFn)) {
        $code = get_function_code($testFn);
        try {
            $result = $testFn();
            $result = trim(print_r($result, true));
        } catch (\Throwable $e) {
            $result = bold(red('TEST ERROR: '.$e->getMessage()));
            $error = true;
        }
        echo $separator.$eol.$eol.bold($testName).$eol.$eol;
        echo bold(underline('Code:')).$eol.$code.$eol.$eol;
        echo bold(underline('Result:')).$eol.$result.$eol.$eol;
        if ($error) {
            break;
        }
    }
    echo $separator;
    echo isCli() ? $eol : '</pre>'.$eol;
}

function get_function_code(\Closure $fn) {
    static $sourceLines, $numLines;
    if (!isset($sourceLines)) {
        $sourceLines = file(__FILE__);
        $numLines = count($sourceLines);
    }
    $ref = new \ReflectionFunction($fn);
    $code = array_slice($sourceLines, $ref->getStartLine(), $ref->getEndLine() - $numLines);
    $code = 'function () '.trim(implode(PHP_EOL, $code));
    $code = preg_replace('{['.preg_quote(PHP_EOL).']{2,}}m', PHP_EOL.PHP_EOL, $code);
    $code = preg_replace('{^\s{4}}m', '', $code);
    return $code;
}

function bold(string $str) {
    return isCli() ? "\033[1m{$str}\033[0m" : "<b>{$str}</b>";
}

function underline(string $str) {
    return isCli() ? "\033[4m{$str}\033[0m" : "<u>{$str}</u>";
}

function red(string $str) {
    return isCli() ? "\033[31m{$str}\033[0m" : "<span style=\"color:#F00;\">{$str}</span>";
}

function eol() {
    return isCli() ? PHP_EOL : '<br />';
}

function isCli() {
    return PHP_SAPI === 'cli';
}
