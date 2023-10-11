<?php

namespace Backendprogramer\RedirectPanel\Traits;

trait WriteToHtaccessFile
{
    /**
     * Searching for a specific string and replacing it at the beginning, middle, or end of another string.
     *
     * @param string $string
     * @param string $search
     * @param string $replace
     * @param string $type
     * @return string
     */
    protected static function replaceString(string $string, string $search, string $replace, string $type)
    {
        switch ($type) {
            case 'first':
                if(str_starts_with($string, $search)) {
                    return $replace . substr($string, strlen($search));
                }
                break;
            case 'end':
                if(str_ends_with($string, $search)) {
                    return substr($string, 0, strlen($string) - strlen($search)).$replace;
                }
                break;
            case 'middle':
                return str_replace($search, $replace, $string);
        }
        return $string;
    }

    /**
     * Adding or removing a redirect to/from the .htaccess file.
     *
     * @param array $redirect
     * @param string $type
     * @param array $oldData
     * @return void
     */
    public static function writeNewLineToHtaccess(array $redirect, string $type = 'new', array $oldData = [])
    {
        $htaccessPath = public_path() . '/' . config('redirect-panel.htaccess', '.htaccess');

        // Check if the .htaccess file exists, and create it if it doesn't
        if (!file_exists($htaccessPath)) {
            file_put_contents($htaccessPath, '');
        }

        $lines = file($htaccessPath);
        $fromPath = self::replaceString($redirect['from_path'], '/', '', 'first');
        $toPath = self::replaceString($redirect['to_path'], '/', '/', 'first');
        $fromPath = self::replaceString($fromPath, '/*', '/?$(.*)', 'end');
        $fromPath = self::replaceString($fromPath, '*', '(.*)', 'end');
        $fromPath = self::replaceString($fromPath, '*/', '(.*)', 'first');
        $fromPath = self::replaceString($fromPath, '/*/', '/([^/]+)/', 'middle');
        $newLine = "RewriteRule ^" . $fromPath . "$ " . $toPath . " [R=" . $redirect['type'] . ",L]";
        $beginLine = "# REDIRECTS BEGIN";
        $endLine = "# REDIRECTS END";
        $inserted = false;
        foreach ($lines as $key => &$line) {
            if (trim($line) == $beginLine || $inserted) {
                switch ($type) {
                    case 'edit':
                        if(!$inserted) {
                            $oldData['from_path'] = self::replaceString($oldData['from_path'], '/', '', 'first');
                            $oldData['to_path'] = self::replaceString($oldData['to_path'], '/', '/', 'first');
                            $oldData['from_path'] = self::replaceString($oldData['from_path'], '/*', '/?$(.*)', 'end');
                            $oldData['from_path'] = self::replaceString($oldData['from_path'], '*', '(.*)', 'end');
                            $oldData['from_path'] = self::replaceString($oldData['from_path'], '*/', '(.*)', 'first');
                            $oldData['from_path'] = self::replaceString($oldData['from_path'], '/*/', '/([^/]+)/', 'middle');
                        }
                        $oldLine = "RewriteRule ^" .$oldData['from_path'] . "$ " . $oldData['to_path'] . " [R=" . $oldData['type'] . ",L]";
                        if(trim($line) == $oldLine) {
                            $line = $newLine . PHP_EOL;
                        }
                        break;
                    case 'delete':
                        if (str_contains($line, $newLine)) {
                            unset($lines[$key]);
                        }
                        break;
                    default:
                        if(!$inserted) {
                            $line .= $newLine . PHP_EOL;
                        }
                }
                $inserted = true;
            } elseif (trim($line) == $endLine) {
                $inserted = false;
            }
        }
        if (!$inserted) {
            // If the # REDIRECTS BEGIN and # REDIRECTS END lines are not found, add them
            $lines[] = PHP_EOL . $beginLine . PHP_EOL . $newLine . PHP_EOL . $endLine . PHP_EOL;
        }
        file_put_contents($htaccessPath, implode('', $lines));
    }
}
