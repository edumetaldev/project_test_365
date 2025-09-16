<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class TestRedisPublish extends Command
{
    protected $signature = 'test:redis';
    protected $description = 'Publica un mensaje de prueba en Redis para el canal reservations';

    public function handle()
    {
        $payload = [
            'event' => 'reservation.test',
            'data'  => [
                'id' => rand(1, 1000),
                'status' => 'CONFIRMED',
                'msg' => 'Este es un mensaje de prueba desde Laravel 🚀'
            ]
        ];

        Redis::publish('reservations', json_encode($payload));

        $this->info('Mensaje publicado en Redis (canal: reservations)');
    }
}
