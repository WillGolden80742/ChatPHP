<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $nickNameMap;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->nickNameMap = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $nickNameFrom = $data['nickNameFrom'];
        $this->nickNameMap[$nickNameFrom] = $from;

        if (isset($data['nickNameFrom']) && isset($data['nickNameTo']) && isset($data['message'])) {
            $nickNameTo = $data['nickNameTo'];
            $message = $data['message'];

            if (isset($this->nickNameMap[$nickNameTo])) {
                $client = $this->nickNameMap[$nickNameTo];
                $client->send(json_encode([
                    'from' => $nickNameFrom,
                    'message' => $message
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has been closed\n";

        // Auto-restart the server in case it stops unexpectedly
        $this->restartServer();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();

        // Auto-restart the server in case of error
        $this->restartServer();
    }

    protected function restartServer()
    {
        echo "Restarting the WebSocket server...\n";
        // Delay for a moment to ensure the previous instance has fully closed
        sleep(3);
        exec('php ' . __FILE__);
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080...\n";
$server->run();
