<?php namespace App\Services;


class ErrorHanlder {


    public static function install( ) {
        \set_error_handler([ self::class, 'customErrorHandler']);
    }

    public static function restore( ) {
        \restore_error_handler();
    }

    /**
     * Custom error handler to convert warnings and notices into ErrorExceptions.
     *
     * @param int    $severity The level of the error raised.
     * @param string $message  The error message.
     * @param string $file     The filename that the error was raised in.
     * @param int    $line     The line number the error was raised at.
     *
     * @return bool True if the error was handled, false otherwise.
     * @throws ErrorException When a non-fatal error occurs.
     */


    public static function customErrorHandler( $severity, $message, $file, $line) {
        // Only handle non-fatal errors (warnings, notices, etc.)
        if (!(error_reporting() & $severity)) {
            // This error code is not included in error_reporting
            return false;
        }

        throw new \ErrorException(esc_html($message), 500, $severity, $file, esc_html($line));
    }
}