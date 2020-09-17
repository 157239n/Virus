<?php

/**
 * These are functions that are so god damn simple that they don't warrant any OOP mechanisms and shall be loaded directly.
 */

namespace Kelvinho\Virus {

    /**
     * Map.
     *
     * @param array $list Initial list
     * @param callable $function Mapping function. The element, the index (or key), and extra data will be given to this function
     * @param null $data Extra data. Can be left out
     * @return array Mapped list
     */
    function map(array $list, callable $function, $data = null): array {
        $newList = [];
        foreach ($list as $key => $value) $newList[$key] = $function($value, $key, $data);
        return $newList;
    }

    /**
     * Filter. If the predicate returns true then the element makes it.
     *
     * @param array $list Initial list
     * @param callable $predicate Predicate function. The element, the key/index, and extra data will be given to this function
     * @param null $data Extra data. Can be left out
     * @param bool $ordered Whether to preserve old keys or throw them away. True if throw them away
     * @return array
     */
    function filter(array $list, callable $predicate, $data = null, bool $ordered = true): array {
        $newList = [];
        foreach ($list as $key => $value) {
            if ($predicate($value, $key, $data)) {
                if ($ordered) $newList[] = $value;
                else $newList[$key] = $value;
            }
        }
        return $newList;
    }

    /**
     * Format unix timestamp to a readable format. If no time is given, it will take the current time.
     *
     * @param int $time Optional unix timestamp
     * @return false|string The formatted time
     */
    function formattedTime(int $time = -1): string {
        if ($time == -1) $time = time();
        return date("Y/m/d h:i:sa", $time);
    }

    /**
     * Format a timespan in seconds to something like 1 hour 5 minutes 3 seconds
     *
     * @param int $seconds
     * @return string
     */
    function formattedTimeSpan(int $seconds): string {
        $answer = "";
        $intervals = [86400, 3600, 60, 1];
        $singularIntervals = ["day", "hour", "minute", "second"];
        $pluralIntervals = ["days", "hours", "minutes", "seconds"];
        for ($i = 0; $i < count($intervals); $i++) {
            $wholeAmount = intdiv($seconds, $intervals[$i]);
            $answer .= ($wholeAmount > 0) ? ($wholeAmount . " " . ($wholeAmount > 1 ? $pluralIntervals[$i] : $singularIntervals[$i]) . " ") : "";
            $seconds = $seconds % $intervals[$i];
        }
        return trim($answer);
    }

    /**
     * Make a long-ass hash (SHA256) to something like abc123defG...
     *
     * @param string $hash
     * @return string
     */
    function formattedHash(string $hash): string {
        return substr($hash, 0, 10) . "...";
    }

    /**
     * Checks whether the path is good (aka free of any directory traversal)
     *
     * @param string $basePath
     * @param string $relativePath
     * @return string
     */
    function goodPath(string $basePath, string $relativePath): string {
        $realBase = realpath($basePath);
        $realUserPath = realpath($basePath . $relativePath);
        return ($realUserPath === false || strpos($realUserPath, $realBase) !== 0) ? "" : $realUserPath;
    }

    /**
     * Strips the protocol part (http://, https://, ftp://, ...) out of a url.
     *
     * @param string $url
     * @return string
     */
    function stripProtocol(string $url): string {
        $protocols = ["http://", "https://", "ftp://", "sftp://", "ssh://"];
        foreach ($protocols as $protocol) $url = str_replace($protocol, "", $url);
        return $url;
    }

    /**
     * Returns a nice looking file size
     *
     * @param int $bytes
     * @return string
     */
    function niceFileSize(int $bytes): string {
        $labels = ["TB", "GB", "MB", "KB", "bytes"];
        $amounts = [1000000000000, 1000000000, 1000000, 1000, 1];
        $index = 0;
        if ($bytes == 0) return "0 bytes";
        while (true) {
            if ($bytes >= $amounts[$index]) return (int)($bytes / $amounts[$index] * 100) / 100 . " " . $labels[$index];
            $index += 1;
        }
    }

    /**
     * Returns a nice looking cost
     *
     * @param $cents
     * @return string
     */
    function niceCost(float $cents): string {
        $cents = (int)$cents;
        return $cents / 100;
    }

    /**
     * Turns an array's values to a set of ones.
     *
     * @param array $array
     * @return array
     */
    function set(array $array): array {
        $answer = [];
        foreach ($array as $value) $answer[$value] = 1;
        return $answer;
    }
}
