<?php
/**
 * 8.12.2023
 * 16:28
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

function includeAllClassesInFolder($folderPath = 'Classes'): void
{
    $files = scandir($folderPath);

    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            require_once $folderPath . DIRECTORY_SEPARATOR . $file;
        }
    }
}