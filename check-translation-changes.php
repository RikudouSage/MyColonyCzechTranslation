<?php

$nl = PHP_EOL;
try {
    if (!isset($argv[1])) {
        throw new Exception("You must specify a version of the game!");
    }

    $stripJsCode = function (string $content): string {
        $content = str_replace([
            '})(window, document);',
            '(function(window, document){',
            'window.appStrings = '
        ], '', $content);
        $content = str_replace('};', '}', $content);
        $content = trim($content);

        return $content;
    };

    $version = trim($argv[1]);
    $url = sprintf("https://www.apewebapps.com/apps/my-colony/%s/strings.js", $version);

    $content = @json_decode($stripJsCode(file_get_contents($url)), true);
    if (json_last_error()) {
        throw new Exception("Could not parse the content as json, did the format of the file change?");
    }

    $newVersionEnglish = $content['en'];
    $currentVersionEnglish = @json_decode($stripJsCode(file_get_contents(__DIR__ . "/strings_original.js")), true)['en'];

    $newStrings = [];
    $updatedStrings = [];
    $deletedStrings = [];

    foreach ($newVersionEnglish as $key => $value) {
        if (!array_key_exists($key, $currentVersionEnglish)) {
            $newStrings[] = $key;
        } elseif ($value !== $currentVersionEnglish[$key]) {
            $updatedStrings[] = $key;
        }
    }

    foreach ($currentVersionEnglish as $key => $value) {
        if (!array_key_exists($key, $newVersionEnglish)) {
            $deletedStrings[] = $key;
        }
    }

    $report = "";

    if (count($newStrings)) {
        $report .= "New strings in version {$version}: {$nl}\t";
        $report .= implode("{$nl}\t", $newStrings);
        $report .= "{$nl}{$nl}";
    } else {
        $report .= "No new strings in version {$version}{$nl}";
    }

    if (count($updatedStrings)) {
        $report .= "Updated strings in version {$version}: {$nl}\t";
        $report .= implode("{$nl}\t", $updatedStrings);
        $report .= "{$nl}{$nl}";
    } else {
        $report .= "No updated strings in version {$version}{$nl}";
    }

    if (count($deletedStrings)) {
        $report .= "Deleted strings in version {$version}: {$nl}\t";
        $report .= implode("{$nl}\t", $deletedStrings);
        $report .= "{$nl}{$nl}";
    } else {
        $report .= "No deleted strings in version {$version}{$nl}";
    }

    file_put_contents(__DIR__ . "/report.txt", $report);
    echo "The report has been saved to report.txt", $nl;

} catch (Exception $e) {
    echo $e->getMessage(), $nl;
    exit(1);
}