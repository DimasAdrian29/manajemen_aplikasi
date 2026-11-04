<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reminder Rapat
    |--------------------------------------------------------------------------
    |
    | Pengaturan pengiriman reminder rapat otomatis melalui Telegram. Reminder
    | dikirim ketika sisa waktu menuju rapat sudah masuk dalam rentang menit
    | yang ditentukan di bawah ini.
    |
    */

    'reminder' => [
        'menit_sebelum' => (int) env('RAPAT_REMINDER_MENIT', 60),
        'timezone' => env('RAPAT_TIMEZONE', 'Asia/Jakarta'),
    ],

];
